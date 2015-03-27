
    <section>
        <label class="label">{{ $attribute->name }}</label>
        <label class="input">
            {{ Form::text('attributes[' . $locale_sign . '][' . $attribute->slug . ']', $value) }}
        </label>
    </section>
