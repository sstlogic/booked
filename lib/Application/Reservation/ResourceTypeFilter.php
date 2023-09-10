<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
*/

class ResourceTypeFilter implements IResourceFilter
{
	/**
	 * @var $resourcetypename
	 */
	private $resourcetypeids = array();
	
	public function __construct($resourcetypename)
	{
		$reader = ServiceLocator::GetDatabase()
				  ->Query(new GetResourceTypeByNameCommand($resourcetypename));
		
		while($row = $reader->GetRow())
		{
			$this->resourcetypeids[] = $row[ColumnNames::RESOURCE_TYPE_ID];
		}
		
		$reader->Free();
	}

	/**
	 * @param IResource $resource
	 * @return bool
	 */
	public function ShouldInclude($assignment)
	{
		return in_array( $assignment->GetResourceTypeId(), $this->resourcetypeids );
	}
}
