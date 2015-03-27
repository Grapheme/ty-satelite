@extends(Helper::acclayout())


@section('content')

    @include($module['tpl'].'/menu')


	@if(isset($elements) && is_object($elements) && $elements->count())

        <ol class="dd-list">
            @foreach($elements as $element)

                <li class="dd-item dd3-item dd-item-fixed-height sortable-list-item" data-id="{{ $element->id }}">
                    <div class="dd3-content padding-left-15 padding-top-10 clearfix">

                        <div class="pull-right dicval-actions dicval-main-actions dicval-actions-margin-left">

                            @if(Allow::action($module['group'], 'categories_edit'))
                                <a href="{{ URL::route('catalog.category_attributes.edit', $element->id) . (Request::getQueryString() ? '?' . Request::getQueryString() : '') }}" class="btn btn-success dicval-action dicval-actions-edit" title="Изменить атрибут">
                                    <!--Изменить-->
                                </a>
                            @endif

                            @if(Allow::action($module['group'], 'categories_delete'))
                                <form method="POST" action="{{ URL::route('catalog.category_attributes.destroy', $element->id) }}" style="display:inline-block" class="dicval-action dicval-actions-delete">
                                    <button type="button" class="btn btn-danger remove-attribute-list" title="Удалить атрибут">
                                        <!--Удалить-->
                                    </button>
                                </form>
                            @endif

                        </div>

                        <div class="dicval-lines">
                            {{ $element->name }}
                            <br/>
                            <span class="note">
                                {{ $element->type }}
                            </span>
                        </div>


                    </div>
                </li>
            @endforeach

        </ol>

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

