<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once (ROOT_DIR . 'Domain/Values/CreditLogView.php');
require_once (ROOT_DIR . 'Domain/Access/PageableDataStore.php');

interface ICreditRepository
{
    /**
     * @param int $pageNumber
     * @param int $pageSize
     * @param int $userId
     * @param string $sortField
     * @param string $sortDirection
     * @param ISqlFilter $filter
     * @return PageableData|CreditLogView[]
     */
    public function GetList($pageNumber, $pageSize, $userId = -1, $sortField = null, $sortDirection = null, $filter = null);
}

class CreditRepository implements ICreditRepository
{
    public function GetList($pageNumber, $pageSize, $userId = -1, $sortField = null, $sortDirection = null, $filter = null)
    {
        $command = new GetAllCreditLogsCommand($userId);

        if ($filter != null)
        {
            $command = new FilterCommand($command, $filter);
        }

        $builder = array('CreditLogView', 'Populate');
        return PageableDataStore::GetList($command, $builder, $pageNumber, $pageSize, $sortField, $sortDirection);
    }
}