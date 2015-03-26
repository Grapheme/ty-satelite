<?
#Helper:dd($dic_id);
$menus = array();
/*
$menus[] = array(
        'link' => URL::route('catalog.category_attributes.index'),
        'title' => 'Все атрибуты',
        'class' => 'btn btn-default'
);
*/
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

if (Allow::action($module['group'], 'categories_create')) {
    $current_link_attributes = Helper::multiArrayToAttributes(Input::get('filter'), 'filter');
    $menus[] = array(
            'link' => URL::route('catalog.category_attributes.create', $current_link_attributes),
            'title' => 'Добавить',
            'class' => 'btn btn-primary'
    );
}

#Helper::d($menus);
?>

<h1 class="top-module-menu">
    <a href="{{ URL::route('catalog.category_attributes.index') }}">
        Атрибуты категорий
    </a>
    @if (isset($element) && is_object($element) && $element->name)
        &nbsp;&mdash;&nbsp;
        {{ $element->name }}
    @elseif (isset($root_category) && is_object($root_category) && $root_category->name)
        &nbsp;&mdash;&nbsp;
        {{ $root_category->name }}
    @endif
</h1>

{{ Helper::drawmenu($menus) }}
