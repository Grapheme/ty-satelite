
    {{ Helper::ta_($attribute) }}

    <?
    $values = array();
    if (isset($type) && $type == 'category') {

        $settings = isset($attribute->metas[$locale_sign]->settings) ? $attribute->metas[$locale_sign]->settings : NULL;

    } else {

        #$settings = $attribute->settings;
        #$settings = isset($attribute->settings) ? $attribute->settings : NULL;
        $settings = isset($attribute->metas) && isset($attribute->metas[$locale_sign]) && isset($attribute->metas[$locale_sign]->settings) ? $attribute->metas[$locale_sign]->settings : NULL;
    }

    if (isset($settings) && $settings && isset($settings['values']) && is_string($settings['values'])) {
        $temp = (array)explode("\n", $settings['values']);
        if (count($temp))
            foreach ($temp as $tmp) {
                $tmp = trim($tmp);
                if (!$tmp)
                    continue;
                $values[$tmp] = $tmp;
            }
    }

    ?>

    {{ Helper::tad_($attribute) }}
    {{ Helper::d_($value) }}

    <section>
        <label class="label">{{ $attribute->name }}</label>
        <label class="select">
            {{ Form::select('attributes[' . $locale_sign . '][' . $attribute->slug . ']', $values, $value) }}
        </label>
    </section>
