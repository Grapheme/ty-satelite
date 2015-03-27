<?
    #Helper:dd($dic_id);
    $menus = array();

    $menus[] = array(
        'link' => URL::route('catalog.orders.index'),
        'title' => 'Активные',
        'class' => 'btn btn-default'
    );

    $menus[] = array(
        'link' => URL::route('catalog.orders.index', ['archive' => 1]),
        'title' => 'Архивные',
        'class' => 'btn btn-default'
    );

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

    if  (Allow::action($module['group'], 'orders_create') && 0) {
        #$current_link_attributes = Helper::multiArrayToAttributes(Input::get('filter'), 'filter');
        $array = array();
        $array['category'] = Input::get('category');
        $menus[] = array(
            'link' => URL::route('catalog.orders.create', $array),
            'title' => 'Добавить',
            'class' => 'btn btn-primary'
        );
    }

    #Helper::d($menus);
?>
    
    <h1>
        Заказы
        @if (Input::get('archive') == 1)
            &nbsp;&mdash;&nbsp;
            Архивные
        @endif
        @if (isset($element) && is_object($element) && $element->id)
            &nbsp;&mdash;&nbsp;
            Заказ №{{ $element->id }}
        @endif
    </h1>

    {{ Helper::drawmenu($menus) }}
