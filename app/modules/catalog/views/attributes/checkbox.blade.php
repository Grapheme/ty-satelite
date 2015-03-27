
    <section>
        <label class="checkbox">
            {{ Form::checkbox('attributes[' . $locale_sign . '][' . $attribute->slug . ']', '1', $value) }}
            <i></i>
            {{ $attribute->name }}
        </label>
    </section>
