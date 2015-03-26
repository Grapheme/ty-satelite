<?php

class CatalogCategoryAttribute extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_categories_attributes';

    protected $fillable = array(
        'active',
        'slug',
        'type',
        'settings',
    );

	public static $rules = array(
        #'slug' => 'required',
	);


    public function values() {
        return $this->hasMany('CatalogCategoryAttributeValue', 'attribute_id', 'id');
    }


    /**
    * Связь возвращает все META-данные записи (для всех языков)
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function metas() {
        return $this->hasMany('CatalogCategoryAttributeMeta', 'attribute_id', 'id');
    }

    /**
     * Связь возвращает META для записи, для текущего языка запроса
     *
     * @return mixed
     */
    public function meta() {
        return $this->belongsTo('CatalogCategoryAttributeMeta', 'id', 'attribute_id')
            ->where('language', Config::get('app.locale'))
        ;
    }

    /**
     * Экстрактит атрибут
     *
     * $value->extract();
     *
     * @param bool $unset
     * @return $this
     */
    public function extract($unset = false) {

        #Helper::ta($this);

        ## Extract metas
        if (isset($this->metas)) {
            foreach ($this->metas as $m => $meta) {
                $meta->extract();
                $this->metas[$meta->language] = $meta;
                if ($m != $meta->language || $m === 0)
                    unset($this->metas[$m]);
            }
        }

        ## Extract meta
        if (isset($this->meta)) {

            if (
                is_object($this->meta)
                && ($this->meta->language == Config::get('app.locale') || $this->meta->language == NULL)
            ) {
                if ($this->meta->name != '')
                    $this->name = $this->meta->name;

                if ($this->meta->settings != '') {
                    #$this->settings = json_decode($this->meta->settings, 1);
                }

            }

            #Helper::dd($this);
            if ($unset) {
                unset($this->relations['meta']);
            }
        }

        return $this;
    }
}