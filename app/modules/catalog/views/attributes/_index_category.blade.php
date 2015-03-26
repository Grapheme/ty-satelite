<?
#Helper::tad($element);

$value = NULL;
if (
    isset($element) && is_object($element) && $element->id
    && isset($element->category_attributes_values) && is_object($element->category_attributes_values)
    && isset($element->category_attributes_values[$locale_sign])
    && isset($element->category_attributes_values[$locale_sign][$attribute->slug])
) {
    #dd(111);
    $value = $element->category_attributes_values[$locale_sign][$attribute->slug];
}
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