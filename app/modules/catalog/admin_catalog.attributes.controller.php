<?php

class AdminCatalogAttributesController extends BaseController {

    public static $name = 'attributes';
    public static $group = 'catalog';
    public static $entity = 'attribute';
    public static $entity_name = 'атрибут';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
        $class = __CLASS__;
        $entity = self::$entity;

        Route::group(array('before' => 'auth', 'prefix' => $prefix . "/" . $class::$group), function() use ($class, $entity) {

            Route::post($class::$name.'/ajax-order-save-attributes', array('as' => $class::$group . '.' . $class::$name . '.order-attributes', 'uses' => $class."@postAjaxOrderSaveAttributes"));

            Route::post($class::$name.'/ajax-nested-set-model-attributes-groups', array('as' => $class::$group . '.' . $class::$name . '.nestedsetmodel-attributes-groups', 'uses' => $class."@postAjaxNestedSetModelAttributesGroups"));

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

        /**
         * Типы атрибутов
         */
        $this->types = array(
            'text' => 'Текстовая строка',
            'textarea' => 'Многострочный текст',
            #'wysiwyg' => 'WYSIWYG редактор текста',
            'checkbox' => 'Чекбокс',
            'select' => 'Список',
        );
    }

	public function index() {

        Allow::permission($this->module['group'], 'attributes_view');

        $root_category = NULL;
        if (NULL !== ($cat_id = Input::get('category'))) {

            $root_category = CatalogCategory::where('id', $cat_id)
                ->with('meta')
                ->with('attributes_groups.meta')
                ->with('attributes_groups.attributes.meta')
                ->first()
            ;

            #Helper::tad($root_category);

            if (is_object($root_category))
                $root_category = $root_category->extract(true);
        }

        #Helper::tad($root_category);

        return View::make($this->module['tpl'].'index', compact('root_category'));
	}

    /************************************************************************************/

	public function create() {

        Allow::permission($this->module['group'], 'attributes_create');


        $root_category = NULL;
        if (NULL !== ($cat_id = Input::get('category'))) {

            $root_category = CatalogCategory::where('id', $cat_id)
                ->with('meta')
                ->with('attributes_groups.meta')
                ->first()
            ;

            #Helper::tad($root_category);

            if (is_object($root_category))
                $root_category = $root_category->extract(true);
        }

        #Helper::ta($root_category);

        if (!is_object($root_category))
            App::abort(404);

        $groups = array();
        if (is_object($root_category) && isset($root_category->attributes_groups) && is_object($root_category->attributes_groups) && count($root_category->attributes_groups))
            foreach ($root_category->attributes_groups as $group)
                $groups[$group->id] = $group->name;
        #Helper::dd($groups);

        $element = new CatalogAttribute();

        $types = $this->types;

        $locales = Config::get('app.locales');

		return View::make($this->module['tpl'].'edit', compact('element', 'locales', 'root_category', 'groups', 'types'));
	}
    

	public function edit($id) {

        Allow::permission($this->module['group'], 'attributes_edit');

		$element = CatalogAttribute::where('id', $id)
            ->with(['metas', 'meta', 'attributes_group.meta', 'attributes_group.category.meta'])
            ->first()
        ;

        if (!is_object($element))
            App::abort(404);

        #Helper::tad($element);

        $element->extract(false);

        #Helper::tad($element);

        $root_category = null;

        if (is_object($element) && is_object($element->meta)) {

            $element->extract(0);

            if (isset($element->attributes_group) && isset($element->attributes_group->category) && is_object($element->attributes_group->category)) {
                #$root_category = $element->attributes_group->category->id;
                $cat_id = $element->attributes_group->category->id;

                $root_category = CatalogCategory::where('id', $cat_id)
                    ->with(['meta', 'attributes_groups.meta'])
                    ->first()
                ;

                if (is_object($root_category))
                    $root_category = $root_category->extract(true);
            }
        }

        #Helper::tad($root_category);

        $groups = array();
        if (is_object($root_category) && isset($root_category->attributes_groups) && is_object($root_category->attributes_groups) && count($root_category->attributes_groups))
            foreach ($root_category->attributes_groups as $group)
                $groups[$group->id] = $group->name;
        #Helper::dd($groups);

        $types = $this->types;

        $locales = Config::get('app.locales');

        #Helper::tad($element);

        return View::make($this->module['tpl'].'edit', compact('element', 'locales', 'root_category', 'groups', 'types'));
	}


    /************************************************************************************/


	public function store() {

        Allow::permission($this->module['group'], 'attributes_create');
		return $this->postSave();
	}


	public function update($id) {

        Allow::permission($this->module['group'], 'attributes_edit');
		return $this->postSave($id);
	}


	public function postSave($id = false){

        if (@$id)
            Allow::permission($this->module['group'], 'attributes_edit');
        else
            Allow::permission($this->module['group'], 'attributes_create');

		if(!Request::ajax())
            App::abort(404);

        if (!$id || NULL === ($element = CatalogAttribute::find($id)))
            $element = new CatalogAttribute();

        $input = Input::all();

        /**
         * Проверяем системное имя
         */
        if (!trim($input['slug'])) {
            $input['slug'] = $input['meta'][Config::get('app.locale')]['name'];
        }
        $input['slug'] = Helper::translit($input['slug']);

        /*
        $slug = $input['slug'];
        $exit = false;
        $i = 1;
        do {
            $test = CatalogAttribute::where('slug', $slug)->first();
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
        */

        /**
         * Проверяем флаг активности
         */
        $input['active'] = @$input['active'] ? 1 : NULL;

        $input['settings']['selectable'] = Input::get('settings.selectable') ? 1 : NULL;

        if (isset($input['settings']) && is_array($input['settings']))
            $input['settings'] = json_encode($input['settings']);

        #Helper::dd($input);

        $json_request['responseText'] = "<pre>" . print_r($_POST, 1) . "</pre>";
        #return Response::json($json_request,200);

        $json_request = array('status' => FALSE, 'responseText' => '', 'responseErrorText' => '', 'redirect' => FALSE);
		$validator = Validator::make($input, array('slug' => 'required'));
		if($validator->passes()) {

            #$redirect = false;

            if ($element->id > 0) {

                $element->update($input);
                $redirect = false;
                $attribute_id = $element->id;

                /**
                 * Обновим slug на форме
                 */
                if (Input::get('slug') != $input['slug']) {
                    $json_request['form_values'] = array('input[name=slug]' => $input['slug']);
                }

            } else {

                /**
                 * Ставим элемент в конец списка
                 */
                $temp = CatalogAttribute::selectRaw('max(rgt) AS max_rgt')->where('attributes_group_id', @$input['attributes_group_id'])->first();
                $input['lft'] = $temp->max_rgt+1;
                $input['rgt'] = $temp->max_rgt+2;

                $element->save();
                $element->update($input);
                $attribute_id = $element->id;
                $redirect = Input::get('redirect');
            }

            #Helper::ta($element);

            /**
             * Сохраняем META-данные
             */
            if (
                isset($input['meta']) && is_array($input['meta']) && count($input['meta'])
            ) {
                #Helper::d($attribute_id);
                foreach ($input['meta'] as $locale_sign => $meta_array) {
                    $meta_search_array = array(
                        'attribute_id' => (int)$attribute_id,
                        'language' => $locale_sign
                    );
                    #Helper::d($meta_search_array);
                    $attribute_meta = CatalogAttributeMeta::firstOrNew($meta_search_array);

                    #Helper::ta($attribute_meta);

                    if (!$attribute_meta->id) {
                        #Helper::tad($attribute_meta);
                        $attribute_meta->save();
                    }
                    $meta_array['active'] = @$meta_array['active'] ? 1 : NULL;

                    $meta_array['settings'] = @$meta_array['settings'] ? json_encode($meta_array['settings']) : NULL;
                    #$meta_array_settings = $meta_array['settings'];
                    #$meta_array['settings'] = NULL;
                    #if (in_array($element->type, array('select'))) {
                    #    $meta_array['settings']['values'] = @$meta_array_settings['values'] ?: NULL;
                    #}

                    #Helper::dd($meta_array);

                    $attribute_meta->update($meta_array);
                    unset($meta_search_array);
                    unset($meta_array);
                    unset($attribute_meta);
                }
                #Helper::d($attribute_id);
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

        Allow::permission($this->module['group'], 'attributes_delete');

		if(!Request::ajax())
            App::abort(404);

		$json_request = array('status' => FALSE, 'responseText' => '');

        /*
        $json_request['responseText'] = 'Удалено';
        $json_request['status'] = TRUE;
        return Response::json($json_request,200);
        */

        $element = CatalogAttribute::find($id);

        if (is_object($element)) {

            /**
             * Удаление:
             * - связок атрибута с товарами,
             * + мета-данных
             * + самого атрибута
             */

            $element->metas()->delete();
            $element->delete();

            /**
             * Сдвигаем атрибуты в общем дереве
             */
            if ($element->rgt)
                DB::update(DB::raw("UPDATE " . $element->getTable() . " SET lft = lft - 2, rgt = rgt - 2 WHERE lft > " . $element->rgt . ""));

            $json_request['responseText'] = 'Удалено';
            $json_request['status'] = TRUE;
        }

		return Response::json($json_request,200);
	}


    public function postAjaxNestedSetModelAttributesGroups() {

        #$input = Input::all();

        $data = Input::get('data');
        $data = json_decode($data, 1);
        #Helper::dd($data);

        $offset = 0;
        /*
        ## Отступ
        $root_id = (int)Input::get('root');
        if ($root_id > 0) {
            $root_category = CatalogAttributeGroup::find($root_id);
            if (is_object($root_category)) {
                $offset = $root_category->lft;
            }
        }
        */

        if (count($data)) {

            $id_left_right = (new NestedSetModel())->get_id_left_right($data);
            #Helper::dd($id_left_right);

            if (count($id_left_right)) {

                $list = CatalogAttributeGroup::whereIn('id', array_keys($id_left_right))->get();

                if (count($list)) {
                    foreach ($list as $lst) {
                        $lst->lft = $id_left_right[$lst->id]['left'] + $offset;
                        $lst->rgt = $id_left_right[$lst->id]['right'] + $offset;
                        $lst->save();
                    }
                }
            }
        }

        return Response::make('1');
    }


    public function postAjaxOrderSaveAttributes() {

        $poss = Input::get('poss');
        $group_id = Input::get('group_id');

        $pls = CatalogAttribute::whereIn('id', $poss)->get();

        if ( $pls ) {
            foreach ($pls as $pl) {
                $pl->rgt = (array_search($pl->id, $poss)+1) * 2;
                $pl->lft = $pl->rgt-1;
                if ($group_id)
                    $pl->attributes_group_id = $group_id;
                $pl->save();
            }
        }

        return Response::make('1');
    }

}


