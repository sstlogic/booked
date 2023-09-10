<?php
/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class ResourceTypesResponse extends RestResponse
{
    public $types = array();

    /**
     * @param IRestServer $server
     * @param ResourceType[] $types
     */
    public function __construct(IRestServer $server, $types)
    {
        foreach($types as $type)
        {
            $this->AddType($type->Id(), apidecode($type->Name()), $type->Description());
        }
    }

    protected function AddType($id, $name, $description)
    {
        $this->types[] = ['id' => $id, 'name' => $name, 'description' => $description];
    }

    public static function Example()
    {
        return new ExampleResourceTypesResponse();
    }
}

class ExampleResourceTypesResponse extends ResourceTypesResponse
{
    public function __construct()
    {
        $this->AddType(1, 'name', 'description');
    }
}
