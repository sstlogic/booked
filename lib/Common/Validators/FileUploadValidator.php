<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

class FileUploadValidator extends ValidatorBase implements IValidator
{
    /**
     * @var null|UploadedFile
     */
    private $file;

    /**
     * @param UploadedFile|null $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function Validate()
    {
        if ($this->file == null) {
            return;
        }
        $this->isValid = !$this->file->IsError();
        if (!$this->IsValid()) {
            Log::Debug('Uploaded file is not valid.', ['fileName' => $this->file->OriginalName(), 'error' => $this->file->Error()]);
            $this->AddMessage($this->file->Error());
        }
    }
}
