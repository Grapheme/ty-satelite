<?php

class CatalogAttribute extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_attributes';

    protected $fillable = array(
        'active',
        'slug',
        'attributes_group_id',
        'type',
        'settings',
        'lft',
        'rgt',
    );

	public static $rules = array(
        #'slug' => 'required',
	);


    public function attributes_group() {
        return $this->belongsTo('CatalogAttributeGroup', 'attributes_group_id', 'id');
    }
    public function group() {
        return $this->attributes_group();
    }

    public function products() {
        return $this->hasMany('CatalogProduct', 'category_id', 'id')
            ->orderBy('lft', 'ASC')
            ;
    }

    public function values() {
        #dd($this);
        return $this->hasMany('CatalogAttributeValue', 'attribute_id', 'id');
    }

    public function value() {
        return $this->hasOne('CatalogAttributeValue', 'attribute_id', 'id')
            ->where('language', Config::get('app.locale'))
            ;
    }


    /**
    * Связь возвращает все META-данные записи (для всех языков)
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function metas() {
        return $this->hasMany('CatalogAttributeMeta', 'attribute_id', 'id');
    }

    /**
     * Связь возвращает META для записи, для текущего языка запроса
     *
     * @return mixed
     */
    public function meta() {
        return $this->hasOne('CatalogAttributeMeta', 'attribute_id', 'id')
            ->where('language', Config::get('app.locale'))
            ;
    }

    public function extract($unset = false) {

        ## Extract metas
        if (isset($this->metas)) {
            foreach ($this->metas as $m => $meta) {
                if (isset($meta->settings) && is_string($meta->settings))
                    $meta->settings = json_decode($meta->settings, 1);
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

            }

            if ($unset)
                unset($this->relations['meta']);
        }


        if (isset($this->values)) {
            #Helper::ta($this->values);

            $values = new Collection();
            foreach ($this->values as $value) {
                $values[$value->language] = $value;
            }
            unset($this->relations['values']);
            $this->relations['values'] = $values;
        }


        ## Extract attributes_group
        if (isset($this->attributes_group)) {

            if (
                is_object($this->attributes_group->meta)
                && ($this->attributes_group->meta->language == Config::get('app.locale') || $this->attributes_group->meta->language == NULL)
            ) {
                if ($this->attributes_group->meta->name != '')
                    $this->attributes_group->name = $this->attributes_group->meta->name;

            }

            if ($unset)
                unset($this->attributes_group->relations['meta']);




            ## Extract attributes_group
            if (isset($this->attributes_group->category)) {

                if (
                    is_object($this->attributes_group->category->meta)
                    && ($this->attributes_group->category->meta->language == Config::get('app.locale') || $this->attributes_group->category->meta->language == NULL)
                ) {
                    if ($this->attributes_group->category->meta->name != '')
                        $this->attributes_group->category->name = $this->attributes_group->category->meta->name;

                }

                if ($unset)
                    unset($this->attributes_group->category->relations['meta']);
            }

        }

        return $this;
    }

}