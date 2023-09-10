<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

interface IImage
{
	function ResizeToWidth($pixels);

	function Save($path);
}

class Image implements IImage
{
	/**
	 * @var SimpleImage
	 */
	private $image;

	public function __construct(SimpleImage $image)
	{
		$this->image = $image;
	}

	public function ResizeToWidth($pixels)
	{
		$this->image->resizeToWidth($pixels);
	}

	public function Save($path)
	{
		$this->image->save($path);
	}
}

