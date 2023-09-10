{*
Copyright 2012-2023 Twinkle Toes Software, LLC
*}
<div id="ra{$prefix}-{$attribute->Id()}"></div>
<script>
    createRoot(document.getElementById('ra{$prefix}-{$attribute->Id()}')).render(React.createElement(ReactComponents.BookedCustomAttribute, {
        attribute:{
            id: {$attribute->Id()},
            label: "{$attribute->Label()}",
            type: {$attribute->Type()},
            category: {$attribute->Category()},
            regex: "{$attribute->Regex()}",
            required: {javascript_boolean val=$attribute->Required()},
            entityIds: {javascript_array val=$attribute->EntityIds()},
            adminOnly: {javascript_boolean val=$attribute->AdminOnly()},
            possibleValues: {javascript_array val=$attribute->PossibleValueList()},
            sortOrder: {$attribute->SortOrder()|default:0},
            secondaryCategory: {$attribute->SecondaryCategory()|default:"null"},
            secondaryEntityIds: {javascript_array val=$attribute->SecondaryEntityIds()},
            isPrivate: {javascript_boolean val=$attribute->IsPrivate()},
        },
        {if isset($value)}
        value: "{$value}",
        {else}
        value: undefined,
        {/if}
        isReadonly: {javascript_boolean val=$readonly},
        isSearch: {javascript_boolean val=$searchmode},
        className: "{$className}",
        prefix: "{$prefix}",
        dateTimeFormat: '{Resources::GetInstance()->GetDateFormat("react_datetime")}',
        firstDayOfWeek: {$firstDayOfWeek|default:0},
        lang: "{Resources::GetInstance()->CurrentLanguageJs()}",
    }));
</script>
