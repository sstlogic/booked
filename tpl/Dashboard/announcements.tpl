{*
Copyright 2011-2023 Twinkle Toes Software, LLC
*}
<div class="dashboard announcement-list" id="announcements-dashboard">
	<div class="dashboard-header">
		<div class="float-start">{translate key="Announcements"} <span class="badge">{count($Announcements)}</span></div>
		<div class="float-end">
			<button type="button" class="btn btn-link" title="{translate key=ShowHide} {translate key="Announcements"}">
				<i class="bi"></i>
			</button>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="dashboard-contents">
		<ul>
			{foreach from=$Announcements item=each}
				<li>
                    <div>{markdown text=$each->Text()}</div>
                </li>
				{foreachelse}
				<div class="noresults">{translate key="NoAnnouncements"}</div>
			{/foreach}
		</ul>
	</div>
</div>
