<?php
/**
Copyright 2013-2023 Twinkle Toes Software, LLC
*/

class CompositeResourceFilter implements IResourceFilter
{
	/**
	 * @var array|IResourceFilter[]
	 */
	private $filters = array();

	public function Add(IResourceFilter $filter)
	{
		$this->filters[] = $filter;
	}

	/**
	 * @param IResource $resource
	 * @return bool
	 */
	function ShouldInclude($resource)
	{
		foreach ($this->filters as $filter)
		{
			if (!$filter->ShouldInclude($resource))
			{
				return false;
			}
		}

		return true;
	}
}