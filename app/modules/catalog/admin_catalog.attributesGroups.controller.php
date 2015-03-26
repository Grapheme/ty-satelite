<?php

class AdminCatalogAttributesGroupsController extends BaseController {

    public static $name = 'attributes_groups';
    public static $group = 'catalog';
    public static $entity = 'attributes_group';
    public static $entity_name = 'группа атрибутов';

    /****************************************************************************/

    ## Routing rules of module
    public static function returnRoutes($prefix = null) {
        $class = __CLASS__;
        $entity = self::$entity;

        Route::group(array('before' => 'auth', 'prefix' => $prefix . "/" . $class::$group), function() use ($class, $entity) {

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

        /*
        Allow::permission($this->module['group'], 'attributes_view');

        $root_category = NULL;
        if (NULL !== ($cat_id = Input::get('category'))) {

            $root_category = CatalogCategory::where('id', $cat_id)
                ->with(['meta', 'attributes_groups.meta', 'attributes_groups.attributes.meta'])
                ->first()
            ;

            #Helper::tad($root_category);

            if (is_object($root_category))
                $root_category = $root_category->extract(1);
        }

        #Helper::tad($root_category);

        return View::make($this->module['tpl'].'index', compact('root_category'));
        */
	}

    /************************************************************************************/

	public function create() {

        Allow::permission($this->module['group'], 'attributes_create');


        $root_category = NULL;
        if (NULL !== ($cat_id = Input::get('category'))) {

            $root_category = CatalogCategory::where('id', $cat_id)
                ->with('meta')
                ->first()
            ;

            #Helper::tad($root_category);

            if (is_object($root_category))
                $root_category = $root_category->extract(1);
        }

        #Helper::tad($root_category);

        if (!is_object($root_category))
            App::abort(404);

        $element = new CatalogAttributeGroup();

        $locales = Config::get('app.locales');

		return View::make($this->module['tpl'].'edit', compact('element', 'locales', 'root_category'));
	}
    

	public function edit($id) {

        Allow::permission($this->module['group'], 'attributes_edit');

		$element = CatalogAttributeGroup::where('id', $id)
            ->with(['metas', 'meta', 'category.meta'])
            ->first()
            ->extract(1);

        #Helper::tad($element);

        $root_category = $element->category;

        $locales = Config::get('app.locales');

        #Helper::tad($element);

        return View::make($this->module['tpl'].'edit', compact('element', 'locales', 'root_category'));
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

        if (!$id || NULL === ($element = CatalogAttributeGroup::find($id)))
            $element = new CatalogAttributeGroup();

        $input = Input::all();

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
            $test = CatalogAttributeGroup::where('slug', $slug)->first();
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
                $attributes_group_id = $element->id;

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
                $temp = CatalogAttributeGroup::selectRaw('max(rgt) AS max_rgt')->where('category_id', @$input['category_id'])->first();
                $input['lft'] = $temp->max_rgt+1;
                $input['rgt'] = $temp->max_rgt+2;

                $element->save();
                $element->update($input);
                $attributes_group_id = $element->id;
                $redirect = Input::get('redirect');
            }

            /**
             * Сохраняем META-данные
             */
            if (
                isset($input['meta']) && is_array($input['meta']) && count($input['meta'])
            ) {
                #Helper::d($attribute_id);
                foreach ($input['meta'] as $locale_sign => $meta_array) {
                    $meta_search_array = array(
                        'attributes_group_id' => (int)$attributes_group_id,
                        'language' => $locale_sign
                    );
                    #Helper::d($meta_search_array);
                    $attribute_meta = CatalogAttributeGroupMeta::firstOrNew($meta_search_array);
                    if (!$attribute_meta->id) {
                        #Helper::tad($attribute_meta);
                        $attribute_meta->save();
                    }
                    $meta_array['active'] = @$meta_array['active'] ? 1 : NULL;
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
        #*/

        $element = CatalogAttributeGroup::find($id);

        if (is_object($element)) {

            /**
             * Удаление:
             * - мета-данных
             * - самой группы
             */

            $element->metas()->delete();
            $element->delete();

            /**
             * Сдвигаем группы атрибудтов в общем дереве
             */
            if ($element->rgt)
                DB::update(DB::raw("UPDATE " . $element->getTable() . " SET lft = lft - 2, rgt = rgt - 2 WHERE lft > " . $element->rgt . ""));

            $json_request['responseText'] = 'Удалено';
            $json_request['status'] = TRUE;
        }

		return Response::json($json_request,200);
	}

}


