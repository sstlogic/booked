<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
 */

require_once (ROOT_DIR . 'lib/Email/EmbeddedEmailImage.php');

abstract class EmailMessage implements IEmailMessage
{
	/**
	 * @var SmartyPage
	 */
	private $email;
	/**
	 * @var string|null
	 */
	private $attachmentContents;
	/**
	 * @var string|null
	 */
	private $attachmentFileName;
    /**
     * @var EmbeddedEmailImage[]
     */
    private $embeddedImages;

	protected function __construct($languageCode = null)
	{
	    $this->email = new SmartyPage($resources);
		$resources = Resources::GetInstance();
		if (!empty($languageCode))
		{
			$resources->SetLanguage($languageCode);
			$this->Set('CurrentLanguage', $languageCode);
		}

		$this->Set('ScriptUrl', Configuration::Instance()->GetScriptUrl());
		$this->Set('Charset', $resources->Charset);
        $this->Set('AppTitle', (empty($appTitle) ? 'Booked' : $appTitle));
        $this->Set('Title', (empty($appTitle) ? 'Booked' : $appTitle));
        $this->Set('Version', Configuration::VERSION);
        $this->Set('LogoUrl', 'booked.png');
        if (file_exists(ROOT_DIR . 'Web/img/custom-logo.png')) {
            $this->Set('LogoUrl', 'custom-logo.png');
        }
        if (file_exists(ROOT_DIR . 'Web/img/custom-logo.gif')) {
            $this->Set('LogoUrl', 'custom-logo.gif');
        }
        if (file_exists(ROOT_DIR . 'Web/img/custom-logo.jpg')) {
            $this->Set('LogoUrl', 'custom-logo.jpg');
        }
	}

	protected function Set($var, $value)
	{
		$this->email->assign($var, $value);
	}

	protected function FetchTemplate($templateName, $includeHeaders = true)
	{
		$header = $includeHeaders ? $this->email->fetch('Email/emailheader.tpl') : '';
		$body = $this->email->FetchLocalized($templateName);
		$footer = $includeHeaders ? $this->email->fetch('Email/emailfooter.tpl') : '';

		return $header . $body . $footer;
	}

	protected function Translate($key, $args = array())
	{
		if (!is_array($args))
		{
			$args = array($args);
		}
		return $this->email->SmartyTranslate(array('key' => $key, 'args' => implode(',', $args)), $this->email);
	}

	public function ReplyTo()
	{
		return $this->From();
	}

	public function From()
	{
		return new EmailAddress(Configuration::Instance()->GetAdminEmail(), Configuration::Instance()->GetKey(ConfigKeys::ADMIN_EMAIL_NAME));
	}

	public function CC()
	{
		return array();
	}

	public function BCC()
	{
		return array();
	}

	public function Charset()
	{
		return $this->email->getTemplateVars('Charset');
	}

	public function AddStringAttachment($contents, $fileName)
	{
		$this->attachmentContents = $contents;
		$this->attachmentFileName = $fileName;
	}

	public function HasStringAttachment()
	{
		return !empty($this->attachmentContents);
	}

	public function RemoveStringAttachment()
	{
		$this->attachmentContents = null;
		$this->attachmentFileName = null;
	}

	public function AttachmentContents()
	{
		return $this->attachmentContents;
	}

	public function AttachmentFileName()
	{
		return $this->attachmentFileName;
	}

    /**
     * @param string $contents
     * @param string $cid
     */
    public function AddEmbeddedImage($contents, $cid)
    {
        $this->embeddedImages[] = new EmbeddedEmailImage($contents, $cid);
    }

    public function EmbeddedImages()
    {
        return empty($this->embeddedImages) ? [] : $this->embeddedImages;
    }
}

