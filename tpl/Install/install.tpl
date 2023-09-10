{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}

<div id="page-install">
    <h1>{translate key=InstallApplication}</h1>

    {if $ShowScriptUrlWarning}
        <div class="alert alert-danger">
            {translate key=ScriptUrlWarning args="$CurrentScriptUrl,$SuggestedScriptUrl"}
        </div>
    {/if}

    <div class="">
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

            {if $ShowUpToDateMessage}
                <div class="error" style="margin-bottom: 10px;">
                    <h3>{translate key=NoUpgradeNeeded}</h3>
                </div>
            {/if}

            {if $ShowPasswordPrompt}
                <div>
                    <div><label for="password">{translate key=ProvideInstallPassword}</label></div>
                    <div>{translate key=InstallPasswordLocation args="$ConfigPath,$ConfigSetting"}</div>
                    <div>{textbox type="password" name="INSTALL_PASSWORD" id="password"}</div>
                    <div>
                        <button type="submit" name="" class="btn btn-primary"
                                value="submit">{translate key=Next} <span class="bi bi-arrow-right-circle"></span></button>
                    </div>
                </div>
            {/if}

            {if $ShowDatabasePrompt}
                <div class="">
                    {if $ShowUpgradeOptions}
                    <div class="alert alert-warning">
                        <span class="bi bi-exclamation-triangle-fill"></span>
                        Before continuing, ensure you have made a backup of the database and filesystem. There is no way to roll back to an older version.
                    </div>
                    {/if}

                    <div>1) {translate key=VerifyInstallSettings args=$ConfigPath}
                        <div style="margin-left: 20px;">
                            <div><b>{translate key=DatabaseName}:</b> {$dbname}</div>
                            <div><b>{translate key=DatabaseUser}:</b> {$dbuser}</div>
                            <div><b>{translate key=DatabaseHost}:</b> {$dbhost}</div>
                        </div>
                    </div>
                    <div>&nbsp;</div>
                    <div>2) {translate key=DatabaseCredentials}</div>
                    <div class="form-group">
                        <label for="dbUser">{translate key=MySQLUser}</label>
                        {textbox name="INSTALL_DB_USER" id=dbUser}
                    </div>
                    <div class="form-group">
                        <label for="dbPassword">{translate key=Password}</label>
                        {textbox type="password" name="INSTALL_DB_PASSWORD" id=dbPassword}
                    </div>
                    <div>&nbsp;</div>
                    {if $ShowInstallOptions}
                        <div>3)<i>{translate key=InstallOptionsWarning}</i></div>
                        <div>
                            <label><input type="checkbox" name="create_database"/> {translate key=CreateDatabase}
                                ({$dbname})</label>
                        </div>
                        <div>
                            <label><input type="checkbox" name="create_user"/> {translate key=CreateDatabaseUser}
                                ({$dbuser})</label>
                        </div>
                        <div>
                            <br/>
                            <button type="submit" name="run_install" class="btn btn-primary"
                                    value="submit">{translate key=RunInstallation} <span class="bi bi-arrow-right-circle"></span></button>
                                <br/>
                        </div>
                    {/if}
                    {if $ShowUpgradeOptions}
                        <div>3) {translate key=UpgradeNotice args="$CurrentVersion,$TargetVersion"}</div>
                        <div>
                            <br/>
                            <button type="submit" name="run_upgrade" class="btn btn-primary"
                                    value="submit">{translate key=RunUpgrade} <span class="bi bi-arrow-right-circle"></span>
                                <br/>
                        </div>
                    {/if}
                </div>
            {/if}

            <div class="no-style">
                {foreach from=$installresults item=result}
                    <div class="mt-2">{translate key=Executing}: {$result->taskName}</div>
                    {if $result->WasSuccessful()}
                        <div style="background-color: #9acd32">{translate key=Success}</div>
                    {else}
                        <div style="border: solid red 5px;padding:10px;">
                            {translate key=StatementFailed}
                            <div class='no-style'>
                                <div>{translate key=SQLStatement}
                                    <pre>{$result->sqlText}</pre>
                                </div>
                                <div>{translate key=ErrorCode}
                                    <pre>{$result->sqlErrorCode}</pre>
                                </div>
                                <div>{translate key=ErrorText}
                                    <pre>{$result->sqlErrorText}</pre>
                                </div>
                            </div>
                        </div>
                    {/if}
                {/foreach}
                <div>&nbsp;</div>
                {if $InstallCompletedSuccessfully || $UpgradeCompletedSuccessfully}
                <div class="alert alert-success">
                    {if $InstallCompletedSuccessfully}
                        {translate key=InstallationSuccess}
                        <br/>
                        <a href="{$Path}{Pages::REGISTRATION}">{translate key=Register}</a>
                        {translate key=RegisterAdminUser args="$ConfigPath"}
                        <br/>
                        <br/>
                        <a href="{$Path}{Pages::LOGIN}">{translate key=Login}</a>
                        {translate key=LoginWithSampleAccounts}
                    {/if}
                    {if $UpgradeCompletedSuccessfully}
                        <div>{translate key=InstalledVersion args=$TargetVersion}</div>
                        <div>{translate key=InstallUpgradeConfig}</div>
                        <h3><a href="configure.php" class="btn btn-light">Proceed <span class="bi bi-arrow-right-circle"></span></span></a></h3>
                    {/if}
                </div>
                {/if}

                {if $InstallFailed}
                    <div class="alert alert-danger"> {translate key=InstallationFailure}</div>
                {/if}
            </div>
        </form>
    </div>

</div>

{include file="javascript-includes.tpl"}
{include file='globalfooter.tpl'}