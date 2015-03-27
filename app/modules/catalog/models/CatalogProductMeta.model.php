<?php
/**
 * Soft Delete
 * http://stackoverflow.com/questions/22426165/laravel-soft-delete-posts
 */
use Illuminate\Database\Eloquent\SoftDeletingTrait; // <-- This is required

class CatalogProductMeta extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_products_meta';

    #protected $softDelete = true;
    use SoftDeletingTrait; // <-- Use This Insteaf Of protected $softDelete = true;

    protected $fillable = array(
        'product_id',
        'language',
        'active',
        'name',
        'description',
        'full_description',
        'price',
        'settings',
    );

	public static $rules = array(
        'product_id' => 'required',
        'language' => 'required',
	);

}