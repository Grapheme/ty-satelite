<?php
/**
 * Soft Delete
 * http://stackoverflow.com/questions/22426165/laravel-soft-delete-posts
 */
use Illuminate\Database\Eloquent\SoftDeletingTrait; // <-- This is required

class CatalogOrder extends BaseModel {

    protected $guarded = array();

	public $table = 'catalog_orders';

    #protected $softDelete = true;
    use SoftDeletingTrait; // <-- Use This Insteaf Of protected $softDelete = true;

    protected $fillable = array(
        'status_id',
        'total_sum',
        'client_id',
        'client_name',
        'delivery_info',
        'comment',
    );

	public static $rules = array(
        #'slug' => 'required',
	);


    public function status() {
        return $this->hasOne('CatalogOrderStatus', 'id', 'status_id');
    }

    public function statuses() {
        return $this->hasMany('CatalogOrderStatusHistory', 'order_id', 'id')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'DESC')
            ;
    }

    public function products() {
        return $this->hasMany('CatalogOrderProduct', 'order_id', 'id');
    }

    public function products_attributes() {
        return $this->hasMany('CatalogOrderProductAttribute', 'order_id', 'id');
    }

    public function extract($unset = 0) {

        /**
         * Товары
         */
        if (isset($this->relations['products']) && count($this->relations['products'])) {

            $array = new Collection();
            foreach ($this->relations['products'] as $product) {
                $array[$product->id] = $product;
            }
            $this->relations['products'] = $array;
            unset($array);

            /**
             * Атрибуты товаров
             */
            if (isset($this->relations['products_attributes']) && count($this->relations['products_attributes'])) {

                /*
                 * Собираем атрибуты дял всех товаров в один массив
                 */
                $orders = array();
                foreach ($this->products_attributes as $products_attribute) {
                    if (!isset($orders[$products_attribute->product_id]))
                        $orders[$products_attribute->product_id] = new Collection();
                    $orders[$products_attribute->product_id][$products_attribute->attribute_id] = $products_attribute;
                }

                /*
                 * Устанавливаем каждому товару его атрибуты
                 */
                if (count($orders)) {
                    foreach ($orders as $order_id => $order_attributes) {
                        if (isset($this->relations['products'][$order_id])) {
                            $this->relations['products'][$order_id]->relations['attributes'] = $order_attributes;
                        }
                    }
                }
                unset($orders);
                unset($this->relations['products_attributes']);
            }
        }


    }
}