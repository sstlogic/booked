<span class="label custom-attribute">{$attribute->Label()}</span>
{if in_array($attribute->Type(), [CustomAttributeTypes::SINGLE_LINE_TEXTBOX, CustomAttributeTypes::MULTI_LINE_TEXTBOX, CustomAttributeTypes::SELECT_LIST, CustomAttributeTypes::DATETIME])}
    <span class="value custom-attribute">{if empty($value)}-{else}{$value}{/if}</span>
{/if}
{if $attribute->Type() == CustomAttributeTypes::CHECKBOX}
    <span class="value custom-attribute">{if empty($value)}No{else}Yes{/if}</span>
{/if}
{if $attribute->Type() == CustomAttributeTypes::MULTI_SELECT}
    <span class="value custom-attribute">{if empty($value)}-{else}{str_replace(",", ", ", $value)}{/if}</span>
{/if}
{if $attribute->Type() == CustomAttributeTypes::LINK}
    <span class="value custom-attribute">{if empty($value)}-{else}<a href="{$value}" target="_blank" rel="noreferrer">{$value}</a>{/if}</span>
{/if}