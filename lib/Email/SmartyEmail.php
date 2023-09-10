<?php
/**
Copyright 2011-2023 Twinkle Toes Software, LLC
*/

if (!defined('SMARTY_DIR')) {
	define('SMARTY_DIR', ROOT_DIR . 'lib/external/Smarty/');
}

require_once(ROOT_DIR . 'lib/external/Smarty/Smarty.class.php');
require_once(ROOT_DIR . 'lib/Server/namespace.php');
require_once(ROOT_DIR . 'lib/Common/namespace.php');

class SmartyEmail extends Smarty
{
	/**
	 * @var Resources
	 */
	protected $Resources = null;

	public function __construct($languageCode = null)
	{
		$resources = Resources::GetInstance();
		if (!empty($languageCode))
		{
			$this->Resources->SetLanguage($languageCode);
		}

		$this->assign('Charset', $this->Resources->Charset);
		$this->assign('ScriptUrl', Configuration::Instance()->GetScriptUrl());

		$this->template_dir = ROOT_DIR . 'lang';
		$this->compile_dir = ROOT_DIR . 'tpl_c';
		$this->config_dir = ROOT_DIR . 'configs';
		$this->cache_dir = ROOT_DIR . 'cache';

		$cacheTemplates = Configuration::Instance()->GetKey(ConfigKeys::CACHE_TEMPLATES, new BooleanConverter());
		$this->compile_check = !$cacheTemplates;	// should be set to false in production
		$this->force_compile = !$cacheTemplates;	// should be set to false in production

		$this->RegisterFunctions();
	}

	protected function RegisterFunctions()
	{
		$this->registerPlugin('function', 'translate', [$this, 'SmartyTranslate']);
		$this->registerPlugin('function', 'formatdate', [$this, 'FormatDate']);
		$this->registerPlugin('function', 'html_link', [$this, 'PrintLink']);
		$this->registerPlugin('function', 'html_image', [$this, 'PrintImage']);
	}

	public function FetchTemplate($templateName)
	{
		$localizedTemplate = $this->Resources->CurrentLanguage . '/' . $templateName;
		if (file_exists($localizedTemplate))
		{
			return $this->fetch($localizedTemplate);
		}

		return "en_us/$templateName";
	}

	public function SmartyTranslate($params, &$smarty)
	{
		//TODO: make these more pluggable so theyre not copied
		if (!isset($params['args']))
		{
			return $this->Resources->GetString($params['key'], '');
		}
		return $this->Resources->GetString($params['key'], explode(',', $params['args']));
	}

	public function FormatDate($params, &$smarty)
	{
        if (!isset($params['date']) || empty($params['date'])) {
            return '';
        }

        $date = is_string($params['date']) ? Date::Parse($params['date']) : $params['date'];

        /** @var $date Date */
        $date = isset($params['timezone']) ? $date->ToTimezone($params['timezone']) : $date;

        if (isset($params['format'])) {
            return $date->Format($params['format']);
        }

        $key = 'general_date';
        if (isset($params['key'])) {
            $key = $params['key'];
        }
        $session = ServiceLocator::GetServer()->GetUserSession();
        $format = $this->Resources->GetDateFormat($key, $session->DateFormat, $session->TimeFormat);

        $formatted = $date->Format($format);

        if (strpos($format, 'l') !== false) {
            // correct english day name to translated day name
            $english_days = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
            $days = $this->Resources->GetDays('full');
            $formatted = str_replace($english_days[$date->Weekday()], $days[$date->Weekday()], $formatted);
        }
        return $formatted;
	}
}
