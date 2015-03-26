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

                        @if(Allow::action($module['group'], 'products_edit'))
                        <a href="{{ action('catalog.products.edit', $element->id) . (Request::getQueryString() ? '?' . Request::getQueryString() : '') }}" class="btn btn-success dicval-action dicval-actions-edit" title="Изменить">
                            <!--Изменить-->
                        </a>
                        @endif

                        @if(Allow::action($module['group'], 'products_delete'))
                        <form method="POST" action="{{ action('catalog.products.destroy', $element->id) }}" style="display:inline-block" class="dicval-action dicval-actions-delete">
                            <button type="button" class="btn btn-danger remove-product-list" title="Удалить">
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
                @if ( FALSE )
                    @if (isset($h['children']) && is_array($h['children']) && count($h['children']))
                        <?
                        /**
                         * Вывод дочерних элементов
                         */
                        write_level($h['children'], $elements, $module, $sortable);
                        #Helper::dd($h['children']);
                        ?>
                    @endif
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

    @if ($sortable)
    <script>
    $(document).ready(function() {

        var updateOutput = function(e) {

            //show_hide_delete_buttons();

            var list = e.length ? e : $(e.target), output = $(list.data('output'));
            if (window.JSON) {
                var data = window.JSON.stringify(list.nestable('serialize'));
                var root = $('.settings.settings-root').text() || 0;
                //console.log(root);
                //return;
                $.ajax({
                    url: "{{ URL::route('catalog.products.nestedsetmodel') }}",
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

    });
    </script>
    @endif

@stop

