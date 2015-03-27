<?php

class CatalogCategoryAttributeValue extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_categories_attributes_values';

    protected $fillable = array(
        'category_id',
        'attribute_id',
        'language',
        'value',
        'settings',
    );

	public static $rules = array(
        #'slug' => 'required',
	);

    public function attribute() {
        return $this->belongsTo('CatalogCategoryAttribute', 'attribute_id', 'id')
            #->where('language', Config::get('app.locale'))
            ;
    }

}