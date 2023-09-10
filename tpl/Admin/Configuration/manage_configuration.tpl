{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}

{include file='globalheader.tpl'}

{function name="list_settings"}
    {foreach from=$settings key=section item=s}
        <div class="config-section">
            <h2 id="section-{$section}" class="setting-section">{$section}</h2>
            {foreach from=$settings[$section] item=setting}
                {cycle values=',row1' assign=rowCss}
                {assign var="name" value=$setting->Name()}
                <div class="{$rowCss} mb-2 p-2">
                    <div>
                        {if $setting->Type() == "text"}
                            <div>
                                <label class="form-label" for="{$setting->Key}">{$setting->Label}</label>
                                <input class="form-control" id="{$setting->Key}" type="text"
                                       value="{$setting->Value}" name="{$setting->Name()}"/>
                            </div>
                        {/if}

                        {if $setting->Type() == "bool"}
                            <div class="form-label">{$setting->Label}</div>
                            <div class="form-check-inline">
                                <label class="form-check-label" for="{$setting->Key}_yes">Yes</label>
                                <input class="form-check-input" id="{$setting->Key}_yes" type="radio"
                                       {if $setting->Value == "true"}checked="checked"{/if}
                                       name="{$setting->Name()}" value="true"/>
                            </div>
                            <div class="form-check-inline">
                                <label class="form-check-label" for="{$setting->Key}_no">No</label>
                                <input class="form-check-input" id="{$setting->Key}_no" type="radio"
                                       {if $setting->Value == "false"}checked="checked"{/if}
                                       name="{$setting->Name()}" value="false"/>
                            </div>
                        {/if}

                        {if $setting->Type() == "select"}
                            <div>
                                <label class="form-label" for="{$setting->Key}">{$setting->Label}</label>
                                <select class="form-select" id="{$setting->Key}" name="{$setting->Name()}">
                                    {foreach from=$setting->Options item=o}
                                        <option value="{$o['value']}"
                                                {if $setting->Value == $o['value']}selected{/if}>{$o['text']}</option>
                                    {/foreach}
                                </select>
                            </div>
                        {/if}

                        {if $setting->Type() == "number"}
                            <div>
                                <label class="form-label" for="{$setting->Key}">{$setting->Label}</label>
                                <input class="form-control" id="{$setting->Key}" type="number"
                                       value="{$setting->Value}" name="{$setting->Name()}"/>
                            </div>
                        {/if}
                    </div>
                </div>
            {/foreach}
        </div>
    {/foreach}
{/function}

<div id="page-manage-configuration" class="admin-page">

    <div id="manage-configuration-header" class="admin-page-header">
        <div class="admin-page-header-title">
            <h1>{translate key=ManageConfiguration}</h1>
        </div>
        <div>
            <a href="https://www.bookedscheduler.com/help/configuration/" target="_blank" rel="noreferrer">{translate key=Help}</a>
        </div>
    </div>

    {if $ShowScriptUrlWarning}
        <div class="alert alert-danger">
            {translate key=ScriptUrlWarning args="$CurrentScriptUrl,$SuggestedScriptUrl"}
        </div>
    {/if}

    <div>
        <form id="frmConfigFile" method="GET" action="{$SCRIPT_NAME}">
            <div>
                <label class="form-label" for="cf">{translate key=File}</label>
                <select name="cf" id="cf" class="form-select">
                    {foreach from=$ConfigFiles item=file}
                        {assign var=selected value=""}
                        {if isset($SelectedFile) && $file->Location eq $SelectedFile}{assign var=selected value="selected='selected'"}{/if}
                        <option value="{$file->Location}" {$selected}>{$file->Name}</option>
                    {/foreach}
                </select>
            </div>
        </form>
    </div>


    {if !$IsPageEnabled}
        <div class="alert alert-danger">
            {translate key=ConfigurationUiNotEnabled}
        </div>
    {/if}

    {if !$IsConfigFileWritable}
        <div class="alert alert-danger">
            {translate key=ConfigurationFileNotWritable}
        </div>
    {/if}

    {if $IsPageEnabled && $IsConfigFileWritable}
        <div id="configSettings">

            <div class="settings-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <input type="button" value="{translate key=Update}" class='btn btn-success save'/>
                    </div>

                    <div class="d-flex">
                        <label for="jump-to-section">Jump to section</label>
                        <select id="jump-to-section" class="form-select">
                            {foreach from=$settings key=section item=s}
                                <option value="section-{$section}">{$section}</option>
                            {/foreach}
                            <option value="section-sms">sms</option>
                        </select>
                    </div>
                </div>

                <div id="updatedMessage" class="alert alert-success mt-2" style="display:none;">
                    {translate key=ConfigurationUpdated}
                </div>
            </div>


            <form id="frmConfigSettings" method="post" ajaxAction="{ConfigActions::Update}"
                  action="{$smarty.server.SCRIPT_NAME}">
                <div class="config-settings">
                    {list_settings}

                    <div class="config-section">
                        <h2 id="section-sms" class="setting-section">sms</h2>
                        <div class="mb-2 p-2">
                            {if $isSmsEnabled}
                                <div><strong>{translate key=SMSAllowedMessagesPerMonth}
                                        :</strong> {$smsAllowedMessagesPerMonth}</div>
                                <div><strong>{translate key=SMSSentThisMonth}:</strong> {$smsSentMessagesThisMonth}
                                    ({round($smsSentMessagesThisMonth/$smsAllowedMessagesPerMonth) * 100}%)
                                </div>
                                <div><strong>{translate key=SMSRemainingThisMonth}:</strong> {$smsRemainingMessages}
                                    ({round($smsRemainingMessages/$smsAllowedMessagesPerMonth) * 100}%)
                                </div>
                                <div>
                                    {translate key=SMSAdjustSettingContact} <a href="mailto:support@bookedscheduler.com">support@bookedscheduler.com</a>
                                </div>
                            {else}
                                <div>{translate key=SmsDisabled}
                                    <a href="https://www.bookedscheduler.com/sms"
                                       target="_blank" rel="noreferrer">{translate key=SmsMoreInfo} </a>
                                </div>
                            {/if}
                        </div>
                    </div>
                </div>
            </form>

        </div>
        <form id="updateHomepageForm"
              method="post" ajaxAction="{ConfigActions::SetHomepage}"
              action="{$smarty.server.SCRIPT_NAME}">
            <input type="hidden" name="homepage_id" id="homepage_id"/>
        </form>
        {csrf_token}

        {include file="javascript-includes.tpl"}

        {jsfile src="ajax-helpers.js"}
        {jsfile src="js/jquery.form-3.09.min.js"}
        {jsfile src="admin/configuration.js"}
        <script>

            $(document).ready(function () {
                var config = new Configuration();
                config.init();
            });

        </script>
        <div id="wait-box" class="wait-box">
            <h3>{translate key=Working}</h3>
            {html_image src="reservation_submitting.gif"}
        </div>
    {/if}
</div>

{include file='globalfooter.tpl'}