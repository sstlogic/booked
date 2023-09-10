<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

require_once(ROOT_DIR . 'lib/external/SimpleImage/SimpleImage.php');

interface IImageFactory
{
	/**
	 * @return IImage
	 */
	function Load($pathToImage);
}

class ImageFactory implements IImageFactory
{
	public function Load($pathToImage)
	{
		if (!extension_loaded('gd'))
		{
			die('gd extension is required for image upload');
		}

		$image = new SimpleImage();
      	$image->load($pathToImage);

      	return new Image($image);
	}
}
