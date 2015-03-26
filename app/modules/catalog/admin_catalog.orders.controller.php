<?php

class AdminCatalogOrdersController extends BaseController {

    public static $name = 'orders';
    public static $group = 'catalog';
    public static $entity = 'order';
    public static $entity_name = 'заказ';

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

        #/*
        Allow::permission($this->module['group'], 'orders_view');

        $elements = (new CatalogOrder)
            ->with('status.meta')
            #->with('products.info.meta')
        ;

        if (Input::get('archive') == 1) {
            $elements = $elements->onlyTrashed();
        }

        $elements = $elements->paginate(30);

        #Helper::tad($elements);

        return View::make($this->module['tpl'].'index', compact('elements'));
        #*/
	}

    /************************************************************************************/

    /*
	public function create() {

        Allow::permission($this->module['group'], 'orders_create');


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
    */
    

	public function edit($id) {

        Allow::permission($this->module['group'], 'orders_edit');

		$element = (new CatalogOrder)
            ->where('id', $id)
            ->with('status.meta')
            ->with('statuses.info.meta')
            ->with('products.info.meta')
            #->with('products_attributes.info.meta')
            ->with('products_attributes')
        ;

        if (Input::get('archive') == 1) {
            $element = $element->onlyTrashed();
        }

        $element = $element->first();

        if (!is_object($element))
            App::abort(404);

        $element->extract(true, null);

        #Helper::tad($element);

        $temp = (new CatalogOrderStatus())
            ->with('meta')
            ->orderBy('sort_order', 'ASC')
            ->get()
        ;
        $statuses = array();
        foreach ($temp as $tmp) {
            $statuses[$tmp->id] = $tmp->meta->title;
        }

        #Helper::tad($element);

        return View::make($this->module['tpl'].'edit', compact('element', 'locales', 'root_category', 'statuses'));
	}


    /************************************************************************************/


    /*
	public function store() {

        Allow::permission($this->module['group'], 'orders_create');
		return $this->postSave();
	}
    */

	public function update($id) {

        Allow::permission($this->module['group'], 'orders_edit');
		return $this->postSave($id);
	}


	public function postSave($id = false){

        if (@$id)
            Allow::permission($this->module['group'], 'orders_edit');
        else
            Allow::permission($this->module['group'], 'orders_create');

		if(!Request::ajax())
            App::abort(404);

        $input = Input::all();
        #Helper::tad($input);

        if ($id)
            $updated = Catalog::update_order($id, $input);

        $json_request = array('status' => FALSE, 'responseText' => '', 'responseErrorText' => '', 'redirect' => FALSE);
        $json_request['responseText'] = 'Сохранено';
        if (@$redirect)
            $json_request['redirect'] = $redirect;
        $json_request['status'] = TRUE;
		return Response::json($json_request, 200);
	}

    /************************************************************************************/

	public function destroy($id){

        Allow::permission($this->module['group'], 'orders_delete');

		if(!Request::ajax())
            App::abort(404);

		$json_request = array('status' => FALSE, 'responseText' => '');

        /*
        $json_request['responseText'] = 'Удалено';
        $json_request['status'] = TRUE;
        return Response::json($json_request,200);
        #*/

        $element = CatalogOrder::find($id);

        if (is_object($element)) {

            $json_request['responseText'] = 'Удалено';
            $json_request['status'] = TRUE;
            $element->delete();
        }

		return Response::json($json_request,200);
	}

}


