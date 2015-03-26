<?php

class CatalogOrderStatusHistory extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_orders_statuses_history';

    protected $fillable = array(
        'order_id',
        'status_id',
        'info',
        'changer_id',
        'changer_name',
        'status_cache',
    );

	public static $rules = array(
        #'slug' => 'required',
	);


    public function order() {
        return $this->belongsTo('CatalogOrder', 'order_id', 'id');
    }

    public function info() {
        return $this->belongsTo('CatalogOrderStatus', 'status_id', 'id');
    }

}