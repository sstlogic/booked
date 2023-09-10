<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'lib/WebService/namespace.php');

class ResourceItemResponse extends RestResponse
{
    public $id;
    public $name;
    public $type;
    public $groups;

    public function __construct(IRestServer $server, $id, $name)
    {
        $this->id = $id;
        $this->name = apidecode($name);

        /*
         * Unfortunately we have to get the full resource here to be able to get the
         * resource_type_id
         */
        $resourceRepository = new ResourceRepository();
        $resource = $resourceRepository->LoadById($id);

        if ($resource->HasResourceType()) {
            $this->type = apidecode($resourceRepository->LoadResourceType($resource->GetResourceTypeId())->Name());
        }

        /*
         * For every resource we want to see the full hierarchical path of groups it belongs to.
         * This will add an array containing first parent and consecutive ancestors.
         * This is added here so it is not necessary to retrieve this in separate queries for
         * every resource and group it's assigned to.
         */
        $this->groups = array();

        foreach ($resource->GetResourceGroupIds() as $resourceGroupId) {
            $this->groups[$resourceGroupId] = $this->BuildParentList($resourceGroupId);
        }

        $this->AddService($server, WebServices::GetResource, array(WebServiceParams::ResourceId => $id));
    }

    private function BuildParentList($resourceGroupId, &$parents = array())
    {
        $groupsReader = ServiceLocator::GetDatabase()->Query(new GetResourceGroupCommand($resourceGroupId));
        if ($group = $groupsReader->GetRow()) {
            $parents[$group[ColumnNames::RESOURCE_GROUP_ID]] = new ResourceGroup(
                $group[ColumnNames::RESOURCE_GROUP_ID],
                apidecode($group[ColumnNames::RESOURCE_GROUP_NAME]),
                $group[ColumnNames::RESOURCE_GROUP_PARENT_ID]);

            $this->BuildParentList($group[ColumnNames::RESOURCE_GROUP_PARENT_ID], $parents);
        }
        $groupsReader->Free();

        return $parents;
    }

    public static function Example()
    {
        return new ExampleResourceItemResponse();
    }
}

class ExampleResourceItemResponse extends ResourceItemResponse
{
    public function __construct()
    {
        $this->id = 123;
        $this->name = 'resource name';
    }
}
