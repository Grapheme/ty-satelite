<?php

class AdminCatalogMenuController extends BaseController {

    public static $name = 'catalog';
    public static $group = 'catalog';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
        ##
    }

    ## Shortcodes of module
    public static function returnShortCodes() {
        ##
    }
    
    ## Actions of module (for distribution rights of users)
    public static function returnActions() {
        return array(
        	'catalog_allow'      => 'Глобальный доступ к каталогу',

            'categories_view'    => 'Просмотр категорий',
            'categories_create'  => 'Создание категорий',
            'categories_edit'    => 'Редактирование категорий',
            'categories_delete'  => 'Удаление категорий',
            'categories_seo'     => 'SEO-оптимизация категорий',

            'products_view'      => 'Просмотр товаров',
            'products_create'    => 'Создание товаров',
            'products_edit'      => 'Редактирование товаров',
            'products_delete'    => 'Удаление товаров',
            'products_seo'       => 'SEO-оптимизация товаров',

            'attributes_view'    => 'Просмотр атрибутов',
            'attributes_create'  => 'Создание атрибутов',
            'attributes_edit'    => 'Редактирование атрибутов',
            'attributes_delete'  => 'Удаление атрибутов',

            'orders_view'        => 'Просмотр заказов',
            'orders_create'      => 'Создание заказов', ## ???
            'orders_edit'        => 'Редактирование заказов', ## ???
            'orders_delete'      => 'Удаление заказов',
        );
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        return array(
        	'name' => self::$name,
        	'group' => self::$group,
            'title' => 'Каталог',
            'visible' => '1',
        );
    }


    public static function returnMenu() {

        $menu = array();
        $menu_child = array();

        if (Allow::action(self::$group, 'categories_view', false, true))
            $menu_child[] = array(
                'title' => 'Категории',
                'link' => self::$group . '/category',
                'class' => 'fa-cubes',
            );

        if (Allow::action(self::$group, 'products_view', false, true))
            $menu_child[] = array(
                'title' => 'Товары',
                'link' => self::$group . '/products',
                'class' => 'fa-cube',
            );

        if (Allow::action(self::$group, 'attributes_view', false, true))
            if (Input::get('category'))
                $menu_child[] = array(
                    'title' => 'Атрибуты',
                    'link' => self::$group . '/attributes?category=' . @(int)Input::get('category'),
                    'class' => 'fa-puzzle-piece',
                );

        if (Allow::action(self::$group, 'orders_view', false, true))
            $menu_child[] = array(
                'title' => 'Заказы',
                'link' => self::$group . '/orders',
                'class' => 'fa-shopping-cart',
            );

        if (count($menu_child) && Allow::action(self::$group, 'catalog_allow', false, true))
            $menu[] = array(
                'title' => 'Каталог',
                'link' => '#',
                'class' => 'fa-folder-open-o',
                'system' => 1,
                'permit' => 'catalog_allow',
                'menu_child' => $menu_child,
            );

        return $menu;
    }
        
    /****************************************************************************/
    
	public function __construct(){
        ##
	}
}


