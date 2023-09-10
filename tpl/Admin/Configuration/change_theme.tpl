{*
Copyright 2013-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl' }

<div id="page-change-theme" class="admin-page">
    <div id="manage-look-and-feel-header" class="admin-page-header">
        <div class="admin-page-header-title">
            <h1>{translate key=LookAndFeel}</h1>
        </div>
    </div>

    <div class="default-box col-md-8 offset-md-2 col-s-12">
        <div id="successMessage" class="alert alert-success" style="display:none;">
            {translate key=ThemeUploadSuccess}
        </div>

        <form id="elementForm" action="{$smarty.server.SCRIPT_NAME}" ajaxAction="update" method="post"
              enctype="multipart/form-data">
            <div class="validationSummary alert alert-danger no-show" id="validationErrors">
                <ul>
                    {async_validator id="logoFileExt"}
                    {async_validator id="cssFileExt"}
                    {async_validator id="faviconFileExt"}
                    {async_validator id="logoFile"}
                    {async_validator id="cssFile"}
                    {async_validator id="faviconFile"}
                </ul>
            </div>

            <div>
                <h4>{translate key="Logo"} (*.png, *.gif, *.jpg - 50px height)</h4>

                <div>
                    <a href="{$ScriptUrl}/img/{$LogoUrl}" download="{$ScriptUrl}/img/{$LogoUrl}">{$LogoUrl}</a>
                    <a href="#" id="removeLogo">{translate key=Remove}</a>
                </div>
                <label for="logoFile" class="no-show">Logo File</label>
                <input type="file" {formname key=LOGO_FILE} class="pull-left" id="logoFile" accept="image/*" />

                <a href="#" class="clearInput inline"><span class="no-show">{translate key=Delete}</span><span
                            class="bi bi-x-circle icon remove"></span></a>
            </div>

            <div>
                <h4>Favicon (*.ico, *.png, *.gif, *.jpg - 32px x 32px or 16px x 16px)</h4>

                <div>
                    <a href="{$ScriptUrl}/{$FaviconUrl}" download="{$ScriptUrl}/img/{$FaviconUrl}">{$FaviconUrl}</a>
                    <a href="#" id="removeFavicon">{translate key=Remove}</a>
                </div>
                <label for="faviconFile" class="no-show">Favicon File</label>
                <input type="file" {formname key=FAVICON_FILE} class="pull-left" id="faviconFile"  accept="image/*"/>

                <a href="#" class="clearInput inline"><span class="no-show">{translate key=Delete}</span><span
                            class="bi bi-x-circle icon remove"></span></a>
            </div>

            <div>
                <div>
                    <h4>{translate key="CssFile"} (*.css)</h4>

                    <a href="{$ScriptUrl}/css/{$CssUrl}" download="{$ScriptUrl}/css/{$CssUrl}">{$CssUrl}</a>
                </div>
                <label for="cssFile" class="no-show">CSS File</label>
                <input type="file" {formname key=CSS_FILE} class="pull-left" id="cssFile" accept="text/css">
                <a href="#" class="clearInput"><span class="no-show">{translate key=Delete}</span><span
                            class="bi bi-x-circle icon remove"></span></a>
            </div>

            <div class="clearfix"></div>

            <button type="button" class="btn btn-success update margin-top-25" name="{Actions::SAVE}" id="saveButton">
                {translate key='Update'}
            </button>

            {csrf_token}

        </form>

    </div>

    <div id="wait-box" class="wait-box">
        <h3>{translate key=Working}</h3>
        {html_image src="reservation_submitting.gif"}
    </div>

    {include file="javascript-includes.tpl"}

    {jsfile src="ajax-helpers.js"}
    {jsfile src="js/jquery.form-3.09.min.js"}
    {jsfile src="js/ajaxfileupload.js"}
    {jsfile src="ajax-form-submit.js"}

    <script>
        $('document').ready(function () {

            $('#elementForm').bindAjaxSubmit($('#saveButton'), $('#successMessage'), $('#wait-box'));

            $('.clearInput').click(function (e) {
                e.preventDefault();
                $(this).prev('input').val('');
            });

            $('#removeLogo').click(function (e) {
                e.preventDefault();

                PerformAsyncAction($(this), function () {
                    return '{$smarty.server.SCRIPT_NAME}?action=removeLogo';
                });
            });

            $('#removeFavicon').click(function (e) {
                e.preventDefault();

                PerformAsyncAction($(this), function () {
                    return '{$smarty.server.SCRIPT_NAME}?action=removeFavicon';
                });
            });
        });

    </script>

</div>
{include file='globalfooter.tpl'}