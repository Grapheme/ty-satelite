<?php

class CatalogAttributeValue extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_attributes_values';

    protected $fillable = array(
        'product_id',
        'attribute_id',
        'language',
        'value',
        'settings',
        'lft',
        'rgt',
    );

	public static $rules = array(
        'product_id' => 'required',
        'attribute_id' => 'required',
        'language' => 'required',
	);


    public function attribute() {
        return $this->belongsTo('CatalogAttribute', 'attribute_id', 'id');
    }

    public function product() {
        return $this->belongsTo('CatalogProduct', 'product_id', 'id');
    }

}