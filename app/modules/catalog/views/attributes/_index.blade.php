
{{ Helper::ta_($attribute) }}
<?
$value = (
            isset($attribute) && is_object($attribute)
            && isset($attribute->values) && is_object($attribute->values)
            && isset($attribute->values[$locale_sign])
        )
        ? $attribute->values[$locale_sign]
        : NULL;
if (is_object($value))
    $value = $value->value;
else
    $value = NULL;

/*
$value = NULL;
if (
    isset($attribute) && is_object($attribute)
    && isset($attribute->values) && is_object($attribute->values)
    && isset($attribute->values[$locale_sign]) && is_object($attribute->values[$locale_sign]) && isset($attribute->values[$locale_sign]->value)
) {
    $value = $attribute->values[$locale_sign]->value;
}
*/
?>

{{ Helper::ta_($attribute) }}
{{ Helper::ta_($value) }}

@include($module['gtpl'] . 'attributes.' . $attribute->type)

@if (0)
    @if ($attribute->type == 'text')
        @include($module['gtpl'] . 'attributes.text')
    @elseif ($attribute->type == 'textarea')
        @include($module['gtpl'] . 'attributes.textarea')
    @elseif ($attribute->type == 'wysiwyg')
        @include($module['gtpl'] . 'attributes.wysiwyg')
    @elseif ($attribute->type == 'checkbox')
        @include($module['gtpl'] . 'attributes.checkbox')
    @elseif ($attribute->type == 'select')
        @include($module['gtpl'] . 'attributes.select')
    @endif
@endif