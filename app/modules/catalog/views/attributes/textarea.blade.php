
    <section>
        <label class="label">{{ $attribute->name }}</label>
        <label class="textarea">
            {{ Form::textarea('attributes[' . $locale_sign . '][' . $attribute->slug . ']', $value) }}
        </label>
    </section>
