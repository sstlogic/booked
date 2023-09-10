{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}

<h1>{translate key=ConfigureApplication}</h1>

<div>
    <form class="register" method="post" action="{$smarty.server.SCRIPT_NAME}">

        {if $ShowInvalidPassword}
            <div class="error">{translate key=IncorrectInstallPassword}</div>
        {/if}

        {if $InstallPasswordMissing}
            <div class='error'>
				<p>{translate key=SetInstallPassword}</p>
			    <p>{translate key=InstallPasswordInstructions args="$ConfigPath,$ConfigSetting,$SuggestedInstallPassword"}</p>
            </div>
        {/if}

        {if $ShowPasswordPrompt}
            <ul class="no-style">
				<li>{translate key=ProvideInstallPassword}</li>
			    <li>{translate key=InstallPasswordLocation args="$ConfigPath,$ConfigSetting"}</li>
                <li>{textbox type="password" name="INSTALL_PASSWORD" class="textbox"}
                    <button type="submit" name="" class="btn btn-primary" value="submit">{translate key=Next} <span class="bi bi-arrow-right-circle"></span></button>
                </li>
            </ul>
        {/if}

		{if $ShowConfigSuccess}
            <h3>{translate key=ConfigUpdateSuccess}</h3>
            <a href="{$Path}{Pages::LOGIN}" class="btn btn-primary">{translate key=Login} <span class="bi bi-arrow-right-circle"></span></a>
		{/if}

		{if $ShowManualConfig}
			{translate key=ConfigUpdateFailure}

			<div style="font-family: courier; border: solid 1px #666;padding: 10px;margin-top: 20px;background-color: #eee">
				&lt;?php<br/>
				error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);<br/>
				{nl2br($ManualConfig)}
				?&gt;
			</div>
		{/if}

    </form>
</div>

{include file="javascript-includes.tpl"}
{include file='globalfooter.tpl'}