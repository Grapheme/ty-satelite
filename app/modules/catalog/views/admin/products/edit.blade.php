@extends(Helper::acclayout())


@section('style')
    {{ HTML::style('private/css/redactor.css') }}
@stop


@section('content')

    <?
    $create_title = "Изменить продукцию";
    $edit_title   = "Добавить продукцию";

    $url =
        @$element->id
        ? URL::route('catalog.products.update', array('id' => $element->id))
        : URL::route('catalog.products.store');
    $method     = @$element->id ? 'PUT' : 'POST';
    $form_title = @$element->id ? $create_title : $edit_title;
    ?>

    @include($module['tpl'].'/menu')

    {{ Form::model($element, array('url' => $url, 'class' => 'smart-form clearfix', 'id' => $module['entity'].'-form', 'role' => 'form', 'method' => $method, 'files' => true)) }}

    <!-- Fields -->
	<div class="row clearfix">


        <section class="col col-xs-12 col-sm-12 col-md-12 col-lg-12 clearfix">
            <div class="well">

                <header>
                    {{ $form_title }}
                </header>

                <fieldset class="padding-bottom-15">
                    <div class="widget-body">

                        <ul class="nav nav-tabs bordered">
                            <li class="active">
                                <a href="#tab_main" data-toggle="tab">
                                    Основное
                                </a>
                            </li>
                            <li class="">
                                <a href="#tab_attributes" data-toggle="tab">
                                    Описание
                                </a>
                            </li>
                            <li class="">
                                <a href="#tab_multimedia" data-toggle="tab">
                                    Мультимедиа
                                </a>
                            </li>
                            @if (Allow::action('seo', 'edit') && Allow::action($module['group'], 'products_seo'))
                            <li class="">
                                <a href="#tab_seo" data-toggle="tab">
                                    SEO-оптимизация
                                </a>
                            </li>
                            @endif
                        </ul>

                        <div class="tab-content">

                            <div class="tab-pane active fade in clearfix" id="tab_main">
                                <fieldset class="col col-xs-12 col-sm-6 col-md-6 col-lg-6">

                                    <section>
                                        <label class="label" data-helpmessage="Уникальное системное имя">
                                            Системное имя
                                        </label>
                                        <label class="input">
                                            {{ Form::text('slug', null, array()) }}
                                        </label>
                                        <label class="note second_note">
                                            Только символы англ. алфавита, знаки _ и -, цифры.<br/>
                                            Если оставить поле пустым - будет сгенерировано автоматически.
                                        </label>
                                    </section>

                                    <section>
                                        <label class="checkbox">
                                            {{ Form::checkbox('active', 1, ($element->active || !$element->id)) }}
                                            <i></i>
                                            Активно
                                        </label>
                                    </section>

                                    <section>
                                        <label class="label">Артикул</label>
                                        <label class="input">
                                            {{ Form::text('article') }}
                                        </label>
                                    </section>

                                    <section>
                                        <label class="label">Количество</label>
                                        <label class="input">
                                            {{ Form::text('amount') }}
                                        </label>
                                    </section>

                                    <section>
                                        <label class="label" data-helpmessage="Основная категория, к которой относится данный товар">
                                            Категория товара
                                        </label>
                                        <label class="select">
                                            {{ Form::select('category_id', $categories_for_select, $element->category_id ?: Input::get('category')) }}
                                        </label>
                                    </section>

                                </fieldset>
                            </div>


                            <div class="tab-pane fade clearfix" id="tab_attributes">
                                <fieldset class="col col-xs-12 col-sm-9 col-md-9 col-lg-9">

                                    <div class="widget-body">
                                        @if (count($locales) > 1)
                                        <ul class="nav nav-tabs bordered">
                                            <? $i = 0; ?>
                                            @foreach ($locales as $locale_sign => $locale_name)
                                            <li class="{{ !$i++ ? 'active' : '' }}">
                                                <a href="#product_locale_{{ $locale_sign }}" data-toggle="tab">
                                                    {{ $locale_name }}
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif

                                        <div class="tab-content @if(count($locales) > 1) padding-10 @endif">

                                            <? $i = 0; ?>
                                            @foreach ($locales as $locale_sign => $locale_name)
                                            <div class="tab-pane fade {{ !$i++ ? 'active in' : '' }} clearfix" id="product_locale_{{ $locale_sign }}">

                                                <section>
                                                    <label class="label">Название</label>
                                                    <label class="input">
                                                        {{ Form::text('meta[' . $locale_sign . '][name]', @$element->metas[$locale_sign]['name']) }}
                                                    </label>
                                                </section>

                                                <section>
                                                    <label class="checkbox">
                                                        {{ Form::checkbox('meta[' . $locale_sign . '][active]', 1, (@$element->metas[$locale_sign]['active'] || !$element->id)) }}
                                                        <i></i>
                                                        Активно
                                                    </label>
                                                </section>

                                                <section>
                                                    <label class="label">Краткое описание</label>
                                                    <label class="textarea">
                                                        {{ Form::textarea('meta[' . $locale_sign . '][description]', @$element->metas[$locale_sign]['description']) }}
                                                    </label>
                                                </section>

                                                <section>
                                                    <label class="label">Полное описание</label>
                                                    <label class="textarea">
                                                        {{ Form::textarea('meta[' . $locale_sign . '][full_description]', @$element->metas[$locale_sign]['full_description'], ['class' => 'redactor redactor_150']) }}

                                                    </label>
                                                </section>

                                                <section>
                                                    <label class="label">Цена</label>
                                                    <label class="input">
                                                        {{ Form::text('meta[' . $locale_sign . '][price]', @$element->metas[$locale_sign]['price']) }}
                                                    </label>
                                                </section>

                                                @if (isset($element->attributes_groups) && is_object($element->attributes_groups) && count($element->attributes_groups))

                                                    <hr class="margin-top-10 margin-bottom-10"/>

                                                    @foreach ($element->attributes_groups as $group)
                                                        @if (isset($group->attributes) && is_object($group->attributes) && count($group->attributes))

                                                            <fieldset class="padding-0 margin-bottom-15 padding-top-20 border-top-0">

                                                                <h4>{{ $group->name }}</h4>

                                                                @foreach ($group->attributes as $attribute)
                                                                    @include($module['gtpl'] . 'attributes._index', compact('module', 'attribute', 'locale_sign'))
                                                                @endforeach

                                                            </fieldset>

                                                        @endif
                                                    @endforeach
                                                @endif

                                            </div>
                                            @endforeach

                                        </div>
                                    </div>

                                </fieldset>
                            </div>


                            <div class="tab-pane fade in clearfix" id="tab_multimedia">
                                <fieldset class="col col-xs-12 col-sm-6 col-md-6 col-lg-6">

                                    <section>
                                        <label class="label">Основное изображение</label>
                                        <label class="input">
                                            {{ ExtForm::image('image_id', null) }}
                                        </label>
                                    </section>

                                    <section>
                                        <label class="label">Галерея</label>
                                        <label class="input">
                                            {{ ExtForm::gallery('gallery_id', null) }}
                                        </label>
                                    </section>

                                </fieldset>
                            </div>


                        @if (Allow::action('seo', 'edit') && Allow::action($module['group'], 'products_seo'))
                            <div class="tab-pane fade clearfix" id="tab_seo">
                                <fieldset class="col col-xs-12 col-sm-9 col-md-9 col-lg-9">

                                    <div class="widget-body">
                                        @if (count($locales) > 1)
                                        <ul class="nav nav-tabs bordered">
                                            <? $i = 0; ?>
                                            @foreach ($locales as $locale_sign => $locale_name)
                                            <li class="{{ !$i++ ? 'active' : '' }}">
                                                <a href="#seo_locale_{{ $locale_sign }}" data-toggle="tab">
                                                    {{ $locale_name }}
                                                </a>
                                            </li>
                                            @endforeach
                                        </ul>
                                        @endif

                                        <div class="tab-content @if(count($locales) > 1) padding-10 @endif">
                                            <? $i = 0; ?>
                                            @foreach ($locales as $locale_sign => $locale_name)
                                            <div class="tab-pane fade {{ !$i++ ? 'active in' : '' }} clearfix" id="seo_locale_{{ $locale_sign }}">

                                                {{ ExtForm::seo('seo[' . $locale_sign . ']', @$element->seos[$locale_sign]) }}

                                            </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </fieldset>
                            </div>
                            @endif

                        </div>

                    </div>
                </fieldset>

                <footer>
                    <a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner" href="{{ link::previous() }}">
                        <i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
                    </a>
                    <button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
                        <i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Сохранить</span>
                    </button>
                </footer>
            </div>
        </section>

   	</div>

    @if(@$element->id)
    @else
    {{ Form::hidden('redirect', URL::route('catalog.products.index') . (Request::getQueryString() ? '?' . Request::getQueryString() : '')) }}
    @endif

    {{ Form::close() }}

@stop


@section('scripts')
    <script>
    var essence = '{{ $module['entity'] }}';
    var essence_name = '{{ $module['entity_name'] }}';
    var validation_rules = {
        'meta[ru][name]': { required: true }
    };
    var validation_messages = {
        'meta[ru][name]': { required: "Укажите название" }
    };
    </script>

    {{ HTML::script('private/js/modules/standard.js') }}

	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function') {
			loadScript("{{ asset('private/js/vendor/jquery-form.min.js'); }}", runFormValidation);
		} else {
			loadScript("{{ asset('private/js/vendor/jquery-form.min.js'); }}");
		}        
	</script>

    {{ HTML::script('private/js/vendor/redactor.min.js') }}
    {{ HTML::script('private/js/system/redactor-config.js') }}

    {{-- HTML::script('private/js/modules/gallery.js') --}}
    {{-- HTML::script('private/js/plugin/select2/select2.min.js') --}}

@stop