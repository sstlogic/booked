<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Pages/Page.php');

interface IPageable
{
	/**
	 * @abstract
	 * @return int
	 */
	function GetPageNumber();

	/**
	 * @abstract
	 * @return int
	 */
	function GetPageSize();

	/**
	 * @abstract
	 * @param PageInfo $pageInfo
	 * @return void
	 */
	function BindPageInfo(PageInfo $pageInfo);
}

class PageablePage extends Page implements IPageable
{
	/**
	 * @var \Page
	 */
	private $page;

	public function __construct(Page $wrappedPage)
	{
		$this->page = $wrappedPage;
	}

	/**
	 * @return int
	 */
	public function GetPageNumber()
	{
		return $this->page->GetQuerystring(QueryStringKeys::PAGE);
	}

	/**
	 * @return int
	 */
	public function GetPageSize()
	{
		$size = $this->page->GetQuerystring(QueryStringKeys::PAGE_SIZE);
		if (empty($size))
		{
			return 50;
		}
		return $size;
	}

	/**
	 * @param PageInfo $pageInfo
	 * @return void
	 */
	public function BindPageInfo(PageInfo $pageInfo)
	{
		$this->page->Set('PageInfo', $pageInfo);
	}

	public function PageLoad()
	{
		$this->page->PageLoad();
	}
}