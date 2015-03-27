@extends(Helper::acclayout())


@section('style')
    {{ HTML::style('private/css/redactor.css') }}
@stop


@section('content')

    <?
    $create_title = "Изменить атрибут";
    $edit_title   = "Добавить атрибут";

    $url =
        @$element->id
        ? URL::route('catalog.attributes.update', array('id' => $element->id))
        : URL::route('catalog.attributes.store');
    $method     = @$element->id ? 'PUT' : 'POST';
    $form_title = @$element->id ? $create_title : $edit_title;
    ?>

    @include($module['tpl'].'/menu')

    {{ Form::model($element, array('url' => $url, 'class' => 'smart-form', 'id' => $module['entity'].'-form', 'role' => 'form', 'method' => $method, 'files' => true)) }}

    <!-- Fields -->
	<div class="row">

        <!-- Form -->
        <section class="col col-6">
            <div class="well">
                <header>{{ $form_title }}</header>

                <fieldset>

                    <section>
                        <label class="label">Системное имя</label>
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
                        <label class="label">Тип атрибута</label>
                        <label class="select">
                            {{ Form::select('type', @$types, $element->type ?: Input::get('type'), ['class' => 'attribute_type']) }}
                        </label>
                    </section>

                    <section class="select-values{{ $element->type != 'select' ? ' hidden' : '' }}">
                        <label class="checkbox">
                            {{ Form::checkbox('settings[selectable]', 1, @$element->settings['selectable']) }}
                            <i></i>
                            Покупатель должен будет выбрать значение при заказе
                        </label>
                    </section>

                    <section>
                        <label class="label">Группа</label>
                        <label class="select">
                            {{ Form::select('attributes_group_id', $groups, $element->attributes_group_id ?: Input::get('group')) }}
                        </label>
                    </section>

                    @if (count($locales) > 1)
                    <ul id="myTab2" class="nav nav-tabs bordered">
                        <? $i = 0; ?>
                        @foreach ($locales as $locale_sign => $locale_name)
                        <li class="{{ !$i++ ? 'active' : '' }}">
                            <a href="#category_locale_{{ $locale_sign }}" data-toggle="tab">
                                {{ $locale_name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    <div id="myTabContent1" class="tab-content @if(count($locales) > 1) padding-10 @endif">
                        <? $i = 0; ?>
                        @foreach ($locales as $locale_sign => $locale_name)
                        <div class="tab-pane fade {{ !$i++ ? 'active in' : '' }} clearfix" id="category_locale_{{ $locale_sign }}">

                            <section>
                                <label class="label">Название</label>
                                <label class="input">
                                    {{ Form::text('meta[' . $locale_sign . '][name]', @$element->metas[$locale_sign]['name']) }}
                                </label>
                            </section>

                            <section>
                                <label class="checkbox">
                                    {{ Form::checkbox('meta[' . $locale_sign . '][active]', 1, (@$element->metas[$locale_sign]['active'] || (!$element->id && $locale_sign == Config::get('app.locale')))) }}
                                    <i></i>
                                    Активно
                                </label>
                            </section>

                            <section class="select-values{{ $element->type != 'select' ? ' hidden' : '' }}">
                                <label class="label">Значения SELECT (по одному на строку)</label>
                                <label class="textarea">
                                    {{ Form::textarea('meta[' . $locale_sign . '][settings][values]', @$element->metas[$locale_sign][settings]['values']) }}
                                </label>
                            </section>

                        </div>
                        @endforeach
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


        @if (Allow::action('seo', 'edit') && Allow::action($module['group'], 'categories_seo') && FALSE)
        <section class="col col-6">
            <div class="well">
                <header>Поисковая оптимизация</header>
                <fieldset class="padding-bottom-15">
                    <div class="widget-body">
                        @if (count($locales) > 1)
                        <ul id="myTab2" class="nav nav-tabs bordered">
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

                        <div id="myTabContent2" class="tab-content @if(count($locales) > 1) padding-10 @endif">
                            <? $i = 0; ?>
                            @foreach ($locales as $locale_sign => $locale_name)
                            <div class="tab-pane fade {{ !$i++ ? 'active in' : '' }} clearfix" id="seo_locale_{{ $locale_sign }}">

                                {{ ExtForm::seo('seo[' . $locale_sign . ']', @$element->seos[$locale_sign]) }}

                            </div>
                            @endforeach
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
        @endif

        <!-- /Form -->
   	</div>

    @if(@$element->id)
    @else
    {{ Form::hidden('redirect', URL::route('catalog.attributes.index', array('category' => Input::get('category') ?: NULL))) }}
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

    {{-- HTML::script('private/js/vendor/redactor.min.js') --}}
    {{-- HTML::script('private/js/system/redactor-config.js') --}}

    {{-- HTML::script('private/js/modules/gallery.js') --}}
    {{-- HTML::script('private/js/plugin/select2/select2.min.js') --}}

@stop