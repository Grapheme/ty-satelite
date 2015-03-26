<?php

return array(

    'fields' => function() {

        /**
         * Предзагружаем нужные словари с данными, по системному имени словаря, для дальнейшего использования.
         * Делается это одним SQL-запросом, для снижения нагрузки на сервер БД.
         */
        $dics_slugs = array(
            'equipments',
            'ksp_cat',
        );
        $dics = Dic::whereIn('slug', $dics_slugs)->get();
        #->with('values')->get();
        $dics->load('values');
        /*
        $dics->load(['values_no_conditions' => function($query) {
            $query->orderBy($this->sort_by, 'ASC');
        }]);
        */
        $dics = Dic::modifyKeys($dics, 'slug');
        #Helper::tad($dics);
        $lists = Dic::makeLists($dics, 'values', 'name', 'id');
        $lists_ids = Dic::makeLists($dics, null, 'id', 'slug');
        #Helper::dd($lists);

        $equipments = array();
        if (isset($lists['equipments']) && count($lists['equipments']))
            foreach ($lists['equipments'] as $eq_id => $eq_title) {
                $equipments['weight_equipment_' . $eq_id] = array(
                    'title' => 'Вес для комплектации &laquo;' . $eq_title . '&raquo;',
                    'type' => 'text',
                    'value_modifier' => function($value) {
                        return (int)$value;
                    },
                );
            }

        return array(
            'ksp_cat' => array(
               'title' => 'Категория KSP',
               'type' => 'select',
               'values' => $lists['ksp_cat'], ## Используется предзагруженный словарь
               'default' => Input::get('filter.fields.ksp_cat') ?: null,
            ),
            'description' => array(
                'title' => 'Описание',
                'type' => 'textarea',
            ),
            'image' => array(
                'title' => 'Фото',
                'type' => 'image',
            ),
        ) + $equipments;
    },


    /**
     * MENUS - дополнительные пункты верхнего меню, под названием словаря.
     */
    'menus' => function($dic, $dicval = NULL) {
        $menus = array();
        $menus[] = array('raw' => '<br/>');

        /**
         * Предзагружаем словари для дальнейшего использования, одним SQL-запросом
         */
        $dics_slugs = array(
            'ksp_cat',
        );
        $dics = Dic::whereIn('slug', $dics_slugs)->with('values')->get();
        $dics = Dic::modifyKeys($dics, 'slug');
        $lists = Dic::makeLists($dics, 'values', 'name', 'id');
        #Helper::tad($lists);

        /**
         * Добавляем доп. элементы в меню, в данном случае: выпадающие поля для организации фильтрации записей по их свойствам
         */
        $menus[] = Helper::getDicValMenuDropdown('ksp_cat', 'Все категории', $lists['ksp_cat'], $dic);
        return $menus;
    },


    /**
     * HOOKS - набор функций-замыканий, которые вызываются в некоторых местах кода модуля словарей, для выполнения нужных действий.
     */
    'hooks' => array(

        /**
         * Вызывается первым из всех хуков в каждом действенном методе модуля
         */
        'before_all' => function ($dic) {
        },

        /**
         * Вызывается в самом начале метода index, после хука before_all
         */
        'before_index' => function ($dic) {
        },

        /**
         * Вызывается в методе index, перед выводом данных в представление (вьюшку).
         * На этом этапе уже известны все элементы, которые будут отображены на странице.
         */
        'before_index_view' => function ($dic, $dicvals) {
            /**
             * Предзагружаем нужные словари
             */
            $dics_slugs = array(
                'ksp_cat',
            );
            $dics = Dic::whereIn('slug', $dics_slugs)->get();
            $dics = Dic::modifyKeys($dics, 'slug');
            #$dics = DicLib::extracts($dics, null, 1, 1);

            if (isset($dics['ksp_cat']) && count($dics['ksp_cat'])) {
                #$dics['ksp_cat']->load('values');
                $dic = $dics['ksp_cat'];
                $dic->load('values_no_conditions');
                $temp = DicLib::modifyKeys($dic['values_no_conditions'], 'id');
                #Helper::tad($temp);
                Config::set('temp.ksp_cat.values', $temp);
            }

            #Helper::tad($dics);
            #Config::set('temp.index_dics', $dics);
        },
    ),


    'second_line_modifier' => function($line, $dic, $dicval) {
        $dicval->extract(1);
        #Helper::ta($dicval);
        $ksp_cat_values = Config::get('temp.ksp_cat.values');
        return @is_object($ksp_cat_values[$dicval->ksp_cat]) ? $ksp_cat_values[$dicval->ksp_cat]->name : '';
    },


    'seo' => 0,

    'versions' => 0,
);