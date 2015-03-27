<?php

class CatalogCategoryAttributeMeta extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_categories_attributes_meta';

    protected $fillable = array(
        'attribute_id',
        'language',
        'name',
        'settings',
    );

	public static $rules = array(
        #'slug' => 'required',
	);


    public function extract() {
        if (isset($this->settings) && $this->settings != '') {
            $this->settings = json_decode($this->settings, true);
        }
    }

}