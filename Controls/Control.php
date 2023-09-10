<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

abstract class Control
{
	/**
	 * @$var SmartyPage|Smarty
	 */
	protected $smarty = null;

	/**
	 * @var Smarty_Data
	 */
	protected $data = null;

    private $id;

	/**
	 * @param SmartyPage|Smarty $smarty
	 */
	public function __construct(SmartyPage $smarty)
	{
		$this->smarty = $smarty;
		$this->id = uniqid();

		$this->data = $smarty->createData();
        $userSession = ServiceLocator::GetServer()->GetUserSession();
        $this->Set('Timezone', $userSession->Timezone);
        $this->Set('ScriptUrl', Configuration::Instance()->GetScriptUrl());
        $this->Set('Path', '');

    }

	public function Set($var, $value)
	{
		$this->data->assign($var, $value);
	}

	protected function Get($var)
	{
		return $this->data->getTemplateVars($var);
	}

	protected function Display($templateName)
	{
		$tpl = $this->smarty->createTemplate($templateName, $this->data);
		$tpl->display();
	}

	public abstract function PageLoad();
}