<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class CustomAttributeTypes
{
    const SINGLE_LINE_TEXTBOX = 1;
    const MULTI_LINE_TEXTBOX = 2;
    const SELECT_LIST = 3;
    const CHECKBOX = 4;
    const DATETIME = 5;
    const MULTI_SELECT = 6;
    const LINK = 7;
}

class CustomAttributeCategory
{
    const RESERVATION = 1;
    const USER = 2;
    //const GROUP = 3;
    const RESOURCE = 4;
    const RESOURCE_TYPE = 5;
}

class CustomAttribute
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var CustomAttributeTypes|int
     */
    protected $type;

    /**
     * @var CustomAttributeCategory|int
     */
    protected $category;

    /**
     * @var string
     */
    protected $regex;

    /**
     * @var bool
     */
    protected $required;

    /**
     * @var int[]
     */
    protected $entityIds = array();

    /**
     * @var int[]
     */
    protected $addedEntityIds = array();

    /**
     * @var int[]
     */
    protected $removedEntityIds = array();

    /**
     * @var string[]
     */
    protected $entityDescriptions = array();

    /**
     * @var bool
     */
    protected $adminOnly = false;

    /**
     * @var string
     */
    protected $possibleValues;

    /**
     * @var int
     */
    protected $sortOrder;

    /**
     * @var CustomAttributeTypes|int
     */
    protected $secondaryCategory;

    /**
     * @var int[]
     */
    protected $secondaryEntityIds = array();

    /**
     * @var string[]
     */
    protected $secondaryEntityDescriptions = array();

    /**
     * @var bool
     */
    protected $isPrivate = false;

    /**
     * @return int
     */
    public function Id()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function Label()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function PossibleValues()
    {
        return $this->possibleValues;
    }

    /**
     * @return array|string[]
     */
    public function PossibleValueList()
    {
        if (empty($this->possibleValues)) {
            return [];
        }
        return explode(',', $this->possibleValues);
    }

    /**
     * @return string
     */
    public function Regex()
    {
        return $this->regex;
    }

    /**
     * @return bool
     */
    public function Required()
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function UniquePerEntity()
    {
        return !empty($this->entityIds);
    }

    /**
     * @return int[]
     */
    public function EntityIds()
    {
        return empty($this->entityIds) ? [] : $this->entityIds;
    }

    /**
     * @return int[]
     */
    public function AddedEntityIds()
    {
        return empty($this->addedEntityIds) ? [] : $this->addedEntityIds;
    }

    /**
     * @return int[]
     */
    public function RemovedEntityIds()
    {
        return empty($this->removedEntityIds) ? [] : $this->removedEntityIds;
    }

    /**
     * @return array|string[]
     */
    public function EntityDescriptions()
    {
        return empty($this->entityDescriptions) ? [] : $this->entityDescriptions;
    }

    /**
     * @return CustomAttributeCategory|int
     */
    public function Category()
    {
        return $this->category;
    }

    /**
     * @return CustomAttributeTypes|int
     */
    public function Type()
    {
        return $this->type;
    }

    public function HasSecondaryEntities()
    {
        return !empty($this->secondaryCategory) && !empty($this->secondaryEntityIds);
    }

    /**
     * @return CustomAttributeCategory|int|null
     */
    public function SecondaryCategory()
    {
        return $this->secondaryCategory;
    }

    /**
     * @return int[]
     */
    public function SecondaryEntityIds()
    {
        return empty($this->secondaryEntityIds) ? [] : $this->secondaryEntityIds;
    }

    /**
     * @return string[]
     */
    public function SecondaryEntityDescriptions()
    {
        return empty($this->secondaryEntityDescriptions) ? array() : $this->secondaryEntityDescriptions;
    }

    /**
     * @return int
     */
    public function SortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @return bool
     */
    public function AdminOnly()
    {
        return (int)$this->adminOnly;
    }

    /**
     * @return bool
     */
    public function IsPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * @param int|int[] $entityId
     * @return bool
     */
    public function AppliesToEntity($entityId)
    {
        if ($this->UniquePerEntity()) {
            if (is_array($entityId)) {
                foreach ($entityId as $id) {
                    if (in_array($id, $this->EntityIds())) {
                        return true;
                    }
                }

                return false;
            }
            return in_array($entityId, $this->EntityIds());
        }
        return true;
    }

    /**
     * @param int $id
     * @param string $label
     * @param CustomAttributeTypes|int $type
     * @param CustomAttributeCategory|int $category
     * @param string $regex
     * @param bool $required
     * @param string|string[] $possibleValues
     * @param int $sortOrder
     * @param int[] $entityIds
     * @param bool $adminOnly
     */
    public function __construct($id, $label, $type, $category, $regex, $required, $possibleValues, $sortOrder, $entityIds = [], $adminOnly = false)
    {
        $this->id = $id;
        $this->label = $label;
        $this->type = $type;
        $this->category = $category;
        $this->SetRegex($regex);
        $this->required = $required;
        if ($category != CustomAttributeCategory::RESERVATION) {
            $this->entityIds = is_array($entityIds) ? $entityIds : [$entityIds];
        }
        $this->adminOnly = $adminOnly;
        $this->SetSortOrder($sortOrder);
        $this->SetPossibleValues($possibleValues);
    }

    /**
     * @static
     * @param string $label
     * @param CustomAttributeTypes|int $type
     * @param CustomAttributeCategory|int $category
     * @param string $regex
     * @param bool $required
     * @param string|string[] $possibleValues
     * @param int $sortOrder
     * @param int[] $entityIds
     * @param bool $adminOnly
     * @return CustomAttribute
     */
    public static function Create($label, $type, $category, $regex, $required, $possibleValues, $sortOrder, $entityIds = [], $adminOnly = false)
    {
        return new CustomAttribute(null, $label, $type, $category, $regex, $required, $possibleValues, $sortOrder,
            $entityIds, $adminOnly);
    }

    /**
     * @static
     * @param $row array
     * @return CustomAttribute
     */
    public static function FromRow($row)
    {
        $entityIds = array();
        if (!empty($row[ColumnNames::ATTRIBUTE_ENTITY_IDS])) {
            $entityIds = explode('!sep!', $row[ColumnNames::ATTRIBUTE_ENTITY_IDS]);
        }

        $descriptions = array();
        if (!empty($row[ColumnNames::ATTRIBUTE_ENTITY_DESCRIPTIONS])) {
            $descriptions = explode('!sep!', $row[ColumnNames::ATTRIBUTE_ENTITY_DESCRIPTIONS]);
        }

        $attribute = new CustomAttribute(
            $row[ColumnNames::ATTRIBUTE_ID],
            $row[ColumnNames::ATTRIBUTE_LABEL],
            $row[ColumnNames::ATTRIBUTE_TYPE],
            $row[ColumnNames::ATTRIBUTE_CATEGORY],
            $row[ColumnNames::ATTRIBUTE_CONSTRAINT],
            $row[ColumnNames::ATTRIBUTE_REQUIRED],
            $row[ColumnNames::ATTRIBUTE_POSSIBLE_VALUES],
            $row[ColumnNames::ATTRIBUTE_SORT_ORDER],
            $entityIds,
            $row[ColumnNames::ATTRIBUTE_ADMIN_ONLY]
        );

        $attribute->WithEntityDescriptions($descriptions);

        if (isset($row[ColumnNames::ATTRIBUTE_SECONDARY_CATEGORY])) {
            $attribute->WithSecondaryEntities($row[ColumnNames::ATTRIBUTE_SECONDARY_CATEGORY],
                $row[ColumnNames::ATTRIBUTE_SECONDARY_ENTITY_IDS],
                $row[ColumnNames::ATTRIBUTE_SECONDARY_ENTITY_DESCRIPTIONS]);
        }

        if (isset($row[ColumnNames::ATTRIBUTE_IS_PRIVATE])) {
            $attribute->WithIsPrivate($row[ColumnNames::ATTRIBUTE_IS_PRIVATE]);
        }

        return $attribute;
    }

    /**
     * @param $value string|null
     * @return bool
     */
    public function SatisfiesRequired($value)
    {
        if (!$this->required) {
            return true;
        }

        $trimmed = trim($value . '');
        return !(empty($trimmed) && !is_numeric($trimmed));
    }

    /**
     * @param $value string|null
     * @return bool
     */
    public function SatisfiesConstraint($value)
    {
        if (!empty($this->regex)) {
            return preg_match($this->regex, $value . '') > 0;
        }

        if (!empty($this->possibleValues)) {
            if ($this->required && empty($value)) {
                return false;
            }

            if (empty($value)) {
                return true;
            }

            $list = $this->PossibleValueList();

            if ($this->type == CustomAttributeTypes::SELECT_LIST) {
                return in_array($value, $list);
            }

            if ($this->type == CustomAttributeTypes::MULTI_SELECT) {
                $values = explode(",", $value);

                return !array_diff($values, $list);
            }

            return false;
        }

        if ($this->Type() == CustomAttributeTypes::LINK && !empty($value)) {
            return preg_match("/[(http(s)?):\/\/(www\.)?a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)/", $value) > 0;
        }

        return true;
    }

    /**
     * @param string $label
     * @param string $regex
     * @param bool $required
     * @param string|string[] $possibleValues
     * @param int $sortOrder
     * @param int[] $entityIds
     * @param bool $adminOnly
     */
    public function Update($label, $regex, $required, $possibleValues, $sortOrder, $entityIds, $adminOnly)
    {
        $this->label = $label;
        $this->SetRegex($regex);
        $this->required = $required;

        if ($this->category != CustomAttributeCategory::RESERVATION) {

            $entityIds = is_array($entityIds) ? $entityIds : array($entityIds);
            $removed = array_diff($this->entityIds, $entityIds);
            $added = array_diff($entityIds, $this->entityIds);

            if (!empty($removed) || !empty($added)) {
                $this->removedEntityIds = $removed;
                $this->addedEntityIds = $added;
            }

            $this->entityIds = $entityIds;
        }

        $this->adminOnly = $adminOnly;
        $this->SetPossibleValues($possibleValues);
        $this->SetSortOrder($sortOrder);
    }

    /**
     * @param string|string[] $possibleValues
     */
    private function SetPossibleValues($possibleValues)
    {
        if (empty($possibleValues)) {
            $this->possibleValues = "";
            return;
        }

        if (!($this->type == CustomAttributeTypes::SELECT_LIST || $this->type == CustomAttributeTypes::MULTI_SELECT)) {
            $this->possibleValues = "";
            return;
        }

        if (is_array($possibleValues)) {
            $this->possibleValues = implode(",", array_filter(array_map('trim', $possibleValues), function($v) {return $v != "";}));
            return;
        }

        $this->possibleValues = preg_replace('/\s*,\s*/', ',', trim($possibleValues));
    }

    /**
     * @param int $sortOrder
     */
    private function SetSortOrder($sortOrder)
    {
        $this->sortOrder = intval($sortOrder);
    }

    /**
     * @param string[] $entityDescriptions
     */
    public function WithEntityDescriptions($entityDescriptions)
    {
        $this->entityDescriptions = $entityDescriptions;
    }

    /**
     * @param int|CustomAttributeCategory $category
     * @param string|int[] $entityIds
     * @param string|null $entityDescriptions
     */
    public function WithSecondaryEntities($category, $entityIds, $entityDescriptions = null)
    {
        if ($this->category != CustomAttributeCategory::RESERVATION) {
            return;
        }

        if (!empty($category) && !empty($entityIds)) {
            $this->secondaryCategory = $category;

            if (is_array($entityIds)) {
                $this->secondaryEntityIds = $entityIds;
            } else {
                $this->secondaryEntityIds = explode(',', $entityIds);
            }

            $descriptions = array();
            if (!empty($entityDescriptions)) {
                $descriptions = explode('!sep!', $entityDescriptions);
            }
            $this->secondaryEntityDescriptions = $descriptions;
        } else {
            $this->secondaryCategory = null;
            $this->secondaryEntityIds = null;
            $this->secondaryEntityDescriptions = null;
        }
    }

    /**
     * @param int|bool $isPrivate
     */
    public function WithIsPrivate($isPrivate)
    {
        $this->isPrivate = BooleanConverter::ConvertValue($isPrivate);
    }

    /**
     * @param string $regex
     */
    private function SetRegex($regex)
    {
        $this->regex = $regex;
        if (empty($this->regex)) {
            return;
        }

        if (!BookedStringHelper::StartsWith($this->regex, '/')) {
            $this->regex = '/' . $this->regex;
        }
        if (!BookedStringHelper::EndsWith($this->regex, '/')) {
            $this->regex = $this->regex . '/';
        }
    }
}