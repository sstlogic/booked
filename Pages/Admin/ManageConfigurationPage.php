<?php
/**
 * Copyright 2012-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'config/timezones.php');
require_once(ROOT_DIR . 'Pages/Admin/AdminPage.php');
require_once(ROOT_DIR . 'lib/Config/Configurator.php');
require_once(ROOT_DIR . 'Presenters/Admin/ManageConfigurationPresenter.php');

interface IManageConfigurationPage extends IActionPage
{
    /**
     * @param bool $isPageEnabled
     */
    public function SetIsPageEnabled($isPageEnabled);

    /**
     * @param bool $isFileWritable
     */
    public function SetIsConfigFileWritable($isFileWritable);

    /**
     * @return array|ConfigSetting[]
     */
    public function GetSubmittedSettings();

    /**
     * @param ConfigFileOption[] $configFiles
     */
    public function SetConfigFileOptions($configFiles);

    /**
     * @return string
     */
    public function GetConfigFileToEdit();

    /**
     * @param string $configFileName
     */
    public function SetSelectedConfigFile($configFileName);

    /**
     * @param string $scriptUrl
     * @param string $suggestedUrl
     */
    public function ShowScriptUrlWarning($scriptUrl, $suggestedUrl);

    /**
     * @param string[] $values
     */
    public function SetAuthenticationPluginValues($values);

    /**
     * @param string[] $values
     */
    public function SetAuthorizationPluginValues($values);

    /**
     * @param string[] $values
     */
    public function SetPermissionPluginValues($values);

    /**
     * @param string[] $values
     */
    public function SetPostRegistrationPluginValues($values);

    /**
     * @param string[] $values
     */
    public function SetPreReservationPluginValues($values);

    /**
     * @param string[] $values
     */
    public function SetPostReservationPluginValues($values);

    /**
     * @return int
     */
    public function GetHomePageId();

    /**
     * @param ConfigurationSetting[] $items
     */
    public function AddSettings(array $items);

    public function SetSms(bool $isEnabled, int $allowedMessagesPerMonth, int $sentMessagesThisMonth, int $remainingMessages);
}

class ManageConfigurationPage extends ActionPage implements IManageConfigurationPage
{
    /**
     * @var ManageConfigurationPresenter
     */
    private $presenter;

    public function __construct()
    {
        parent::__construct('ManageConfiguration', 1);
        $this->presenter = new ManageConfigurationPresenter($this, new Configurator());
    }

    public function ProcessAction()
    {
        $this->presenter->ProcessAction();
    }

    public function ProcessDataRequest($dataRequest)
    {
        // no-op
    }

    public function ProcessPageLoad()
    {
        $this->Set('IsConfigFileWritable', true);
        $this->Set('ShowScriptUrlWarning', false);

        $this->presenter->PageLoad();
        $this->Display('Admin/Configuration/manage_configuration.tpl');
    }

    public function SetIsPageEnabled($isPageEnabled)
    {
        $this->Set('IsPageEnabled', $isPageEnabled);
    }

    public function SetIsConfigFileWritable($isFileWritable)
    {
        $this->Set('IsConfigFileWritable', $isFileWritable);
    }

    public function GetSubmittedSettings()
    {
        $settings = $this->GetRawForm('setting');
        $submittedSettings = [];
        foreach ($settings as $key => $setting) {
            $key = trim($key);
            $setting = trim($setting);
            if (!empty($key)) {
                $submittedSettings[] = ConfigSetting::ParseForm($key, stripslashes($setting));
            }
        }

        return $submittedSettings;
    }

    public function SetConfigFileOptions($configFiles)
    {
        $this->Set('ConfigFiles', $configFiles);
    }

    public function GetConfigFileToEdit()
    {
        return $this->GetQuerystring(QueryStringKeys::CONFIG_FILE);
    }

    public function SetSelectedConfigFile($configFileName)
    {
        $this->Set('SelectedFile', $configFileName);
    }

    public function ShowScriptUrlWarning($currentScriptUrl, $suggestedScriptUrl)
    {
        $this->Set('CurrentScriptUrl', $currentScriptUrl);
        $this->Set('SuggestedScriptUrl', $suggestedScriptUrl);
        $this->Set('ShowScriptUrlWarning', true);
    }

    public function SetAuthenticationPluginValues($values)
    {
        $this->Set('AuthenticationPluginValues', $values);
    }

    public function SetAuthorizationPluginValues($values)
    {
        $this->Set('AuthorizationPluginValues', $values);
    }
    public function SetPermissionPluginValues($values)
    {
        $this->Set('PermissionPluginValues', $values);
    }
    public function SetPostRegistrationPluginValues($values)
    {
        $this->Set('PostRegistrationPluginValues', $values);
    }
    public function SetPreReservationPluginValues($values)
    {
        $this->Set('PreReservationPluginValues', $values);
    }
    public function SetPostReservationPluginValues($values)
    {
        $this->Set('PostReservationPluginValues', $values);
    }

    public function GetHomePageId()
    {
       return $this->GetForm('homepage_id');
    }

    public function AddSettings(array $items)
    {
        $this->Set('settings', $items);
    }

    public function SetSms(bool $isEnabled, int $allowedMessagesPerMonth, int $sentMessagesThisMonth, int $remainingMessages)
    {
        $this->Set('isSmsEnabled', $isEnabled);
        $this->Set('smsAllowedMessagesPerMonth', $allowedMessagesPerMonth);
        $this->Set('smsSentMessagesThisMonth', $sentMessagesThisMonth);
        $this->Set('smsRemainingMessages', $remainingMessages);
    }
}