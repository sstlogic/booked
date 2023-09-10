const path = window.location.pathname.replace("{$ReactPathName}", "");
const lang = "{$CurrentLanguageJs}";
const csrf = "{$CSRFToken}";
const appTitle = "{$AppTitle|escape}";
const timezone = "{$Timezone}";
const version = "{$Version}";
const uploadImagesPath = "{$UploadsUrl}";
const creditsEnabled = {javascript_boolean val=$CreditsEnabled};
const dateFormat = '{Resources::GetInstance()->GetDateFormat("react_date")}';
const dateTimeFormat = '{Resources::GetInstance()->GetDateFormat("react_datetime")}';
const scriptUrl = '{$ScriptUrl}';
const firstDayOfWeek = {$FirstDayOfWeek|default:0};

const props = {
path, lang, csrf, appTitle, timezone, version, uploadImagesPath, creditsEnabled, dateFormat, dateTimeFormat, scriptUrl, firstDayOfWeek,
};