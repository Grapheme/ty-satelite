@extends(Helper::acclayout())


@section('content')

    @include($module['tpl'].'/menu')


	@if(is_object($root_category))


        @if (isset($root_category->attributes_groups) && count($root_category->attributes_groups))

            <div class="dd attributes-groups-list">

                <ol class="dd-list">

                @foreach ($root_category->attributes_groups as $attributes_group)

                    <li class="dd-item dd3-item attributes_group clearfix margin-top-10 margin-bottom-10" data-id="{{ $attributes_group->id }}">
                        <div class="dd-handle dd3-handle" style="z-index:999">
                            Drag
                        </div>

                        <div class="dd3-content panel-group smart-accordion-default margin-top-0 margin-bottom-0 padding-0 border-0">
                            <div class="panel panel-default">
                                <div class="panel-heading" style="padding:2px 0;">
                                    <h4 class="panel-title panel-title-custom" style="position:relative">

                                        <div class="" style="position:absolute; right:0; top:0; padding: 6px 36px 6px 10px;">

                                            <span class="pull-right txt-color-grayDark margin-right-0">
                                                Группа
                                            </span>

                                            <div class="pull-right txt-color-grayDark margin-right-10">

                                                @if(Allow::action($module['group'], 'attributes_create'))
                                                    <a href="{{ URL::route('catalog.attributes.create', Input::all() + array('group' => $attributes_group->id)) }}" class="btn btn-xs btn-default add-attribute margin-right-5 display-inline-block padding-1-5" title="Добавить атрибут в группу">
                                                        <i class="fa fa-plus"></i>
                                                    </a>
                                                @endif

                                                @if(Allow::action($module['group'], 'attributes_edit'))
                                                    <a href="{{ URL::route('catalog.attributes_groups.edit', $attributes_group->id) }}" class="btn btn-xs btn-success edit-attributes-group display-inline-block padding-1-5" title="Редактировать группу">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                @endif

                                                @if(Allow::action($module['group'], 'attributes_delete'))
                                                    <form method="POST" action="{{ action('catalog.attributes_groups.destroy', $attributes_group->id) }}" style="display:inline-block" class="">
                                                        <button type="button" class="btn btn-xs btn-danger remove-attributes-group-list" title="Удалить атрибут">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>

                                        </div>

                                        <div data-toggle="collapse" data-parent="#accordion" href="#attributes_group_{{ $attributes_group->id }}" class="panel-title-link collapsed--" style="z-index:9">
                                            <i class="fa fa-lg fa-angle-down pull-right margin-top-0 accordion-collapse"></i>
                                            <i class="fa fa-lg fa-angle-up pull-right margin-top-0 accordion-collapse"></i>
                                            <span class="menu_item_title">
                                                {{ $attributes_group->name }}
                                                &nbsp;
                                            </span>
                                        </div>
                                    </h4>
                                </div>
                                <div id="attributes_group_{{ $attributes_group->id }}" class="bg-color-white panel-collapse collapse in">
                                    <div class="panel-body padding-10 menu_item_type_content">

                                        <div class="dd_ attributes-list">
                                            <ul class="dd-list_ padding-left-0 list-style-none sortable attributes margin-top-10 margin-bottom-10" style="min-height:30px;">

                                                @if (isset($attributes_group->attributes) && is_object($attributes_group->attributes) && count($attributes_group->attributes))


                                                    @foreach ($attributes_group->attributes as $attribute)

                                                        <li class="sortable-list-item sortable_item cursor-move" data-id="{{ $attribute->id }}">
                                                            <div class="dd3-content padding-left-10 padding-right-5">

                                                                <div class="pull-right txt-color-grayDark margin-right-0">

                                                                    @if(Allow::action($module['group'], 'attributes_edit'))
                                                                        <a href="{{ action('catalog.attributes.edit', $attribute->id) }}" class="btn btn-xs btn-success edit-attribute" title="Редактировать атрибут">
                                                                            <i class="fa fa-pencil"></i>
                                                                        </a>
                                                                    @endif

                                                                    @if(Allow::action($module['group'], 'attributes_delete'))
                                                                        <form method="POST" action="{{ action('catalog.attributes.destroy', $attribute->id) }}" style="display:inline-block" class="">
                                                                            <button type="button" class="btn btn-xs btn-danger remove-attribute-list" title="Удалить атрибут">
                                                                                <i class="fa fa-trash-o"></i>
                                                                            </button>
                                                                        </form>

                                                                    @endif
                                                                </div>

                                                                {{ $attribute->name }}
                                                                &nbsp;
                                                            </div>
                                                        </li>

                                                    @endforeach

                                                @endif

                                            </ul>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </li>

                @endforeach

                </ol>

            </div>

        @else

            У категории нет групп атрибутов.

        @endif

        <div class="clear clearfix"></div>

        {{ Helper::ta_($root_category) }}

        <div class="clear clearfix"></div>

	@else

        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="ajax-notifications custom">
                    <div class="alert alert-transparent">
                        <h4>Для управления атрибутами - выберите категорию</h4>
                        <p><br><i class="regular-color-light fa fa-th-list fa-3x"></i></p>
                    </div>
                </div>
            </div>
        </div>

	@endif

    <textarea name="order" id="nestable-output" rows="3" class="form-control font-md hidden"></textarea>

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

    @if (isset($root_category) && is_object($root_category) && count($root_category->attributes_groups)))
    <script>
    $(document).ready(function() {

        /**
         * Функция обработки сортировки групп атрибутов
         */
        var nestable_output = $('#nestable-output');
        var updateOutputAttributesGroup = function(e, save_nestable_order) {

            if (typeof save_nestable_order == 'undefined')
                save_nestable_order = true;

            var list = e.length ? e : $(e.target),
                output = list.data('output')
                ;

            //alert(typeof output);
            //console.log(list);

            if (typeof output == 'undefined') {
                return false;
            }

            //console.log(list.nestable('serialize'));
            //console.log(output);
            var last_output_val = output.val();

            if (window.JSON) {
                output.val(window.JSON.stringify(list.nestable('serialize')));
                //, null, 2));
            } else {
                output.val('JSON browser support required for this demo.');
            }

            /**
             * Если порядок изменился - сохраняем его аяксом
             */
            if (save_nestable_order && last_output_val != output.val()) {

                //alert(output.val());
                $.ajax({
                    url: "{{ URL::route('catalog.attributes.nestedsetmodel-attributes-groups') }}",
                    type: "post",
                    data: { data: output.val() }
                });
            }
        }

        /**
         * Активируем сортировку групп атрибутов
         */
        $('.dd.attributes-groups-list').nestable({
            maxDepth: 1
        }).on('change', updateOutputAttributesGroup);
        updateOutputAttributesGroup($('.dd.attributes-groups-list').data('output', $(nestable_output)), false);


        /**
         * AJAX-запрос на сервер с информацией о порядке атрибутов и их принадлежности к группе
         */
        function init_attributes_sortable_handler(url, element, success) {

            if (url) {

                var $this = element;
                //console.log($(this));

                var pls = $($this).find('tr, .sortable_item');
                var poss = [];
                $(pls).each(function(i, item) {
                    poss.push($(item).data('id'));
                });

                var group_id = $($this).parents('.attributes_group').attr('data-id');

                $.ajax({
                    url: url,
                    type: "post",
                    data: { poss: poss, group_id: group_id },
                    success: success
                });
            }

        }

        /**
         * Активируем сортировку списков атрибутов всех групп на странице
         */
        function init_attributes_sortable(url, selector, success, obj_selector) {

            if (typeof obj_selector == 'object')
                full_selector = obj_selector;
            else
                full_selector = selector;

            //alert(typeof selector);

            //init_attributes_sortable_onthefly(url, selector, success);

            $(full_selector).each(function(){

                if ( !$(this).data('sortable') ) {

                    $(this).sortable({
                        /**
                         * Drop элемента в том же списке, в котором он был изначально
                         */
                        stop: function() {

                            init_attributes_sortable_handler(url, $(this), success);
                        },
                        /**
                         * Drop элемента в connectWith списке
                         */
                        receive: function (event, ui) {

                            init_attributes_sortable_handler(url, $(this), success);
                        },
                        cancel: ".not-sortable",
                        distance: 5,
                        connectWith: selector
                    });
                }
            });
        }

        /**
         * Активируем сортировку списков атрибутов групп, для которых она еще не была активирована (добавлена динамически)
         */
        function init_attributes_sortable_onthefly(url, selector, success) {

            if (typeof success != 'function')
                success = function(){};

            var full_selector = ".sortable" + selector;

            $(document).on("mouseover", full_selector, function(e){

                if ( !$(this).data('sortable') ) {

                    init_attributes_sortable(url, full_selector, success, $(this));
                }
            });
        }

        /**
         * Активируем сортировку списков атрибутов всех групп на странице
         */
        init_attributes_sortable("{{ URL::route('catalog.attributes.order-attributes') }}", ".attributes", null);

    });
    </script>
    @endif

@stop

