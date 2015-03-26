<?
#Helper:dd($dic_id);
$menus = array();
$array = array();
if (isset($root_category) && is_object($root_category))
    $array['category'] = $root_category->id;

/*
$menus[] = array(
    'link' => URL::route('catalog.attributes.index', $array),
    'title' => 'Все группы',
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
/*
if  (
    Allow::action($module['group'], 'attributes_edit')
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
if (Allow::action($module['group'], 'attributes_create')) {
    #$current_link_attributes = Helper::multiArrayToAttributes(Input::get('filter'), 'filter');
    $menus[] = array(
        #'link' => URL::route('catalog.category.create', $current_link_attributes),
        'link' => URL::route('catalog.attributes_groups.create', $array), 'title' => 'Добавить группу', 'class' => 'btn btn-primary'
    );
}

#Helper::d($menus);
?>

<h1 class="top-module-menu">
    <a href="{{ URL::route('catalog.attributes.index') }}">
        Группа атрибутов
    </a>
    @if (isset($element) && is_object($element) && $element->name)

        @if (is_object($element->category))

            &nbsp;&mdash;&nbsp;
            <a href="{{ URL::route('catalog.attributes.index', $array) }}">
                {{ $element->category->name }}
            </a>

        @endif

        &nbsp;&mdash;&nbsp;
        {{ $element->name }}

    @elseif (isset($root_category) && is_object($root_category) && $root_category->name)

        &nbsp;&mdash;&nbsp;
        {{ $root_category->name }}

        @if (NULL !== ($group_id = Input::get('group')) && isset($groups) && isset($groups[$group_id]))

            &nbsp;&mdash;&nbsp;
            {{ $groups[$group_id] }}

        @endif

    @endif
</h1>

{{ Helper::drawmenu($menus) }}
