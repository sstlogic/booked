{include file='globalheader.tpl'}

<div id="page-manage-email-templates" class="admin-page">

    <div id="manage-email-templates-header" class="admin-page-header">
        <div class="admin-page-header-title">
            <h1>{translate key=ManageEmailTemplates}</h1>
        </div>
    </div>


    <div class="default-box col-md-8 offset-md-2 col-s-12">

        <div class="d-flex mb-2">
            <div class="me-2">
                <select id="templateOpts" title="{translate key=EmailTemplate}" class="form-select">
                    <option value="">--- {translate key=SelectEmailTemplate} ---</option>
                    {foreach from=$Templates item=template}
                        <option value="{$template->FileName()}">{$template->Name()}</option>
                    {/foreach}
                </select>

            </div>
            <div>
                <select id="languageOpts" title="{translate key=Language}" class="form-select">
                    {foreach from=$Languages item=language}
                        <option value="{$language->LanguageCode}"
                                {if $Language==$language->LanguageCode}selected="selected"{/if}>{$language->DisplayName}</option>
                    {/foreach}
                </select>
            </div>
        </div>

        <div id="editEmailSection" class="no-show">

            <div>
                <form id="updateEmailForm" ajaxAction="{EmailTemplatesActions::Update}" method="post">
                    <div>
                    <textarea id="templateContents" {formname key=EMAIL_CONTENTS} title="{translate key=EmailTemplate}"
                              class="form-control" rows="20" style="width:100%"></textarea>
                    </div>

                    <div class="mt-2">
                        <div id="updateSuccess" class="alert alert-success col-xs-12" style="display:none;">
                            <span class="bi bi-check-circle"></span> {translate key=UpdateEmailTemplateSuccess}
                        </div>

                        <div id="updateFailed" class="alert alert-warning col-xs-12" style="display:none;">
                            <span class="bi bi-x-diamond"></span> {translate key=UpdateEmailTemplateFailed}
                        </div>
                    </div>

                    <div class="mt-2">
                        {indicator}
                        {update_button submit=true}
                        <input id="reloadEmailContents" type="button" class="btn btn-default"
                               value="{translate key=ReloadOriginalContents}"/>
                    </div>

                    <input type="hidden" id="templatePath" {formname key=EMAIL_TEMPLATE_NAME} />
                    <input type="hidden" {formname key=LANGUAGE} value="{$Language}" />
                    {csrf_token}
                </form>
            </div>

        </div>

        {include file="javascript-includes.tpl"}

        {jsfile src="ajax-helpers.js"}
        {jsfile src="admin/email-templates.js"}
        <script>

            $(document).ready(function () {
                var opts = {
                    scriptUrl: '{$smarty.server.SCRIPT_NAME}'
                };
                var emails = new EmailTemplateManagement(opts);
                emails.init();
            });

        </script>
        <div id="wait-box" class="wait-box">
            <h3>{translate key=Working}</h3>
            {html_image src="reservation_submitting.gif"}
        </div>
    </div>
</div>

{include file='globalfooter.tpl'}