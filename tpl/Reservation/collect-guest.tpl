{*
Copyright 2017-2023 Twinkle Toes Software, LLC
*}
{include file='globalheader.tpl'}
<div class="page-guest-collect">

    {validation_group class="alert alert-danger"}
    {validator id="emailformat" key="ValidEmailRequired"}
    {validator id="uniqueemail" key="UniqueEmailRequired"}
    {/validation_group}

    <div class="default-box col-md-6 offset-md-3 col-sm-12">
        <h2>{translate key=WeNeedYourEmailAddress}</h2>

        <form method="post" id="form-guest-collect" action="{$smarty.server.REQUEST_URI|escape:'html'}">

            <div class="row">
                <div class="col-xs-12">
                    <label class="form-label" for="email">{translate key="Email"}</label>
                    <div class="input-group mb-3">
					                            <span class="input-group-text">
					                                <i class="bi-person-circle"></i>
					                            </span>

                        {textbox type="email" name="EMAIL" class="input" value="Email" required="required"}
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="update btn btn-primary col-xs-12" name="" id="btnUpdate">
                    {translate key='Continue'}
                </button>
            </div>
        </form>
    </div>
    {setfocus key='EMAIL'}

    {include file="javascript-includes.tpl"}
    {jsfile src="ajax-helpers.js"}

    <div id="wait-box" class="wait-box">
        <h3>{translate key=Working}</h3>
        {html_image src="reservation_submitting.gif"}
    </div>

</div>
{include file='globalfooter.tpl'}