<?php

/**
 * Copyright 2017-2023 Twinkle Toes Software, LLC
 */

class FileExtensionValidator extends ValidatorBase implements IValidator
{
	/**
	 * @var string[]
	 */
	private $validExtensions;

	/**
	 * @var string
	 */
	private $fileExtension;

	/**
	 * @param $validExtensions string|string[]
	 * @param $file UploadedFile
	 */
	public function __construct($validExtensions, $file)
	{
		if (!is_array($validExtensions))
		{
			$validExtensions = array($validExtensions);
		}
		$this->validExtensions = $validExtensions;

		if ($file == null || !is_a($file, 'UploadedFile'))
		{
			$this->fileExtension = '';
		}
		else
		{
			$this->fileExtension = $file->Extension();
		}
	}

	/**
	 * @return void
	 */
	public function Validate()
	{
		$this->isValid = in_array($this->fileExtension, $this->validExtensions);
	}

	public function Messages()
	{
		return array(Resources::GetInstance()->GetString('InvalidAttachmentExtension', implode(',', $this->validExtensions)));
	}
}
