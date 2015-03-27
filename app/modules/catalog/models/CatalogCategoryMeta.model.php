<?php

class CatalogCategoryMeta extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_categories_meta';

    protected $fillable = array(
        'category_id',
        'language',
        'active',
        'name',
        'settings',
    );

	public static $rules = array(
        'category_id' => 'required',
        'language' => 'required',
	);

    public function attributes_values() {
        return $this->hasMany('CatalogCategoryAttributeValue', 'category_id', 'id');
    }

    public function extract() {

        /*
        ## Extract metas
        if (isset($this->attributes_values)) {
            $temp = new Collection();
            $temp_arr = [];
            foreach ($this->attributes_values as $a => $attr_value) {

                if (!isset($temp_arr[$attr_value->language]))
                    $temp_arr[$attr_value->language] = array();

                $temp_arr[$attr_value->language][$attr_value->attribute_id] = $attr_value->value;
            }
            if (count($temp_arr)) {
                foreach($temp_arr as $t => $arr) {
                    $temp[$t] = $arr;
                }
            }
            $this->relations['attributes_values'] = $temp;
        }
        */

        return $this;
    }

}