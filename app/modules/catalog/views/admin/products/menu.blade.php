<?
#Helper:dd($dic_id);
$menus = array();

#/*
$array = array();
if (isset($root_category) && is_object($root_category) && $root_category->id)
    $array['category'] = $root_category->id;
#*/
/*
$array = [];

$menus[] = array(
    'link' => URL::route('catalog.products.index', $array),
    'title' => 'Все товары',
    'class' => 'btn btn-default'
);
*/

$menus[] = Catalog::getCategoryMenuDropdown($categories_for_select);


/*
if (
    Allow::action($module['group'], 'categories_delete')
    && isset($element) && is_object($element) && $element->id
    && ($element->lft+1 == $element->rgt)
) {
    $menus[] = array(
        'link' => URL::route('catalog.category.destroy', array($element->id)),
        'title' => '<i class="fa fa-trash-o"></i>',
        'class' => 'btn btn-danger remove-category-record',
        'others' => [
            'data-goto' => URL::route('catalog.category.index'),
            'title' => 'Удалить запись'
        ]
    );
}
*/

/*
if  (
    Allow::action($module['group'], 'categories_edit')
    && isset($root_category) && is_object($root_category) && $root_category->id
) {
    $current_link_attributes = Helper::multiArrayToAttributes(Input::get('filter'), 'filter');
    $menus[] = array(
        'link' => URL::route('catalog.category.edit', array('id' => $root_category->id) + $current_link_attributes),
        'title' => 'Изменить',
        'class' => 'btn btn-success'
    );
}
*/

if (Allow::action($module['group'], 'products_create')) {
    #$current_link_attributes = Helper::multiArrayToAttributes(Input::get('filter'), 'filter');
    $array = array();
    $array['category'] = Input::get('category');
    $menus[] = array(
            'link' => URL::route('catalog.products.create', $array),
            'title' => 'Добавить',
            'class' => 'btn btn-primary'
    );
}

#Helper::d($menus);
?>

<h1 class="top-module-menu">
    <a href="{{ URL::route('catalog.products.index') }}">
        Товары
    </a>
    @if (isset($element) && is_object($element) && $element->name)

        @if (isset($element->category) && is_object($element->category) && $element->category->name)
            &nbsp;&mdash;&nbsp;
            <a href="{{ URL::route('catalog.products.index', array('category' => $element->category->id)) }}">
                {{ $element->category->name }}
            </a>
        @endif

        &nbsp;&mdash;&nbsp;
        {{ $element->name }}

    @elseif (isset($root_category) && is_object($root_category) && $root_category->name)

        &nbsp;&mdash;&nbsp;
        <a href="{{ URL::route('catalog.products.index', $array) }}">
            {{ $root_category->name }}
        </a>

    @endif
</h1>

{{ Helper::drawmenu($menus) }}
