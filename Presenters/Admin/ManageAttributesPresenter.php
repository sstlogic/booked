<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/Access/namespace.php');
require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');
require_once(ROOT_DIR . 'Presenters/ApiDtos/namespace.php');

class ManageAttributesPresenter extends ActionPresenter
{
    /**
     * @var IManageAttributesPage
     */
    private $page;
    /**
     * @var IAttributeRepository
     */
    private $attributeRepository;
    /**
     * @var IUserViewRepository
     */
    private $userViewRepository;
    /**
     * @var IResourceRepository
     */
    private $resourceRepository;

    public function __construct(IManageAttributesPage $page, IAttributeRepository $attributeRepository, IUserViewRepository $userViewRepository, IResourceRepository $resourceRepository)
    {
        parent::__construct($page);

        $this->page = $page;
        $this->attributeRepository = $attributeRepository;
        $this->userViewRepository = $userViewRepository;
        $this->resourceRepository = $resourceRepository;

        $this->AddApi('load', 'ApiLoad');
        $this->AddApi('add', 'ApiAdd');
        $this->AddApi('update', 'ApiUpdate');
        $this->AddApi('delete', 'ApiDelete');
    }

    public function ApiLoad()
    {
        $session = ServiceLocator::GetServer()->GetUserSession();
        $attributes = $this->attributeRepository->GetAll();
        $users = $this->userViewRepository->GetAll();
        $resources = $this->resourceRepository->GetResourceList();
        $types = $this->resourceRepository->GetResourceTypes();

        return new ApiActionResult(true, [
                'attributes' => AttributeApiDto::FromList($attributes),
                'users' => UserApiDto::FromList($users, [], false, true, false, $session->UserId),
                'resources' => ResourceApiDto::FromList($resources, null),
                'types' => ResourceTypeApiDto::FromList($types),
            ]
        );
    }

    public function ApiAdd($json): ApiActionResult
    {
        /** @var AttributeApiDto $dto */
        $dto = $json;
        $attributeName = trim($dto->label);
        $type = intval($dto->type);
        $scope = intval($dto->category);
        $regex = trim($dto->regex);
        $required = BooleanConverter::ConvertValue($dto->required);
        $possibleValues = $this->GetPossibleValues($dto->possibleValues);
        $sortOrder = intval($dto->sortOrder);
        $entityIds = !empty($dto->entityIds) ? array_map('intval', $dto->entityIds) : [];
        $adminOnly = BooleanConverter::ConvertValue($dto->adminOnly);

        Log::Debug('Adding new attribute', ['name' => $attributeName]);

        $attribute = CustomAttribute::Create($attributeName, $type, $scope, $regex, $required, $possibleValues, $sortOrder, $entityIds, $adminOnly);
        $secondaryEntities = !empty($dto->secondaryEntityIds) ? array_map('intval', $dto->secondaryEntityIds) : [];
        $attribute->WithSecondaryEntities($dto->secondaryCategory, $secondaryEntities);
        $attribute->WithIsPrivate($dto->isPrivate);

        $newId = $this->attributeRepository->Add($attribute);
        $added = $this->attributeRepository->LoadById($newId);
        return new ApiActionResult(true, AttributeApiDto::FromAttribute($added));
    }

    public function ApiUpdate($json): ApiActionResult
    {
        /** @var AttributeApiDto $dto */
        $dto = $json;
        $id = intval($dto->id);

        $attribute = $this->attributeRepository->LoadById($id);
        if (empty($attribute)) {
            return new ApiActionResult(false, null, new ApiErrorList(['Not found']));
        }

        $attributeName = trim($dto->label);
        $regex = trim($dto->regex);
        $required = BooleanConverter::ConvertValue($dto->required);
        $possibleValues = $this->GetPossibleValues($dto->possibleValues);
        $sortOrder = intval($dto->sortOrder);
        $entityIds = !empty($dto->entityIds) ? array_map('intval', $dto->entityIds) : [];
        $adminOnly = BooleanConverter::ConvertValue($dto->adminOnly);

        Log::Debug('Updating attribute.', ['id' => $id]);

        $attribute->Update($attributeName, $regex, $required, $possibleValues, $sortOrder, $entityIds, $adminOnly);
        $secondaryEntities = !empty($dto->secondaryEntityIds) ? array_map('intval', $dto->secondaryEntityIds) : [];
        $attribute->WithSecondaryEntities($dto->secondaryCategory, $secondaryEntities);
        $attribute->WithIsPrivate($dto->isPrivate);

        $this->attributeRepository->Update($attribute);

        $updated = $this->attributeRepository->LoadById($id);
        return new ApiActionResult(true, AttributeApiDto::FromAttribute($updated));
    }

    public function ApiDelete($json): ApiActionResult
    {
        $id = intval($json->id);

        Log::Debug('Deleting attribute', ['id' => $id]);
        $this->attributeRepository->DeleteById($id);

        return new ApiActionResult(true, ["id" => $id]);
    }

    private function GetPossibleValues($possibleValues)
    {
        if (empty($possibleValues)) {
            return [];
        }

        $scrubbed = [];
        foreach ($possibleValues as $v) {
            $scrubbed[] = str_replace(",", "", trim($v));
        }
        return $scrubbed;
    }
}