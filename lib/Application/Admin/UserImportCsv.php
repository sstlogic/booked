<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

class UserImportCsvRow
{
    public $username;
    public $email;
    public $firstName = 'imported';
    public $lastName = 'imported';
    public $password;
    public $phone;
    public $phoneCountryCode;
    public $organization;
    public $position;
    public $timezone;
    public $language;
    public $groups = array();
    public $attributes = array();
    public $status = 1;
    public $credits;
    public $color;

    private $values = array();
    private $indexes = array();

    /**
     * @param $values array
     * @param $indexes array
     * @param $attributes CustomAttribute[]
     */
    public function __construct($values, $indexes, $attributes)
    {
        $this->values = $values;
        $this->indexes = $indexes;

        $this->username = $this->valueOrDefault('username');
        $this->email = $this->valueOrDefault('email');
        $this->firstName = $this->valueOrDefault('firstName');
        $this->lastName = $this->valueOrDefault('lastName');
        $this->password = $this->valueOrDefault('password');
        $this->phone = $this->valueOrDefault('phone');
        $this->phoneCountryCode = $this->valueOrDefault('phoneCountryCode');
        $this->organization = $this->valueOrDefault('organization');
        $this->position = $this->valueOrDefault('position');
        $this->timezone = $this->valueOrDefault('timezone');
        $this->language = $this->valueOrDefault('language');
        $this->status = $this->valueOrDefault('status');
        $this->credits = $this->valueOrDefault('credits');
        $this->color = $this->valueOrDefault('color');
        $this->groups = (!array_key_exists('groups', $this->indexes) || $indexes['groups'] === false) ? array() : array_map('trim', explode(',', htmlspecialchars($values[$indexes['groups']])));
        foreach ($attributes as $label => $attribute) {
            $this->attributes[$label] = $this->valueOrDefault($label);
        }
    }

    public function IsValid()
    {
        $isValid = !empty($this->username) && !empty($this->email);
        if (!$isValid) {
            Log::Debug('User import row is not valid.', ['username' => $this->username, 'email' => $this->email]);
        }
        return $isValid;
    }

    /**
     * @param string[] $values
     * @param CustomAttribute[] $attributes
     * @return bool|string[]
     */
    public static function GetHeaders($values, $attributes)
    {
        $values = array_map('trim', array_map('strtolower', $values));

        if (!in_array('email', $values) && !in_array('username', $values)) {
            return false;
        }

        $indexes['email'] = self::indexOrFalse('email', $values);
        $indexes['username'] = self::indexOrFalse('username', $values);
        $indexes['firstName'] = self::indexOrFalse('first name', $values);
        $indexes['lastName'] = self::indexOrFalse('last name', $values);
        $indexes['password'] = self::indexOrFalse('password', $values);
        $indexes['phoneCountryCode'] = self::indexOrFalse('phone country', $values);
        $indexes['phone'] = self::indexOrFalse('phone', $values);
        $indexes['organization'] = self::indexOrFalse('organization', $values);
        $indexes['position'] = self::indexOrFalse('position', $values);
        $indexes['timezone'] = self::indexOrFalse('timezone', $values);
        $indexes['language'] = self::indexOrFalse('language', $values);
        $indexes['groups'] = self::indexOrFalse('groups', $values);
        $indexes['status'] = self::indexOrFalse('status', $values);
        $indexes['credits'] = self::indexOrFalse('credits', $values);
        $indexes['color'] = self::indexOrFalse('color', $values);

        foreach ($attributes as $label => $attribute) {
            $label = strtolower($label);
            $escapedLabel = str_replace('\'', '\\\\', $label);
            $indexes[$label] = self::indexOrFalse($escapedLabel, $values);
        }

        return $indexes;
    }

    private static function indexOrFalse($columnName, $values)
    {
        $index = array_search($columnName, $values);
        if ($index === false) {
            return false;
        }

        return intval($index);
    }

    /**
     * @param $column string
     * @return string
     */
    private function valueOrDefault($column)
    {
        return ($this->indexes[$column] === false || !array_key_exists($this->indexes[$column], $this->values)) ? '' : $this->tryToGetEscapedValue($this->values[$this->indexes[$column]]);
    }

    private function tryToGetEscapedValue($v)
    {
        $value = htmlspecialchars(trim($v));
        if (!$value) {
            // htmlspecialchars freaked out and couldnt encode
            return trim($v);
        }

        return $value;
    }
}

class UserImportCsv
{
    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * @var int[]
     */
    private $skippedRowNumbers = array();

    /**
     * @var CustomAttribute[]
     */
    private $attributes;

    /**
     * @param UploadedFile $file
     * @param CustomAttribute[] $attributes
     */
    public function __construct(UploadedFile $file, $attributes)
    {
        $this->file = $file;
        $this->attributes = $attributes;
    }

    /**
     * @return UserImportCsvRow[]
     */
    public function GetRows()
    {
        $rows = array();

        $contents = $this->file->Contents();

        $contents = $this->RemoveUTF8BOM($contents);
        $csvRows = preg_split('/\n|\r\n?/', $contents);

        if (count($csvRows) == 0) {
            Log::Debug('No rows in user import file');
            return $rows;
        }

        Log::Debug('Found rows in user import file', ['count' => count($csvRows)]);

        $headers = UserImportCsvRow::GetHeaders(str_getcsv($csvRows[0]), $this->attributes);

        if (!$headers) {
            Log::Debug('No headers in user import file');
            return $rows;
        }

        for ($i = 1; $i < count($csvRows); $i++) {
            $values = str_getcsv($csvRows[$i]);

            $row = new UserImportCsvRow($values, $headers, $this->attributes);

            if ($row->IsValid()) {
                $rows[] = $row;
            } else {
                Log::Error('Skipped import of user row', ['row' => $i, 'values' => $values]);
                $this->skippedRowNumbers[] = $i;
            }
        }

        return $rows;
    }

    /**
     * @return int[]
     */
    public function GetSkippedRowNumbers()
    {
        return $this->skippedRowNumbers;
    }

    private function RemoveUTF8BOM($text)
    {
        return str_replace("\xEF\xBB\xBF", '', $text);
    }
}