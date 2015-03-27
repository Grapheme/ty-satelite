<?php
/**
 * Soft Delete
 * http://stackoverflow.com/questions/22426165/laravel-soft-delete-posts
 */
use Illuminate\Database\Eloquent\SoftDeletingTrait; // <-- This is required

class CatalogProduct extends BaseModel {

    protected $guarded = array();

	public $table = 'catalog_products';

    #protected $softDelete = true;
    use SoftDeletingTrait; // <-- Use This Insteaf Of protected $softDelete = true;

    protected $fillable = array(
        'active',
        'slug',
        'category_id',
        'article',
        'amount',
        'image_id',
        'gallery_id',
        'settings',
        'lft',
        'rgt',
    );

	public static $rules = array(
        #'slug' => 'required',
	);



    public function attr($group, $attr, $return = 'value') {
        /*
        $attr_exists =
            isset($this->attributes_groups) && is_object($this->attributes_groups)
            && isset($this->attributes_groups[$group]) && is_object($this->attributes_groups[$group])
            && isset($this->attributes_groups[$group]->relations['attributes']) && is_object($this->attributes_groups[$group]->relations['attributes'])
            && isset($this->attributes_groups[$group]->relations['attributes'][$attr]) && is_object($this->attributes_groups[$group]->relations['attributes'][$attr])
            && isset($this->attributes_groups[$group]->relations['attributes'][$attr]->values) && is_object($this->attributes_groups[$group]->relations['attributes'][$attr]->values)
            && isset($this->attributes_groups[$group]->relations['attributes'][$attr]->values[Config::get('app.locale')]) && is_object($this->attributes_groups[$group]->relations['attributes'][$attr]->values[Config::get('app.locale')])
        ;

        #dd($this->attributes_groups[$group]->relations['attributes'][$attr]);
        */

        /*
        Helper::ta($this);
        Helper::ta($attr_exists);
        Helper::ta( $group . ' :: ' . $attr . ' => ' . $this->attributes_groups[$group]->relations['attributes'][$attr]->values[Config::get('app.locale')]->value);
        #*/

        $attr_exists =
            isset($this->values) && is_object($this->values)
            && isset($this->values[$group]) && is_object($this->values[$group])
            && isset($this->values[$group][$attr]) && is_object($this->values[$group][$attr])
        ;

        #Helper::dd($this->values[$group]);
        #dd(isset($this->values[$group][$attr]) && is_object($this->values[$group][$attr]));

        $ret = NULL;
        if ($attr_exists) {
            $obj = $this->values[$group][$attr];
            if ($return == 'value')
                $ret = $obj->value;
            else
                $ret = $obj;
        }

        return $ret;
    }


    public function attributes_groups() {
        return $this->hasMany('CatalogAttributeGroup', 'category_id', 'category_id')
            ->orderBy('lft', 'ASC')
            ;
    }


    public function category() {
        return $this->belongsTo('CatalogCategory', 'category_id', 'id')
            ->with('meta')
            ;
    }

    /**
     * Значения всех атрибутов товара
     */
    public function values() {
        return $this->hasMany('CatalogAttributeValue', 'product_id', 'id');
    }

    /*
    public function attributes_meta() {
        return $this->hasOne('CatalogProductMeta', 'product_id', 'id')
            ->where('language', Config::get('app.locale'))
            ;
    }
    */

    /**
    * Связь возвращает все META-данные записи (для всех языков)
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function metas() {
        return $this->hasMany('CatalogProductMeta', 'product_id', 'id')->withTrashed();
    }

    /**
     * Связь возвращает META для записи, для текущего языка запроса
     *
     * @return mixed
     */
    public function meta() {
        return $this->belongsTo('CatalogProductMeta', 'id', 'product_id')
            ->where('language', Config::get('app.locale'))
            ->withTrashed()
            ;
    }

    /**
     * Возвращает SEO-данные записи, для текущего языка запроса
     *
     * @return mixed
     */
    public function seo() {
        return $this->belongsTo('Seo', 'unit_id', 'id')
            ->where('module', 'CatalogProductMeta')
            ->where('language', Config::get('app.locale'))
            ;
    }

    /**
     * Связь возвращает все SEO-данные записи, для каждого из языков
     *
     * @return mixed
     */
    public function seos() {
        return $this->hasMany('Seo', 'unit_id', 'id')
            ->where('module', 'CatalogProduct')
            ;
    }

    /**
     * Экстрактит запись
     *
     * $value->extract();
     *
     * @param bool $unset
     * @return $this
     */
    public function extract($unset = false) {

        #Helper::tad($this);

        ## Extract category
        if (isset($this->relations['category']) && is_object($this->relations['category'])) {
            $this->relations['category'] = $this->relations['category']->extract($unset);
        }


        ## Extract metas
        if (isset($this->metas)) {
            foreach ($this->metas as $m => $meta) {
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

                if ($this->meta->description != '')
                    $this->description = $this->meta->description;

                if ($this->meta->price != '')
                    $this->price = $this->meta->price;

            }

            if ($unset)
                unset($this->meta);
        }


        ## Extract SEOs
        if (isset($this->seos)) {
            #Helper::tad($this->seos);
            if (count($this->seos) == 1 && count(Config::get('app.locales')) == 1) {
                $app_locales = Config::get('app.locales');
                foreach ($app_locales as $locale_sign => $locale_name)
                    break;
                foreach ($this->seos as $s => $seo) {
                    $this->seos[$locale_sign] = $seo;
                    break;
                }
                unset($this->seos[0]);
                #Helper::tad($this->seos);
            } else {
                foreach ($this->seos as $s => $seo) {
                    $this->seos[$seo->language] = $seo;
                    #Helper::d($s . " != " . $seo->language);
                    if ($s != $seo->language || $s === 0)
                        unset($this->seos[$s]);
                }
            }
        }



        #$this->checksum = rand(99, 999);




        /**
         * Значения атрибутов
         */
        if (isset($this->relations['values']) && is_object($this->relations['values']) && count($this->relations['values'])) {

            $temp = new Collection();
            $temps = [];

            foreach($this->relations['values'] as $value) {

                if (
                    !isset($value->attribute) || !is_object(($value->attribute)) || !$value->attribute->slug
                    || !isset($value->attribute->group) || !is_object(($value->attribute->group)) || !$value->attribute->group->slug
                )
                    continue;

                $temp_value = clone $value;
                unset($temp_value->relations['attribute']);

                if (!isset($temps[$value->attribute->group->slug]))
                    $temps[$value->attribute->group->slug] = new Collection();

                $temps[$value->attribute->group->slug][$value->attribute->slug] = $temp_value;

                #$temp[$value->attribute->group->slug][$value->attribute->slug] = $temp_value;
            }

            if (count($temps)) {
                #Helper::tad($temps);
                foreach ($temps as $t => $tmp) {
                    $temp[$t] = $tmp;
                }
            }

            $this->relations['values'] = $temp;
        }





        /**
         * Группы атрибутов и атрибуты - по ID
         */
        if (isset($this->relations['attributes_groups']) && is_object($this->relations['attributes_groups']) && count($this->relations['attributes_groups']) && 0) {

            #echo "!!!!!";

            $attributes_groups = new Collection();
            foreach ($this->relations['attributes_groups'] as $ag => $attributes_group) {

                $temp = clone($attributes_group);
                $temp->extract($unset);
                #Helper::ta($temp->relations);

                if (is_object($temp) && isset($temp->relations['attributes']) && count($temp->relations['attributes'])) {

                    $attributes = new Collection();
                    foreach ($temp->relations['attributes'] as $ra => $attribute) {

                        $attribute = $attribute->extract($unset);

                        /**
                         * Правильное обновление значения элемента коллекции
                         */
                        $attributes->put($attribute->id, $attribute);
                    }
                    unset($temp->relations['attributes']);
                    $temp->relations['attributes'] = $attributes;
                }
                #Helper::ta($temp->relations);

                /**
                 * Правильное обновление значения элемента коллекции
                 */
                $attributes_groups->put($attributes_group->id, $temp);
                unset($attributes_group);
                unset($temp);
            }
            $this->relations['attributes_groups'] = $attributes_groups;
            #$this->attributes_groups = $attributes_groups;
            #Helper::tad($this->attributes_groups);
        }

        /**
         * Значения атрибутов
         */

        /*
        Helper::ta("VALUES:");
        Helper::ta(@$this->relations['values']);
        Helper::ta('values: ' . isset($this->relations['values']) . ' && ' . is_object($this->relations['values']) . ' && ' . count($this->relations['values']));
        #*/

        if (0) {
        #if (isset($this->relations['values']) && is_object($this->relations['values']) && count($this->relations['values']) && 1) {

            #dd($this);

            #Helper::ta($this);
            #Helper::ta($this->relations['values']);

            $temp = new Collection();

            /**
             * Перебираем все группы атрибутов
             */
            Helper::ta('attributes_groups: ' . isset($this->relations['attributes_groups']). ' && ' .is_object($this->relations['attributes_groups']). ' && ' .count($this->relations['attributes_groups']));

            if (isset($this->relations['attributes_groups']) && is_object($this->relations['attributes_groups']) && count($this->relations['attributes_groups'])) {

                foreach ($this->relations['attributes_groups'] as $ag => $attributes_group) {

                    /**
                     * Перебираем все атрибуты в группе
                     */
                    Helper::ta('group-' . $attributes_group->id . ', attributes: ' . isset($attributes_group->relations['attributes']). ' && ' . is_object($attributes_group->relations['attributes']). ' && ' . count($attributes_group->relations['attributes']));

                    if (isset($attributes_group->relations['attributes']) && is_object($attributes_group->relations['attributes']) && count($attributes_group->relations['attributes'])) {

                        foreach ($attributes_group->relations['attributes'] as $a => $attribute) {

                            #Helper::ta($attribute);

                            unset($this->relations['attributes_groups'][$ag]->relations['attributes'][$a]->relations['value']);
                            unset($this->relations['attributes_groups'][$ag]->relations['attributes'][$a]->relations['values']);

                            /**
                             * Перебираем все значения атрибутов товара
                             */
                            if (isset($this->relations['values']) && is_object($this->relations['values']) && count($this->relations['values']) && 1) {

                                foreach ($this->relations['values'] as $v => $value) {

                                    if ($attribute->id == $value->attribute_id) {

                                        #if (!isset($this->relations['attributes_groups'][$ag]->relations['attributes'][$a]->relations['values']))
                                        #    $this->relations['attributes_groups'][$ag]->relations['attributes'][$a]->relations['values'] = new Collection();
                                        #$this->relations['attributes_groups'][$ag]->relations['attributes'][$a]->relations['values'][$value->language] = $value;

                                        #Helper::ta($this->id . ' / ' . $ag . ' / ' . $a . ' => ' . $value->value);

                                        if ($value->language == Config::get('app.locale')) {
                                            $this->relations['attributes_groups'][$ag]->relations['attributes'][$a]->relations['value'] = $value;
                                        }

                                        $temp[] = $this->relations['values'][$v];
                                        unset($this->relations['values'][$v]);
                                    }
                                }
                            }
                        }
                    }

                }
            }

            #Helper::ta($this);
            #echo "<hr/>";
            $this->relations['values'] = $temp;
            unset($temp);
        }

        #Helper::ta($this);





        /**
         * Группы атрибутов и атрибуты - по SLUG
         */
        if (isset($this->attributes_groups) && is_object($this->attributes_groups) && count($this->attributes_groups) && 1) {

            #Helper::tad($this->relations['attributes_groups']);

            $attributes_groups = new Collection();
            foreach ($this->relations['attributes_groups'] as $ag => $attributes_group) {

                $temp = $attributes_group->extract($unset);
                #Helper::ta($temp->relations);

                if (is_object($temp) && @count($temp->relations['attributes'])) {

                    $attributes = new Collection();
                    foreach ($temp->relations['attributes'] as $ra => $attribute) {

                        $attribute = $attribute->extract($unset);

                        /**
                         * Правильное обновление значения элемента коллекции
                         */
                        $attributes->put($attribute->slug, $attribute);
                    }
                    $temp->relations['attributes'] = $attributes;
                }
                #Helper::ta($temp->relations);

                /**
                 * Правильное обновление значения элемента коллекции
                 */
                $attributes_groups->put($attributes_group->slug, $temp);
            }
            $this->relations['attributes_groups'] = $attributes_groups;
            #Helper::tad($this->attributes_groups);
        }

        #Helper::tad($this);

        return $this;
    }


    ####################################################################################################################


    public function full_delete() {

        #Helper::tad($this);

        /**
         * Если категории не существует - ничего не будем делать
         */
        if (!$this->id)
            return false;

        /**
         * Alias
         */
        $element = $this;

        /**
         * Удаление:
         * + SEO-данных,
         * + мета-данных
         * + значений атрибутов товара
         * + фото, галерея с фотографиями
         * + самого товара
         */

        /**
         * SEO
         */
        if (Allow::module('seo')) {
            Seo::where('module', 'CatalogProduct')
                ->where('unit_id', $element->id)
                ->delete()
            ;
        }

        $element->metas()->delete();

        $element->values()->delete();

        if ($element->image_id) {
            Photo::where('id', $element->image_id)->delete();
        }
        if ($element->gallery_id) {
            Photo::where('gallery_id', $element->gallery_id)->delete();
            Gallery::where('id', $element->gallery_id)->delete();
        }

        $element->delete();

        /**
         * Делаем сдвиг в общем дереве товаров
         */
        if ($element->rgt)
            DB::update(DB::raw("UPDATE " . $element->getTable() . " SET lft = lft - 2, rgt = rgt - 2 WHERE lft > " . $element->rgt . ""));

        return true;
    }
}