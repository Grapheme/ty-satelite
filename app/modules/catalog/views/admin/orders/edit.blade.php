@extends(Helper::acclayout())


@section('style')
    {{ HTML::style('private/css/redactor.css') }}
@stop


@section('content')

    <?
    $create_title = "Просмотр заказа";
    $edit_title   = "Добавить заказ";

    $url =
        @$element->id
        ? URL::route('catalog.orders.update', array('id' => $element->id))
        : URL::route('catalog.orders.store');

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
                <header>
                    Состав заказа
                </header>

                @if (count($element->products))

                    @foreach ($element->products as $product)
                        <?
                        if (!is_object($product->info) || !is_object($product->info->meta) || !($product->info->meta->name))
                            continue;
                        ?>

                        <fieldset class="catalog-product-block">

                            <section class="col-xs-12 col-sm-12 col-md-12 col-lg-8 pull-left">
                                <label class="label">
                                    {{ $product->info->meta->name }}
                                </label>
                                @if (count($product->attributes))
                                <label class="note">
                                    @foreach ($product->attributes as $product_attribute)
                                        <?
                                        if (!is_object($product_attribute))
                                            continue;
                                        ?>
                                        <b>{{ $product_attribute->attribute_cache }}</b>: {{ $product_attribute->value }} &nbsp; &nbsp; &nbsp; &nbsp;
                                    @endforeach
                                </label>
                                @endif
                            </section>

                            <section class="col-xs-12 col-sm-12 col-md-12 col-lg-4 pull-left text-right">

                                <label class="input display-inline-block" style="width:40px">
                                    {{ Form::text('products[' . $product->id . '][count]', (int)$product->count, array('class' => 'text-center catalog-product-count catalog-check-value', 'data-original' => (int)$product->count)) }}
                                </label>

                                <label class="input display-inline-block text-center" style="width:10px; position:relative; top:-10px">
                                    <i class="fa fa-remove"></i>
                                </label>

                                <label class="input display-inline-block" style="width:80px">
                                    {{ Form::text('products[' . $product->id . '][price]', (int)$product->price, array('class' => 'text-center catalog-product-price catalog-check-value', 'data-original' => (int)$product->price)) }}
                                </label>
                            </section>

                        </fieldset>

                    @endforeach

                    <fieldset>

                        <section class="col-xs-12 col-sm-12 col-md-12 col-lg-8 pull-left">
                            Общая сумма:
                        </section>

                        <section class="col-xs-12 col-sm-12 col-md-12 col-lg-4 pull-left text-right">

                            <div>
                                <label class="catalog-current-order-price" style="font-size:24px;" data-original="{{ (int)$element->total_sum }}">
                                    {{ $element->total_sum }}
                                </label>
                                <label class="catalog-new-order-price hidden" style="font-size:24px; color:red;">
                                    {{ $element->total_sum }}
                                </label>
                            </div>

                        </section>

                    </fieldset>
                @endif

                <footer>

                    @if (Input::get('archive') == 1)
                        <div class="margin-bottom-15 text-center">
                            Внимание! Данный заказ находится в архиве. При сохранении он автоматически станет активным.
                        </div>
                    @endif


                    <a class="btn btn-default no-margin regular-10 uppercase pull-left btn-spinner" href="{{ link::previous() }}">
                        <i class="fa fa-arrow-left hidden"></i> <span class="btn-response-text">Назад</span>
                    </a>
                    <button type="submit" autocomplete="off" class="btn btn-success no-margin regular-10 uppercase btn-form-submit">
                        <i class="fa fa-spinner fa-spin hidden"></i> <span class="btn-response-text">Сохранить</span>
                    </button>
                </footer>

		    </div>
    	</section>


        <section class="col col-6">
            <div class="well">
                <header>
                    Информация о заказе
                </header>
                <fieldset class="padding-bottom-15">

                    @if (isset($element->status) && is_object($element->status) && isset($element->status->meta) && is_object($element->status->meta) && $element->status->meta->title)
                    <section>
                        <label class="label">Статус:</label>
                        <label class="input catalog-current-order-status">
                            <b>{{ $element->status->meta->title }}</b>
                            <span class="catalog-change-order-status" style="border-bottom:1px dashed #333; cursor:pointer;">Изменить статус</span>
                        </label>
                        <div class="catalog-order-status-select hidden">
                            <label class="select">
                                {{ Form::select('status', $statuses, $element->status->id) }}
                            </label>
                            <label class="textarea">
                                {{ Form::textarea('status_comment', NULL, array('placeholder' => 'Комментарий к новому статусу')) }}
                            </label>
                            {{ Form::hidden('changer_id', Auth::user()->id) }}
                            {{ Form::hidden('changer_name', Auth::user()->name) }}
                        </div>
                    </section>
                    @endif

                    <section>
                        <label class="label">Покупатель:</label>
                        <label class="input">
                            {{ Form::text('client_name') }}
                        </label>
                    </section>

                    <section>
                        <label class="label">Информация о доставке:</label>
                        <label class="textarea">
                            {{ Form::textarea('delivery_info') }}
                        </label>
                    </section>

                    <section>
                        <label class="label">Комментарий к заказу:</label>
                        <label class="textarea">
                            {{ Form::textarea('comment') }}
                        </label>
                    </section>

                    <section>
                        <label class="label">Создан:</label>
                        <label class="text">
                            {{ $element->created_at->format('H:i, d.m.Y') }}
                        </label>
                    </section>

                    @if ($element->created_at != $element->updated_at)
                    <section>
                        <label class="label">Обновлен:</label>
                        <label class="text">
                            {{ $element->updated_at->format('H:i, d.m.Y') }}
                        </label>
                    </section>
                    @endif

                </fieldset>
            </div>
        </section>

        <section class="col col-6 pull-right">
            <div class="well">
                <header>
                    История статусов
                </header>
                <fieldset class="padding-bottom-15">

                    @if (count($element->statuses))

                        @foreach($element->statuses as $status)
                        <?
                        if (!is_object($status->info) || !is_object($status->info->meta) || !($status->info->meta->title))
                            continue;
                        ?>
                            {{-- Helper::d($status) --}}


                            <section>
                                <label class="label">
                                    {{ $status->info->meta->title }}
                                </label>
                                <label class="note">
                                    {{ $status->created_at->format('H:i, d.m.Y') }}
                                    @if ($status->changer_name)
                                    - {{ $status->changer_name }}@if ($status->comment):
                                           {{ $status->comment }}
                                        @endif
                                    @endif
                                </label>
                            </section>

                        @endforeach

                    @endif

                </fieldset>
            </div>
        </section>
        <!-- /Form -->
   	</div>

    @if(@$element->id)
    @else
    {{ Form::hidden('redirect', URL::route('catalog.category.index') . (Request::getQueryString() ? '?' . Request::getQueryString() : '')) }}
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