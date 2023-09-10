<?php
/**
 * Copyright 2022-2023 Twinkle Toes Software, LLC
 */

class UserGroupFilter
{
    /**
     * @param string $entityTableAndColumn
     * @param int $groupId
     * @return ISqlFilter
     */
    public static function Create($entityTableAndColumn, $groupId)
    {
        if ($groupId == "-1") {
            $filter = new SqlFilterFreeForm($entityTableAndColumn . ' IN (SELECT `' . TableNames::USERS . '`.`' . ColumnNames::USER_ID . '` FROM `' . TableNames::USERS . '` LEFT JOIN `' . TableNames::USER_GROUPS . '` ON `' . TableNames::USER_GROUPS . '`.`' . ColumnNames::USER_ID . '` = `' . TableNames::USERS . '`.`' . ColumnNames::USER_ID . '` WHERE `' . TableNames::USER_GROUPS . '`.`' . ColumnNames::USER_ID . '` IS NULL)');
        } else {
            $filter = new SqlFilterFreeForm($entityTableAndColumn . ' IN (SELECT `' . ColumnNames::USER_ID . '` FROM `' . TableNames::USER_GROUPS . '` WHERE `' . TableNames::USER_GROUPS . '`.`' . ColumnNames::GROUP_ID . '` = @InGroupId)');
            $filter->AddCriteria('InGroupId', $groupId);
        }
        return $filter;
    }
}