<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

class GroupItemResponse extends RestResponse
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $isDefault;

    public function __construct(IRestServer $server, GroupItemView $group)
    {
        $this->id = $group->Id();
        $this->name = apidecode($group->Name());
        $this->isDefault = (bool)$group->IsDefault();

        $this->AddService($server, WebServices::GetGroup, [WebServiceParams::GroupId => $group->Id()]);
    }

    public static function Example()
    {
        return new ExampleGroupItemResponse();
    }
}

class ExampleGroupItemResponse extends GroupItemResponse
{
    public function __construct()
    {
        $this->id = 1;
        $this->name = 'group name';
        $this->isDefault = true;
    }
}

