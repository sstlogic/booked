<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/Admin/ManageAttributesPresenter.php');

interface IAttributeSaveController
{
	/**
	 * @param CustomAttributeRequest $request
	 * @param WebServiceUserSession $session
	 * @return AttributeControllerResult
	 */
	public function Create($request, $session);

	/**
	 * @param int $attributeId
	 * @param CustomAttributeRequest $request
	 * @param WebServiceUserSession $session
	 * @return AttributeControllerResult
	 */
	public function Update($attributeId, $request, $session);

	/**
	 * @param int $attributeId
	 * @param WebServiceUserSession $session
	 * @return AttributeControllerResult
	 */
	public function Delete($attributeId, $session);
}

class AttributeControllerResult
{
	private $attributeId;
	private $errors = array();

	public function __construct($attributeId, $errors = array())
	{
		$this->attributeId = $attributeId;
		$this->errors = $errors;
	}

	/**
	 * @return bool
	 */
	public function WasSuccessful()
	{
		return !empty($this->attributeId) && empty($this->errors);
	}

	/**
	 * @return int
	 */
	public function AttributeId()
	{
		return $this->attributeId;
	}

	/**
	 * @return string[]
	 */
	public function Errors()
	{
		return $this->errors;
	}
}

class AttributeSaveController implements IAttributeSaveController
{
	/**
	 * @var IAttributeRepository
	 */
	private $repository;

	public function __construct(IAttributeRepository $repository)
	{
		$this->repository = $repository;
	}

	/**
	 * @param CustomAttributeRequest $request
	 * @param WebServiceUserSession $session
	 * @return AttributeControllerResult
	 */
	public function Create($request, $session)
	{
		$errors = $this->ValidateRequest($request);
		if (!empty($errors))
		{
			return new AttributeControllerResult(null, $errors);
		}

		$attribute = CustomAttribute::Create(apiencode($request->label), intval($request->type), intval($request->categoryId), $request->regex . '', intval($request->required),
            apiencode($this->GetPossibleValues($request)), intval($request->sortOrder), array_map('intval', $request->appliesToIds), intval($request->adminOnly));

		$attribute->WithSecondaryEntities($request->secondaryCategoryId, $request->secondaryEntityIds);

		$attribute->WithIsPrivate($request->isPrivate);

		$attributeId = $this->repository->Add($attribute);

		return new AttributeControllerResult($attributeId, null);
	}

	/**
	 * @param int $attributeId
	 * @param CustomAttributeRequest $request
	 * @param WebServiceUserSession $session
	 * @return AttributeControllerResult
	 */
	public function Update($attributeId, $request, $session)
	{
		$errors = $this->ValidateRequest($request);
		if (empty($attributeId))
		{
			$errors[] = 'attributeId is required';
		}
		if (!empty($errors))
		{
			return new AttributeControllerResult(null, $errors);
		}

		$attribute = new CustomAttribute(intval($attributeId), apiencode($request->label), intval($request->type), intval($request->categoryId), $request->regex . '', intval($request->required),
            apiencode($this->GetPossibleValues($request)), intval($request->sortOrder), array_map('intval', $request->appliesToIds), intval($request->adminOnly));

        $attribute->WithSecondaryEntities($request->secondaryCategoryId, $request->secondaryEntityIds);
        $attribute->WithIsPrivate($request->isPrivate);

		$this->repository->Update($attribute);

		return new AttributeControllerResult($attributeId, null);
	}

	/**
	 * @param int $attributeId
	 * @param WebServiceUserSession $session
	 * @return AttributeControllerResult
	 */
	public function Delete($attributeId, $session)
	{
		$errors = empty($attributeId) ? array('attributeId is required') : array();
		if (!empty($errors))
		{
			return new AttributeControllerResult(null, $errors);
		}

		$this->repository->DeleteById($attributeId);

		return new AttributeControllerResult($attributeId);
	}

	/**
	 * @param CustomAttributeRequest $request
	 * @return array|string[]
	 */
	private function ValidateRequest($request)
	{
		$errors = array();

		if (empty($request->label))
		{
			$errors[] = 'label is required';
		}

		if ($request->type != CustomAttributeTypes::CHECKBOX &&
				$request->type != CustomAttributeTypes::MULTI_LINE_TEXTBOX &&
				$request->type != CustomAttributeTypes::SELECT_LIST &&
				$request->type != CustomAttributeTypes::SINGLE_LINE_TEXTBOX
		)
		{
			$errors[] = sprintf('type is invalid. Allowed values for type: %s (checkbox), %s (multi line), %s (select list), %s (single line)',
								CustomAttributeTypes::CHECKBOX,
								CustomAttributeTypes::MULTI_LINE_TEXTBOX,
								CustomAttributeTypes::SELECT_LIST,
								CustomAttributeTypes::SINGLE_LINE_TEXTBOX);
		}

		if ($request->categoryId != CustomAttributeCategory::RESERVATION &&
				$request->categoryId != CustomAttributeCategory::RESOURCE &&
				$request->categoryId != CustomAttributeCategory::RESOURCE_TYPE &&
				$request->categoryId != CustomAttributeCategory::USER
		)
		{
			$errors[] = sprintf('categoryId is invalid. Allowed values for category: %s (reservation), %s (resource), %s (resource type), %s (user)',
								CustomAttributeCategory::RESERVATION,
								CustomAttributeCategory::RESOURCE,
								CustomAttributeCategory::RESOURCE_TYPE,
								CustomAttributeCategory::USER);
		}

		if ($request->type == CustomAttributeTypes::SELECT_LIST && empty($request->possibleValues))
		{
			$errors[] = 'possibleValues is required when the type is a select list';
		}

		if ($request->type != CustomAttributeTypes::SELECT_LIST && !empty($request->possibleValues))
		{
			$errors[] = 'possibleValues is only valid when the type is a select list';
		}

		if ($request->categoryId == CustomAttributeCategory::RESERVATION && !empty($request->appliesToIds))
		{
			$errors[] = 'appliesToId is not valid when the type is reservation';
		}

		return $errors;
	}

	/**
	 * @param CustomAttributeRequest $request
	 * @return string
	 */
	private function GetPossibleValues($request)
	{
		if (empty($request->possibleValues))
		{
			return null;
		}

		if (!is_array($request->possibleValues))
		{
			return $request->possibleValues . '';
		}

		return implode(',', $request->possibleValues);
	}


}
