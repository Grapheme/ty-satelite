<?php

class CatalogAttributeMeta extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_attributes_meta';

    protected $fillable = array(
        'attribute_id',
        'language',
        'active',
        'name',
        'settings',
    );

	public static $rules = array(
        'attribute_id' => 'required',
        'language' => 'required',
	);


    public function extract() {

        if (isset($this->settings) && $this->settings != '') {
            $this->settings = json_decode($this->settings);
        }
    }

}