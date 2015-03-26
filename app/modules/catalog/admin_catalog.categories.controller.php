<?php

class AdminCatalogCategoriesController extends BaseController {

    public static $name = 'category';
    public static $group = 'catalog';
    public static $entity = 'category';
    public static $entity_name = 'категория';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
        $class = __CLASS__;
        $entity = self::$entity;

        Route::group(array('before' => 'auth', 'prefix' => $prefix . "/" . $class::$group), function() use ($class, $entity) {

            Route::post($class::$name.'/ajax-nested-set-model', array('as' => $class::$group . '.' . $class::$name . '.nestedsetmodel', 'uses' => $class."@postAjaxNestedSetModel"));

            Route::resource($class::$name, $class,
                array(
                    'except' => array('show'),
                    'names' => array(
                        'index'   => $class::$group . '.' . $class::$name . '.index',
                        'create'  => $class::$group . '.' . $class::$name . '.create',
                        'store'   => $class::$group . '.' . $class::$name . '.store',
                        'edit'    => $class::$group . '.' . $class::$name . '.edit',
                        'update'  => $class::$group . '.' . $class::$name . '.update',
                        'destroy' => $class::$group . '.' . $class::$name . '.destroy',
                    )
                )
            );
        });
    }

    ## Shortcodes of module
    public static function returnShortCodes() {
        ##
    }
    
    ## Actions of module (for distribution rights of users)
    public static function returnActions() {
        ##return array();
    }

    ## Info about module (now only for admin dashboard & menu)
    public static function returnInfo() {
        ##
    }
        
    /****************************************************************************/
    
	public function __construct() {

        $this->module = array(
            'name' => self::$name,
            'group' => self::$group,
            'rest' => self::$group,
            'tpl' => static::returnTpl('admin/' . self::$name),
            'gtpl' => static::returnTpl(),

            'entity' => self::$entity,
            'entity_name' => self::$entity_name,

            'class' => __CLASS__,
        );

        View::share('module', $this->module);
	}

	public function index() {

        Allow::permission($this->module['group'], 'categories_view');

        /**
         * Подготавливаем запрос для выборки
         */
        $elements = new CatalogCategory();
        $tbl_cat_category = $elements->getTable();
        $elements = $elements
            ->orderBy(DB::raw('-' . $tbl_cat_category . '.lft'), 'DESC') ## 0, 1, 2 ... NULL, NULL
            ->orderBy($tbl_cat_category . '.created_at', 'DESC')
            ->orderBy($tbl_cat_category . '.id', 'DESC')
            ->with('meta')
            ->with('products')
            ->with('attributes_groups.attributes')
        ;

        /**
         * Если задана корневая категория - выбираем только ее содержимое
         */
        #/*
        $root_category = null;
        if (NULL !== ($root_id = Input::get('root'))) {
            $root_category = CatalogCategory::find($root_id);
            $root_category->load('meta')->extract(1);
            #Helper::tad($root_category);
            if (is_object($root_category)) {
                $elements = $elements
                    ->where('lft', '>', $root_category->lft)
                    ->where('rgt', '<', $root_category->rgt)
                    ;
            }
        }
        #*/

        /**
         * Получаем все категории из БД
         */
        $elements = $elements->get();
        $elements = DicLib::extracts($elements, null, true, true);
        #Helper::smartQueries(1);
        #Helper::tad($elements);

        /**
         * Строим иерархию
         */
        $id_left_right = array();
        foreach($elements as $element) {
            $id_left_right[$element->id] = array();
            $id_left_right[$element->id]['left'] = $element->lft;
            $id_left_right[$element->id]['right'] = $element->rgt;
        }
        $hierarchy = (new NestedSetModel())->get_hierarchy_from_id_left_right($id_left_right);


        if ( 0 ) {
            Helper::ta($elements);
            Helper::tad($hierarchy);
        }

        $sortable = 9;

        $show_attributes_button = true;

        return View::make($this->module['tpl'].'index', compact('elements', 'hierarchy', 'sortable', 'root_category', 'show_attributes_button'));
	}

    /************************************************************************************/

	public function create() {

        Allow::permission($this->module['group'], 'categories_create');

        /**
         * Новая (пустая) категория
         */
        $element = new CatalogCategory();

        /**
         * Существующие категории (для списка родителей)
         */
        /**
         * Подготавливаем запрос для выборки
         */
        $elements = new CatalogCategory();
        $tbl_cat_category = $elements->getTable();
        $elements = $elements
            ->orderBy(DB::raw('-' . $tbl_cat_category . '.lft'), 'DESC') ## 0, 1, 2 ... NULL, NULL
            ->orderBy($tbl_cat_category . '.created_at', 'DESC')
            ->orderBy($tbl_cat_category . '.id', 'DESC')
            ->with('meta')
            #->with('products')
            #->with('attributes_groups.attributes')
        ;

        /**
         * Получаем все категории из БД
         */
        $elements = $elements->get();
        $elements = DicLib::extracts($elements, null, true, true);
        #Helper::smartQueries(1);
        #Helper::tad($elements);

        /**
         * Формируем массив с отступами
         */
        $attributes_from_category = NestedSetModel::get_array_for_select($elements);
        $parent_category = ['[нет]'] + $attributes_from_category;
        $attributes_from_category = ['[не копировать]'] + $attributes_from_category;
        #Helper::dd($categories_for_select);


        /**
         * Загружаем атрибуты категорий
         */
        $category_attributes = (new CatalogCategoryAttribute())
            ->with('metas')
            ->with('meta')
            ->references('meta')
            ->where('meta.language', Config::get('app.locale'))
            ->orderBy('meta.name')
            ->get()
        ;
        #Helper::tad($category_attributes);
        $category_attributes = DicLib::extracts($category_attributes, null, true, true);
        $category_attributes = Dic::modifyKeys($category_attributes, 'slug');
        #Helper::tad($category_attributes);


        /**
         * Локали
         */
        $locales = Config::get('app.locales');

		return View::make($this->module['tpl'].'edit', compact('element', 'locales', 'attributes_from_category', 'parent_category', 'category_attributes'));
	}
    

	public function edit($id) {

        Allow::permission($this->module['group'], 'categories_edit');

		$element = CatalogCategory::where('id', $id)
            ->with(['seos', 'metas.attributes_values', 'meta', 'category_attributes_values'])
            ->first()
        ;
        #Helper::tad($element);

        if (!is_object($element))
            App::abort(404);

        if (is_object($element->meta))
            $element->name = $element->meta->name;

        $element->extract();
        #Helper::tad($element);


        /**
         * Загружаем атрибуты категорий
         */
        $category_attributes = (new CatalogCategoryAttribute())
            ->where('active', 1)
            ->with('metas')
            ->with('meta')
            ->references('meta')
            ->where('meta.language', Config::get('app.locale'))
            ->orderBy('meta.name')
            ->get()
        ;
        #Helper::tad($category_attributes);
        $category_attributes = DicLib::extracts($category_attributes, null, true, true);
        $category_attributes = Dic::modifyKeys($category_attributes, 'slug');
        #Helper::tad($category_attributes);


        $locales = Config::get('app.locales');

        #Helper::tad($element);

        return View::make($this->module['tpl'].'edit', compact('element', 'locales', 'category_attributes'));
	}


    /************************************************************************************/


	public function store() {

        Allow::permission($this->module['group'], 'categories_create');
		return $this->postSave();
	}


	public function update($id) {

        Allow::permission($this->module['group'], 'categories_edit');
		return $this->postSave($id);
	}


	public function postSave($id = false){

        if (@$id)
            Allow::permission($this->module['group'], 'categories_edit');
        else
            Allow::permission($this->module['group'], 'categories_create');

		if(!Request::ajax())
            App::abort(404);

        if (!$id || NULL === ($element = CatalogCategory::find($id)))
            $element = new CatalogCategory();

        $input = Input::all();
        #Helper::tad($input);

        /**
         * Проверяем системное имя
         */
        if (!trim($input['slug'])) {
            $input['slug'] = $input['meta'][Config::get('app.locale')]['name'];
        }
        $input['slug'] = Helper::translit($input['slug']);

        $slug = $input['slug'];
        $exit = false;
        $i = 1;
        do {
            $test = CatalogCategory::where('slug', $slug)->first();
            #Helper::dd($count);

            if (!is_object($test) || $test->id == $element->id) {
                $input['slug'] = $slug;
                $exit = true;
            } else
                $slug = $input['slug'] . (++$i);

            if ($i >= 10 && !$exit) {
                $input['slug'] = $input['slug'] . '_' . md5(rand(999999, 9999999) . '-' . time());
                $exit = true;
            }

        } while (!$exit);

        /**
         * Проверяем флаг активности
         */
        $input['active'] = @$input['active'] ? 1 : NULL;

        /**
         * Выбрана ли родительская категория
         */
        $parent_cat_id = isset($input['parent_cat_id']) ? $input['parent_cat_id'] : false;
        unset($input['parent_cat_id']);

        /**
         * Выбрана ли категория для копирования набора атрибутов
         */
        $attributes_cat_id = isset($input['attributes_cat_id']) ? $input['attributes_cat_id'] : false;
        unset($input['attributes_cat_id']);

        #Helper::dd($input);
        #Helper::tad($input);


        /**
         * Загружаем атрибуты категорий
         */
        $category_attributes = (new CatalogCategoryAttribute())
            ->where('active', 1)
            ->with('metas')
            ->get()
        ;
        $category_attributes = DicLib::extracts($category_attributes, null, true, true);
        $category_attributes = Dic::modifyKeys($category_attributes, 'slug');
        #Helper::tad($category_attributes);



        $json_request['responseText'] = "<pre>" . print_r($_POST, 1) . "</pre>";
        #return Response::json($json_request,200);

        $json_request = array('status' => FALSE, 'responseText' => '', 'responseErrorText' => '', 'redirect' => FALSE);
		$validator = Validator::make($input, array('slug' => 'required'));
		if($validator->passes()) {

            #$redirect = false;

            if ($element->id > 0) {

                $element->update($input);
                $redirect = false;
                $category_id = $element->id;

                /**
                 * Обновим slug на форме
                 */
                if (Input::get('slug') != $input['slug']) {
                    $json_request['form_values'] = array('input[name=slug]' => $input['slug']);
                }

            } else {

                #Helper::tad($input);

                /**
                 * Если выбрана родительская категория, и она найдена в БД...
                 */
                if ($parent_cat_id && NULL !== ($parent_cat = CatalogCategory::find($parent_cat_id))) {

                    #Helper::tad($parent_cat);

                    /**
                     * Поставим новую категорию в конец родительской
                     */
                    $input['lft'] = @(int)$parent_cat->rgt;
                    $input['rgt'] = @(int)$parent_cat->rgt+1;
                    $element->save();

                    /**
                     * Увеличим отступ у всех категорий, следующей за родительской
                     */
                    #CatalogCategory::where('rgt', '>', $parent_cat->rgt)->get();
                    if ($parent_cat->rgt) {
                        DB::update(DB::raw("UPDATE " . $parent_cat->getTable() . " SET lft = lft + 2 WHERE lft > " . $parent_cat->rgt . ""));
                        DB::update(DB::raw("UPDATE " . $parent_cat->getTable() . " SET rgt = rgt + 2 WHERE rgt > " . $parent_cat->rgt . ""));
                    }

                    /**
                     * Увеличим RGT родительской категории на 2
                     */
                    $parent_cat->rgt = $parent_cat->rgt+2;
                    $parent_cat->save();

                } else {

                    /**
                     * Ставим элемент в конец списка
                     */
                    $max_rgt = CatalogCategory::max('rgt');
                    $input['lft'] = @(int)$max_rgt+1;
                    $input['rgt'] = @(int)$max_rgt+2;
                    $element->save();
                }

                $element->update($input);
                $category_id = $element->id;

                $redirect = Input::get('redirect');


                /**
                 * Функционал копирования из существующей категории в новую набора групп атрибутов и атрибутов для товаров внутри категории
                 */
                if ($attributes_cat_id) {

                    /**
                     * Получаем категорию-донора, вместе со всеми нужными данными
                     */
                    $donor_cat = CatalogCategory::where('id', $attributes_cat_id)
                        ->with(['attributes_groups.metas', 'attributes_groups.attributes.metas'])
                        ->first();
                    #Helper::tad($donor_cat);

                    if (is_object($donor_cat)) {

                        /**
                         * Если у донора есть группы атрибутов
                         */
                        if (isset($donor_cat->attributes_groups) && is_object($donor_cat->attributes_groups) && $donor_cat->attributes_groups->count()) {

                            foreach ($donor_cat->attributes_groups as $attributes_group) {

                                $temp_array = $attributes_group->toArray();

                                /**
                                 * Создаем массивы для создания новых записей в БД
                                 */
                                $temp_array_metas = $temp_array['metas'];
                                unset($temp_array['metas']);

                                $temp_array_attributes = $temp_array['attributes'];
                                unset($temp_array['attributes']);

                                $temp_array['category_id'] = $category_id;
                                unset($temp_array['id'], $temp_array['created_at'], $temp_array['updated_at']);

                                #Helper::ta($temp_array);

                                /**
                                 * На всякий случай здесь и далее будем удалять все,
                                 * что связано с ID только что созданной категории,
                                 * или новых вложенных элементов для нее.
                                 */
                                #CatalogAttributeGroup::where('category_id', $category_id)->delete();

                                /**
                                 * Создадим новую группу атрибутов, для новой категории
                                 */
                                $new_attr_group = CatalogAttributeGroup::create($temp_array);

                                #Helper::ta($new_attr_group);

                                /**
                                 * Создадим meta-записи текущей группы атрибутов
                                 */
                                if (isset($temp_array_metas) && is_array($temp_array_metas) && count($temp_array_metas)) {

                                    #CatalogAttributeGroupMeta::where('attributes_group_id', $new_attr_group->id)->delete();

                                    foreach ($temp_array_metas as $temp_array_meta) {

                                        $temp_array_meta['attributes_group_id'] = $new_attr_group->id;
                                        unset($temp_array_meta['id'], $temp_array_meta['created_at'], $temp_array_meta['updated_at']);

                                        #Helper::ta($temp_array_meta);
                                        $new_attr_group_meta = CatalogAttributeGroupMeta::create($temp_array_meta);
                                        #Helper::ta($new_attr_group_meta);

                                        unset($temp_array_meta);
                                    }
                                }

                                /**
                                 * Создадим атрибуты группы
                                 */
                                if (isset($temp_array_attributes) && is_array($temp_array_attributes) && count($temp_array_attributes)) {

                                    #CatalogAttribute::where('attributes_group_id', $new_attr_group->id)->delete();

                                    foreach ($temp_array_attributes as $temp_array_attribute) {

                                        $temp_array_attribute_metas = $temp_array_attribute['metas'];
                                        unset($temp_array_attribute['metas']);

                                        $temp_array_attribute['attributes_group_id'] = $new_attr_group->id;
                                        unset($temp_array_attribute['id'], $temp_array_attribute['created_at'], $temp_array_attribute['updated_at']);

                                        #Helper::ta($temp_array_attribute);
                                        $new_attr = CatalogAttribute::create($temp_array_attribute);
                                        #Helper::ta($new_attr);

                                        /**
                                         * Создадим meta-записи текущего атрибута группы
                                         */
                                        if (isset($temp_array_attribute_metas) && is_array($temp_array_attribute_metas) && count($temp_array_attribute_metas)) {

                                            CatalogAttributeMeta::where('attribute_id', $new_attr->id)->delete();

                                            foreach ($temp_array_attribute_metas as $temp_array_attribute_meta) {

                                                $temp_array_attribute_meta['attribute_id'] = $new_attr->id;
                                                unset($temp_array_attribute_meta['id'], $temp_array_attribute_meta['created_at'], $temp_array_attribute_meta['updated_at']);

                                                #Helper::ta($temp_array_attribute_meta);
                                                $new_attr_meta = CatalogAttributeMeta::create($temp_array_attribute_meta);
                                                #Helper::ta($new_attr_meta);

                                                unset($temp_array_attribute_meta);
                                            }
                                        }

                                        unset($new_attr);
                                        unset($temp_array_attribute);

                                    }
                                }

                                unset($temp_array);
                                unset($new_attr_group);

                            }
                        }

                    }

                    #die;

                } else {

                    /**
                     * Создаем группу атрибутов по умолчанию
                     */
                    $max_rgt = CatalogAttributeGroup::where('category_id', $category_id)->max('rgt');
                    $group = CatalogAttributeGroup::create(array(
                        'id' => null,
                        'category_id' => $category_id,
                        'active' => 1,
                        'slug' => 'default',
                        'lft' => @(int)$max_rgt+1,
                        'rgt' => @(int)$max_rgt+2,
                    ));
                    CatalogAttributeGroupMeta::create(array(
                        'id' => null,
                        'attributes_group_id' => $group->id,
                        'language' => 'ru',
                        'active' => 1,
                        'name' => 'По умолчанию',
                    ));

                }

            }


            /**
             * Сохраняем META-данные
             */
            if (
                isset($input['meta']) && is_array($input['meta']) && count($input['meta'])
            ) {
                foreach ($input['meta'] as $locale_sign => $meta_array) {
                    $meta_search_array = array(
                        'category_id' => $category_id,
                        'language' => $locale_sign
                    );
                    $meta_array['active'] = @$meta_array['active'] ? 1 : NULL;
                    $category_meta = CatalogCategoryMeta::firstOrNew($meta_search_array);
                    if (!$category_meta->id)
                        $category_meta->save();
                    $category_meta->update($meta_array);
                    unset($category_meta);
                }
            }


            /**
             * Сохраняем значения атрибутов категории
             */
            if (
                isset($input['attributes']) && is_array($input['attributes']) && count($input['attributes'])
            ) {

                #Helper::tad($category_attributes);
                #Helper::tad($input['attributes']);

                /**
                 * Перебираем все возможные атрибуты категорий
                 */
                foreach ($category_attributes as $cat_attr_slug => $cat_attr) {

                    #Helper::ta($cat_attr_slug);

                    /**
                     * Перебираем все доступные языки
                     */
                    #foreach ($input['attributes'] as $locale_sign => $meta_array) {
                    foreach (Config::get('app.locales') as $locale_sign => $temp) {

                        #Helper::ta($locale_sign);
                        #Helper::ta($temp);
                        #continue;

                        $value = isset($input['attributes'][$locale_sign][$cat_attr_slug]) ? $input['attributes'][$locale_sign][$cat_attr_slug] : NULL;

                        $meta_array = $cat_attr_search_array = array(
                            'category_id' => $category_id,
                            'attribute_id' => $cat_attr->id,
                            'language' => $locale_sign
                        );
                        #$meta_array['active'] = @$meta_array['active'] ? 1 : NULL;
                        $meta_array['value'] = $value ?: NULL;
                        $category_attr = CatalogCategoryAttributeValue::firstOrNew($cat_attr_search_array);
                        if (!$category_attr->id)
                            $category_attr->save();
                        $category_attr->update($meta_array);
                        unset($category_attr);
                    }
                }

                /*
                foreach ($input['attributes'] as $locale_sign => $meta_array) {
                    $meta_search_array = array(
                        'category_id' => $category_id,
                        'language' => $locale_sign
                    );
                    $meta_array['active'] = @$meta_array['active'] ? 1 : NULL;
                    $category_meta = CatalogCategoryMeta::firstOrNew($meta_search_array);
                    if (!$category_meta->id)
                        $category_meta->save();
                    $category_meta->update($meta_array);
                    unset($category_meta);
                }
                */
            }

            /**
             * Сохраняем SEO-данные
             */
            if (
                Allow::module('seo')
                && Allow::action('seo', 'edit')
                && Allow::action($this->module['group'], 'categories_seo')
                && isset($input['seo']) && is_array($input['seo']) && count($input['seo'])
            ) {
                foreach ($input['seo'] as $locale_sign => $seo_array) {
                    ## SEO
                    if (is_array($seo_array) && count($seo_array)) {
                        ###############################
                        ## Process SEO
                        ###############################
                        ExtForm::process('seo', array(
                            'module'  => 'CatalogCategory',
                            'unit_id' => $element->id,
                            'data'    => $seo_array,
                            'locale'  => $locale_sign,
                        ));
                        ###############################
                    }
                }
            }

            $json_request['responseText'] = 'Сохранено';
            if ($redirect)
			    $json_request['redirect'] = $redirect;
			$json_request['status'] = TRUE;

		} else {

			$json_request['responseText'] = 'Неверно заполнены поля';
			$json_request['responseErrorText'] = $validator->messages()->all();
		}
		return Response::json($json_request, 200);
	}

    /************************************************************************************/

	public function destroy($id){

        Allow::permission($this->module['group'], 'categories_delete');

		if(!Request::ajax())
            App::abort(404);

		$json_request = array('status' => FALSE, 'responseText' => '');

        #$element = CatalogCategory::where('id', $id)->with('attributes_groups.attributes')->first();
        $element = CatalogCategory::find($id);
        #Helper::tad($element);

        if (is_object($element)) {

            $element->full_delete();

            $json_request['responseText'] = 'Удалено';
            $json_request['status'] = TRUE;

        } else {

            $json_request['responseText'] = 'Запись не найдена';
            $json_request['status'] = TRUE;
        }

		return Response::json($json_request,200);
	}

    public function postAjaxNestedSetModel() {

        #$input = Input::all();

        $data = Input::get('data');
        $data = json_decode($data, 1);
        #Helper::dd($data);

        $offset = 0;
        $root_id = (int)Input::get('root');
        if ($root_id > 0) {
            $root_category = CatalogCategory::find($root_id);
            if (is_object($root_category)) {
                $offset = $root_category->lft;
            }
        }

        if (count($data)) {

            $id_left_right = (new NestedSetModel())->get_id_left_right($data);

            if (count($id_left_right)) {

                $cats = CatalogCategory::whereIn('id', array_keys($id_left_right))->get();

                if (count($cats)) {
                    foreach ($cats as $cat) {
                        $cat->lft = $id_left_right[$cat->id]['left'] + $offset;
                        $cat->rgt = $id_left_right[$cat->id]['right'] + $offset;
                        $cat->save();
                    }
                }
            }
        }

        return Response::make('1');
    }


}


