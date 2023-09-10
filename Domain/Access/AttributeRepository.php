<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Domain/CustomAttribute.php');

interface IAttributeRepository
{
	/**
	 * @param CustomAttribute $attribute
	 * @return int
	 */
	public function Add(CustomAttribute $attribute);

	/**
	 * @param $attributeId int
	 * @return CustomAttribute
	 */
	public function LoadById($attributeId);

	/**
	 * @param CustomAttribute $attribute
	 */
	public function Update(CustomAttribute $attribute);

	/**
	 * @param $attributeId int
	 * @return void
	 */
	public function DeleteById($attributeId);

	/**
	 * @param int|CustomAttributeCategory $category
	 * @return array|CustomAttribute[]
	 */
	public function GetByCategory($category);

    /**
	 * @return array|CustomAttribute[]
	 */
	public function GetAll();

	/**
	 * @param int|CustomAttributeCategory $category
	 * @param array|int[] $entityIds if null is passed, get all entity values
	 * @return array|AttributeEntityValue[]
	 */
	public function GetEntityValues($category, $entityIds = null);

}

class AttributeRepository implements IAttributeRepository
{
	/**
	 * @var DomainCache
	 */
	private $cache;

	public function __construct()
	{
		$this->cache = new DomainCache();
	}

	public function Add(CustomAttribute $attribute)
	{
		$id = ServiceLocator::GetDatabase()->ExecuteInsert(
				new AddAttributeCommand($attribute->Label(), $attribute->Type(), $attribute->Category(), $attribute->Regex(),
										$attribute->Required(), $attribute->PossibleValues(), $attribute->SortOrder(),
										$attribute->AdminOnly(), $attribute->SecondaryCategory(), $attribute->SecondaryEntityIds(),
										$attribute->IsPrivate()));

		foreach ($attribute->EntityIds() as $entityId)
		{
			ServiceLocator::GetDatabase()->ExecuteInsert(new AddAttributeEntityCommand($id, $entityId));
		}

		return $id;
	}

	public function GetByCategory($category)
	{
		if (!$this->cache->Exists($category))
		{
			$reader = ServiceLocator::GetDatabase()->Query(new GetAttributesByCategoryCommand($category));

			$attributes = array();
			while ($row = $reader->GetRow())
			{
				$attributes[] = CustomAttribute::FromRow($row);
			}

			$this->cache->Add($category, $attributes);
			$reader->Free();
		}

		return $this->cache->Get($category);
	}

    public function GetAll()
    {
        $reader = ServiceLocator::GetDatabase()->Query(new GetAllAttributesCommand());

        $attributes = array();
        while ($row = $reader->GetRow())
        {
            $attributes[] = CustomAttribute::FromRow($row);
        }

        $reader->Free();

        return $attributes;
    }

    public function LoadById($attributeId)
	{
		$reader = ServiceLocator::GetDatabase()->Query(new GetAttributeByIdCommand($attributeId));

		$attribute = null;
		if ($row = $reader->GetRow())
		{
			$attribute = CustomAttribute::FromRow($row);
		}

		$reader->Free();
		return $attribute;
	}

	public function Update(CustomAttribute $attribute)
	{
		$db = ServiceLocator::GetDatabase();
		$db->Execute(new UpdateAttributeCommand($attribute->Id(), $attribute->Label(), $attribute->Type(), $attribute->Category(),
												$attribute->Regex(), $attribute->Required(), $attribute->PossibleValues(), $attribute->SortOrder(),
												$attribute->AdminOnly(), $attribute->SecondaryCategory(),
												$attribute->SecondaryEntityIds(), $attribute->IsPrivate()));

		foreach ($attribute->RemovedEntityIds() as $entityId)
		{
			$db->Execute(new RemoveAttributeEntityCommand($attribute->Id(), $entityId));
		}

		foreach ($attribute->AddedEntityIds() as $entityId)
		{
			$db->Execute(new AddAttributeEntityCommand($attribute->Id(), $entityId));
		}
	}

	public function GetEntityValues($category, $entityIds = null)
	{
		$values = array();

		if (!is_array($entityIds) && !empty($entityIds))
		{
			$entityIds = array($entityIds);
		}

		if (empty($entityIds))
		{
            return [];
//			$reader = ServiceLocator::GetDatabase()->Query(new GetAttributeAllValuesCommand($category));
		}
		else
		{
			$reader = ServiceLocator::GetDatabase()->Query(new GetAttributeMultipleValuesCommand($category, $entityIds));
		}
		while ($row = $reader->GetRow())
		{
			$values[] = new AttributeEntityValue(
					$row[ColumnNames::ATTRIBUTE_ID],
					$row[ColumnNames::ATTRIBUTE_ENTITY_ID],
					$row[ColumnNames::ATTRIBUTE_VALUE]);
		}

		$reader->Free();
		return $values;
	}

	public function DeleteById($attributeId)
	{
		ServiceLocator::GetDatabase()->Execute(new DeleteAttributeCommand($attributeId));
		ServiceLocator::GetDatabase()->Execute(new DeleteAttributeValuesCommand($attributeId));
		ServiceLocator::GetDatabase()->Execute(new DeleteAttributeColorRulesCommand($attributeId));
	}
}