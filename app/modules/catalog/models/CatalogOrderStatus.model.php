<?php

class CatalogOrderStatus extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_orders_statuses';

    protected $fillable = array(
    );

	public static $rules = array(
        #'slug' => 'required',
	);


    public function status() {
        return $this->hasOne('CatalogOrderStatus', 'status_id', 'id');
    }

    /**
     * Связь возвращает все META-данные записи (для всех языков)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function metas() {
        return $this->hasMany('CatalogOrderStatusMeta', 'status_id', 'id');
    }

    /**
     * Связь возвращает META для записи, для текущего языка запроса
     *
     * @return mixed
     */
    public function meta() {
        return $this->hasOne('CatalogOrderStatusMeta', 'status_id', 'id')
            ->where('language', Config::get('app.locale'))
            ;
    }


}