<?php

class CatalogOrderProduct extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_orders_products';

    protected $fillable = array(
        'order_id',
        'product_id',
        'product_hash',
        'count',
        'price',
        'product_cache',
    );

	public static $rules = array(
        #'slug' => 'required',
	);


    public function order() {
        return $this->belongsTo('CatalogOrder', 'status_id', 'id');
    }

    public function info() {
        return $this->belongsTo('CatalogProduct', 'product_id', 'id')->withTrashed();
    }

    public function scopeAttributess() {
        #Helper::dd($this);
        return $this->hasMany('CatalogOrderProductAttribute', 'product_id', 'id')
            ->where('order_id', $this->order_id)
            ;
    }
}