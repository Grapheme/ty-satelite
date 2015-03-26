@extends(Helper::acclayout())


@section('content')

    @include($module['tpl'].'/menu')

	@if($count = @count($elements))

        <ol class="dd-list">
        @foreach($elements as $element)
            <?
            $line = "Заказ №" . $element->id . " - " . $element->total_sum . "";
            #$line = preg_replace("~<br[/ ]*?".">~is", ' ', $line);

            $line2 = "<b>Создан</b>: " . $element->created_at->format('H:i, d.m.Y')
                . ($element->client_name != '' ? " &nbsp; &nbsp; &nbsp; <b>Покупатель</b>: " . $element->client_name: '')
                . ($element->delivery_info != '' ? " &nbsp; &nbsp; &nbsp; <b>Доставка</b>: " . $element->delivery_info: '')
                ;
            #$line2 = preg_replace("~<br[/ ]*?".">~is", ' ', $line2);
            ?>


            <li class="dd-item dd3-item dd-item-fixed-height" data-id="{{ $element->id }}">
                <div class="dd3-content padding-left-10 clearfix">

                    <div class="pull-right dicval-actions dicval-main-actions">

                        @if(Allow::action($module['group'], 'orders_edit'))
                        <a href="{{ action('catalog.orders.edit', $element->id) . (Request::getQueryString() ? '?' . Request::getQueryString() : '') }}" class="btn btn-success dicval-action dicval-actions-edit" title="Изменить">
                            <!--Изменить-->
                        </a>
                        @endif

                        @if(Allow::action($module['group'], 'orders_delete') && !$element->deleted_at)
                        <form method="POST" action="{{ action('catalog.orders.destroy', $element->id) }}" style="display:inline-block" class="dicval-action dicval-actions-delete">
                            <button type="button" class="btn btn-danger remove-order-list" title="Перенести в архив">
                                <!--Удалить-->
                            </button>
                        </form>
                        @endif

                    </div>

                    <div class="pull-right dicval-actions">
                    </div>

                    <div class="dicval-lines">
                        {{ $line }}
                        <br/>
                        <span class="note dicval_note">
                            {{ $line2 }}
                        </span>
                    </div>

                </div>
            </li>
        @endforeach

        </ol>

        <div class="clear"></div>

	@else

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="ajax-notifications custom">
                    <div class="alert alert-transparent">
                        <h4>Список пуст</h4>
                        <p><br><i class="regular-color-light fa fa-th-list fa-3x"></i></p>
                    </div>
                </div>
            </div>
        </div>

	@endif

    <div class="clear"></div>

@stop


@section('scripts')
    <script>
    var essence = 'record';
    var essence_name = 'запись';
	var validation_rules = {
		name: { required: true }
	};
	var validation_messages = {
		name: { required: 'Укажите название' }
	};
    </script>

	<script type="text/javascript">
		if(typeof pageSetUp === 'function'){pageSetUp();}
		if(typeof runFormValidation === 'function'){
			loadScript("{{ asset('private/js/vendor/jquery-form.min.js'); }}", runFormValidation);
		}else{
			loadScript("{{ asset('private/js/vendor/jquery-form.min.js'); }}");
		}
	</script>

@stop

