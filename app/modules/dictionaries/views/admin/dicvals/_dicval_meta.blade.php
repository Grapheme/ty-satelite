<?
#Helper::tad($element->metas->where('language', $locale_sign)->first());
#Helper::ta($element);
#Helper::dd($dic_settings);

$element_meta = new DicValMeta;
if (isset($element->metas) && is_object($element->metas) && $element->metas->count())
    foreach ($element->metas as $tmp) {
        #Helper::ta($tmp);
        if ($tmp->language == $locale_sign) {
            $element_meta = $tmp;
            break;
        }
    }
?>

@if (count($locales) > 1 && FALSE)
    <section>
        <label class="label">{{ $dic->name_title ?: 'Название' }}</label>
        <label class="input select input-select2">
            {{ Form::text('locales[' . $locale_sign . '][name]', $element_meta->name, array()) }}
            @if (isset($dic_settings['name_note']))
                {{ $dic_settings['name_note'] }}
            @endif
        </label>
    </section>
@endif

@if (count($fields_i18n))

    <?

    #Helper::ta($fields_i18n);

    $element_fields = array();
    if (isset($element->allfields) && is_object($element->allfields) && count($element->allfields)) {
        $element_fields = $element->allfields;
        if (count($element_fields))
            foreach ($element_fields as $f => $field) {
                if (!$field->language)
                    unset($element_fields[$f]);
            }
        #$element_fields = $element_fields->lists('value', 'key');
        #Helper::ta($element_fields);
    }
    $element_textfields = array();
    if (isset($element->alltextfields) && is_object($element->alltextfields) && count($element->alltextfields)) {
        $element_textfields = $element->alltextfields;
        if (count($element_textfields))
            foreach ($element_textfields as $f => $field) {
                if (!$field->language)
                    unset($element_textfields[$f]); else
                    $element_fields[$f] = $field;
            }
        #$element_fields = $element_fields->lists('value', 'key');
        #Helper::ta($element_fields);
    }
    #Helper::ta($element_fields);
    #Helper::ta($element_textfields);
    ?>

    @foreach ($fields_i18n as $field_name => $field)
        <?
        $field_meta = new DicFieldVal();
        foreach ($element_fields as $tmp) {
            #Helper::ta($tmp);
            if (isset($field_name) && $tmp->key == $field_name && $tmp->language == $locale_sign) {
                $field_meta = $tmp;
                #Helper::ta($field_meta);
                break;
            }
        }

                #var_dump($field_meta);
                #continue;

        #$field_meta_value = isset($field_meta->value) && $field_meta->value ? $field_meta->value : NULL;

        $form_field = Helper::formField('fields_i18n[' . $locale_sign . '][' . $field_name . ']', $field, $field_meta->value, $element);
        if (!isset($form_field) || !$form_field)
            continue;

        #continue;


        if (isset($field['scripts'])) {
            #var_dump($field['scripts']);
            global $dicval_edit_scripts;
            #var_dump($dicval_edit_scripts);
            $dicval_edit_scripts[] = $field['scripts'];
            #var_dump($dicval_edit_scripts);
        }


        #$form_field = false;
        ?>

        <section>
            @if (!@$field['no_label'] && isset($field['title']))
                <label class="label">{{ $field['title'] }}</label>
            @endif
            @if (@$field['first_note'])
                <label class="note">{{ @$field['first_note'] }}</label>
            @endif
            <div class="input {{ $field['type'] }} {{ @$field['label_class'] }}">
                {{ $form_field }}
            </div>
            @if (@$field['second_note'])
                <label class="note">{{ @$field['second_note'] }}</label>
            @endif
        </section>

    @endforeach


@endif
