<?php
/**
Copyright 2018-2023 Twinkle Toes Software, LLC
 */

class SamlUser
{
	private $username;
	private $fname;
	private $lname;
	private $mail;
	private $phone;
	private $institution;
	private $title;
	private $groups = array();

	/**
	 * @param array of SAML user attributes
	 * @param SamlOptions $samlOptions
	 */
	public function __construct($saml_attributes, $samlOptions)
	{
		Log::Debug('Inside construct SamlUser');
		$options = $samlOptions->AdSamlOptions();
		if (count($options) > 0)
		{
			$this->username = $this->GetAttributeValue($saml_attributes, $options, "ssphp_username");
			$this->fname = $this->GetAttributeValue($saml_attributes, $options, "ssphp_firstname");
			$this->lname = $this->GetAttributeValue($saml_attributes, $options, "ssphp_lastname");
			$this->mail = $this->GetAttributeValue($saml_attributes, $options, "ssphp_email");
			$this->phone = $this->GetAttributeValue($saml_attributes, $options, "ssphp_phone");
			$this->institution = $this->GetAttributeValue($saml_attributes, $options, "ssphp_organization");
			$this->title = $this->GetAttributeValue($saml_attributes, $options, "ssphp_position");
			$this->groups = [];
			if ($samlOptions->SyncGroups())
			{
				$this->groups = $this->GetAttributeValue($saml_attributes, $options, "ssphp_groups", true);
			}
		}
	}

	public function GetUserName()
	{
		return $this->username;
	}

	public function GetFirstName()
	{
		return $this->fname;
	}

	public function GetLastName()
	{
		return $this->lname;
	}

	public function GetEmail()
	{
		return $this->mail;
	}

	public function GetPhone()
	{
		return $this->phone;
	}

	public function GetInstitution()
	{
		return $this->institution;
	}

	public function GetTitle()
	{
		return $this->title;
	}

    public function GetGroups()
    {
        return $this->groups;
    }

	/**
	 * @param $saml_attributes array
	 * @param $options array
	 * @param $key string
	 * @param $returnArray bool
	 * @return mixed
	 */
	private function GetAttributeValue($saml_attributes, $options, $key, $returnArray = false)
	{
		$attributeKeys = array_map('trim', explode(',', $options[$key]));
		foreach ($attributeKeys as $attributeKey)
		{
			if (array_key_exists($attributeKey, $saml_attributes))
			{
				if ($returnArray) {
					return $saml_attributes[$attributeKey];
				}
				return $saml_attributes[$attributeKey][0];
			}
		}
		if ($returnArray) {
			return array();
		}
		return '';
	}
}