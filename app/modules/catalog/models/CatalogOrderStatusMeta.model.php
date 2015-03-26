<?php

class CatalogOrderStatusMeta extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_orders_statuses_meta';

    protected $fillable = array(
        'status_id',
        'language',
        'title',
    );

	public static $rules = array(
        #'slug' => 'required',
	);


    public function status() {
        return $this->belongsTo('CatalogOrderStatus', 'status_id', 'id');
    }

}