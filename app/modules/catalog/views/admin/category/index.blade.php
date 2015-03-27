@extends(Helper::acclayout())



<?
function write_level($hierarchy, $elements, $module, $sortable) {
?>
	@if($count = @count($elements))
        <ol class="dd-list">
        @foreach($hierarchy as $h)
            <?
            #Helper::d($h); #continue;
            #if (!isset($h['id']))
            #    continue;
            $element = $elements[$h['id']];
            #$element->extract();
            #Helper::ta($element); #continue;
            $line = $element->name;
            $line = preg_replace("~<br[/ ]*?".">~is", ' ', $line);
            $line2 = $element->slug;
            $line2 = preg_replace("~<br[/ ]*?".">~is", ' ', $line2);
            ?>

            <li class="dd-item dd3-item dd-item-fixed-height" data-id="{{ $element->id }}">
                @if ($sortable > 0)
                <div class="dd-handle dd3-handle">
                    Drag
                </div>
                @endif
                <div class="dd3-content{{ $sortable > 0 ? '' : ' padding-left-15 padding-top-10' }} clearfix">

                    <div class="pull-right dicval-actions dicval-main-actions dicval-actions-margin-left">

                        @if(Allow::action($module['group'], 'categories_edit'))
                        <a href="{{ action('catalog.category.edit', $element->id) . (Request::getQueryString() ? '?' . Request::getQueryString() : '') }}" class="btn btn-success dicval-action dicval-actions-edit" title="Редактировать категорию">
                            <!--Изменить-->
                        </a>
                        @endif

                        @if(Allow::action($module['group'], 'categories_delete'))
                        <form method="POST" action="{{ action('catalog.category.destroy', $element->id) }}" style="display:inline-block" class="dicval-action dicval-actions-delete" data-products-count="{{ @(int)count($element->products) }}">
                            <button type="button" class="btn btn-danger remove-category-list" title="Удалить категорию">
                                <!--Удалить-->
                            </button>
                        </form>
                        @endif

                    </div>

                    <div class="pull-right dicval-actions">

                        <a href="{{ action('catalog.category.index', array('root' => $element->id)) . (Request::getQueryString() ? '?' . Request::getQueryString() : '') }}" class="btn btn-warning dicval-action catalog-category-root" title="Вложенная структура категорий">
                            <i class="fa fa-sitemap"></i>
                        </a>

                        @if(Allow::action($module['group'], 'products_view'))
                        <a href="{{ action('catalog.products.index', array('category' => $element->id)) . (Request::getQueryString() ? '?' . Request::getQueryString() : '') }}" class="btn btn-default dicval-action catalog-products-list" title="Товары в категории">
                            <i class="fa fa-cube"></i>
                            {{ @(int)count($element->products) }}
                        </a>
                        @endif

                        <a href="{{ action('catalog.attributes.index', array('category' => $element->id)) . (Request::getQueryString() ? '?' . Request::getQueryString() : '') }}" class="btn btn-default dicval-action catalog-attributes-list" title="Кол-во &laquo;Групп атрибутов&raquo;/&laquo;Атрибутов&raquo; товаров в категории">
                            <i class="fa fa-puzzle-piece"></i>
                            @if (count($element->attributes_groups))
                                {{ count($element->attributes_groups) }} /
                                {{ $element->attributes_count }}
                            @endif
                        </a>

                    </div>

                    <div class="dicval-lines">
                        {{ $line }}
                        <br/>
                        <span class="note dicval_note">
                            {{ $line2 }}
                        </span>
                    </div>


                </div>
                @if (isset($h['children']) && is_array($h['children']) && count($h['children']))
                    <?
                    /**
                     * Вывод дочерних элементов
                     */
                    write_level($h['children'], $elements, $module, $sortable);
                    #Helper::dd($h['children']);
                    ?>
                @endif
            </li>
        @endforeach

        </ol>

    @endif
<?
}
?>




@section('content')

    @include($module['tpl'].'/menu')


	@if($count = @count($elements))

        <div class="settings settings-root hidden">{{ (int)Input::get('root') }}</div>

        <div class="dd dicval-list" data-output="#nestable-output">
            <?
            write_level($hierarchy, $elements, $module, $sortable);
            ?>
        </div>

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

    @if (@trim($dic_settings['javascript']))
    <script>
        {{ @$dic_settings['javascript'] }}
    </script>
    @endif

    @if ($sortable)
    <script>
    var show_hide_delete_buttons;
    $(document).ready(function() {

        var updateOutput = function(e) {

            show_hide_delete_buttons();

            var list = e.length ? e : $(e.target), output = $(list.data('output'));
            if (window.JSON) {
                var data = window.JSON.stringify(list.nestable('serialize'));
                var root = $('.settings.settings-root').text() || 0;
                //console.log(root);
                //return;
                $.ajax({
                    url: "{{ URL::route('catalog.category.nestedsetmodel') }}",
                    type: "post",
                    data: { data: data, root: root },
                    success: function(jhr) {
                        //console.clear();
                        //console.log(jhr);
                    }
                });
                output.val(data);
            } else {
                output.val('JSON browser support required for this demo.');
            }
        };

        //updateOutput($('.dd.dicval-list').data('output', $('#nestable-output')));

        $('.dd.dicval-list').nestable({
            maxDepth: {{ (int)$sortable }},
            group: 1
        }).on('change', updateOutput);

        show_hide_delete_buttons = function() {
            /*
            $('.dd-item > button:first-child').parent().find('.dd3-content:first .dicval-actions .dicval-actions-delete').hide();
            $('.dd-item > div:first-child').parent().find('.dd3-content:first .dicval-actions .dicval-actions-delete').show();
            */

            /**
             * После удаления категории нужно проверить, а не была ли она _последней вложенной_ дял родительской?
             */
            $('.dd-item > button:first-child').parent().find('.dd-list').each(function(){
                console.log($(this));
                console.log($(this).find('li').length);
                if ($(this).find('li').length > 0)
                    return;
                $(this).parent().find('button[type=button][data-action]').remove();
                $(this).remove();
            });



            $('.dd-item > button:first-child').parent().find('.dd3-content:first .dicval-actions .dicval-actions-delete').attr('data-can-delete', '0');
            $('.dd-item > div:first-child').parent().find('.dd3-content:first .dicval-actions .dicval-actions-delete').attr('data-can-delete', '1');

            $('.dd-item > button:first-child').parent().find('.dd3-content:first .dicval-actions .catalog-category-root').show();
            $('.dd-item > div:first-child').parent().find('.dd3-content:first .dicval-actions .catalog-category-root').hide();

            //*/
        }

        show_hide_delete_buttons();

    });
    </script>
    @endif

@stop

