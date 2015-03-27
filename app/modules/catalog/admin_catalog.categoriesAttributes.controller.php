<?php

class AdminCatalogCategoriesAttributesController extends BaseController {

    public static $name = 'category_attributes';
    public static $group = 'catalog';
    public static $entity = 'attribute';
    public static $entity_name = 'атрибут';

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

        /**
         * Типы атрибутов
         */
        $this->types = array(
            'checkbox' => 'Чекбокс',
            'text' => 'Текстовая строка',
            'textarea' => 'Многострочный текст',
            #'wysiwyg' => 'WYSIWYG редактор текста',
            'select' => 'Список',
        );
	}

	public function index() {

        Allow::permission($this->module['group'], 'categories_view');

        /**
         * Поулчаем атрибуты
         */
        $elements = (new CatalogCategoryAttribute())
            ->with(['meta'])
            ->references('meta')
            ->orderBy('meta.name')
            ->where('meta.language', Config::get('app.locale'))
            ->get()
        ;
        #Helper::smartQueries(1);
        #Helper::tad($elements);

        $elements = DicLib::extracts($elements, null, true, false);
        $elements = Dic::modifyKeys($elements, 'slug');
        #Helper::smartQueries(1);
        #Helper::tad($elements);

        return View::make($this->module['tpl'].'index', compact('elements'));
	}

    /************************************************************************************/

	public function create() {

        Allow::permission($this->module['group'], 'categories_create');

        /**
         * Новый (пустой) атрибут категории
         */
        $element = new CatalogCategoryAttribute();

        /**
         * Локали
         */
        $locales = Config::get('app.locales');

        $types = $this->types;

        return View::make($this->module['tpl'].'edit', compact('element', 'locales', 'types'));
	}
    

	public function edit($id) {

        Allow::permission($this->module['group'], 'categories_edit');

		$element = CatalogCategoryAttribute::where('id', $id)
            ->with(['metas'])
            ->first()
        ;

        if (!is_object($element))
            App::abort(404);

        if (is_object($element->meta))
            $element->name = $element->meta->name;

        $element->extract();

        $locales = Config::get('app.locales');

        #Helper::tad($element);

        $types = $this->types;

        return View::make($this->module['tpl'].'edit', compact('element', 'locales', 'types'));
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

        if (!$id || NULL === ($element = CatalogCategoryAttribute::find($id)))
            $element = new CatalogCategoryAttribute();

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
            $test = CatalogCategoryAttribute::where('slug', $slug)->first();
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
        #Helper::tad($input);

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

                $element->save();
                $element->update($input);
                $attribute_id = $element->id;

                $redirect = Input::get('redirect');
            }


            /**
             * Сохраняем META-данные
             */
            if (
                isset($input['meta']) && is_array($input['meta']) && count($input['meta'])
            ) {
                foreach ($input['meta'] as $locale_sign => $meta_array) {
                    $meta_search_array = array(
                        'attribute_id' => $attribute_id,
                        'language' => $locale_sign
                    );
                    #$meta_array['active'] = @$meta_array['active'] ? 1 : NULL;
                    $category_attribute_meta = CatalogCategoryAttributeMeta::firstOrNew($meta_search_array);
                    if (!$category_attribute_meta->id)
                        $category_attribute_meta->save();

                    /**
                     * Значения атрибута, если он их имеет
                     */
                    $meta_array['settings'] = @$meta_array['settings'] ? json_encode($meta_array['settings']) : NULL;

                    $category_attribute_meta->update($meta_array);
                    unset($category_attribute_meta);
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

        $element = CatalogCategoryAttribute::where('id', $id)->with(['metas', 'values'])->first();

        #Helper::tad($element);

        if (is_object($element)) {
            /**
             * Удаление:
             * + значений атрибутов,
             * + мета-данных
             * + и самого атрибута
             */

            $element->values()->delete();
            $element->metas()->delete();
            $element->delete();

            $json_request['responseText'] = 'Удалено';
            $json_request['status'] = TRUE;
        }

		return Response::json($json_request,200);
	}

}


