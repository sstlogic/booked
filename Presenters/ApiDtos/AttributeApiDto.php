<?php
/**
 * Copyright 2021-2023 Twinkle Toes Software, LLC
 */

class AttributeApiDto
{
	public $id;
	public $label;
	public $type;
	public $category;
	public $regex;
	public $required;
	public $entityIds = [];
	public $adminOnly = false;
	public $possibleValues = [];
	public $sortOrder;
	public $secondaryCategory;
	public $secondaryEntityIds = [];
	public $isPrivate = false;

	/**
	 * @param CustomAttribute[] $attributes
	 * @return AttributeApiDto[]
	 */
	public static function FromList(array $attributes): array
	{
		$dtos = [];
		foreach ($attributes as $attribute)
		{
			$dtos[] = self::FromAttribute($attribute);
		}
		return $dtos;
	}

	public static function FromAttribute(CustomAttribute $attribute): AttributeApiDto
	{
		$dto = new AttributeApiDto();
		$dto->id = intval($attribute->Id());
		$dto->label = apidecode($attribute->Label());
		$dto->entityIds = array_map('intval', $attribute->EntityIds());
		$dto->type = intval($attribute->Type());
		$dto->category = intval($attribute->Category());
		$dto->regex = apidecode($attribute->Regex());
		$dto->required = intval($attribute->Required()) == 1;
		$dto->adminOnly = intval($attribute->AdminOnly()) == 1;
		$dto->possibleValues = apidecode($attribute->PossibleValueList());
		$dto->sortOrder = intval($attribute->SortOrder());
		$dto->secondaryCategory = empty($attribute->SecondaryCategory()) ? null : intval($attribute->SecondaryCategory());
		$dto->secondaryEntityIds = array_map('intval', $attribute->SecondaryEntityIds());
		$dto->isPrivate = intval($attribute->IsPrivate()) == 1;
		return $dto;
	}
}