<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/MonitorView.php');

interface IMonitorViewRepository {
    /**
     * @param MonitorView $view
     * @return MonitorView
     */
    public function Add(MonitorView $view);

    /**
     * @param MonitorView $view
     * @return MonitorView
     */
    public function Update(MonitorView $view);

    /**
     * @param string $id
     * @return MonitorView|null
     */
    public function LoadByPublicId($id);

    public function DeleteByPublicId($id);

    /**
     * @return MonitorView[]
     */
    public function GetAll();
}

class MonitorViewRepository implements IMonitorViewRepository {

    public function Add(MonitorView $view)
    {
        $id = ServiceLocator::GetDatabase()->ExecuteInsert(new AddMonitorViewCommand($view->Name(), $view->PublicId(), $view->SeralizedSettings()));
        $view->WithId($id);
        return $view;
    }

    public function Update(MonitorView $view)
    {
        ServiceLocator::GetDatabase()->Execute(new UpdateMonitorViewCommand($view->Name(), $view->PublicId(), $view->SeralizedSettings()));
    }

    public function DeleteByPublicId($id)
    {
        ServiceLocator::GetDatabase()->Execute(new DeleteMonitorViewCommand($id));
    }

    public function GetAll() {
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllMonitorViewsCommand());
        $rows = [];
        while ($row = $reader->GetRow()) {
            $rows[] = MonitorView::FromRow($row);
        }

        return $rows;
    }

    public function LoadByPublicId($id) {
        $reader = ServiceLocator::GetDatabase()->Query(new GetMonitorViewByPublicIdCommand($id));
        if ($row = $reader->GetRow()) {
            return MonitorView::FromRow($row);
        }

        return null;
    }
}