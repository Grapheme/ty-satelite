<?php

class CatalogCategory extends BaseModel {

	protected $guarded = array();

	public $table = 'catalog_categories';

    protected $fillable = array(
        'active',
        'slug',
        'image_id',
        'settings',
        'lft',
        'rgt',
    );

	public static $rules = array(
        #'slug' => 'required',
	);

    public function products() {
        return $this->hasMany('CatalogProduct', 'category_id', 'id')
            ->orderBy('lft', 'ASC')
            ;
    }

    /**
     * Группы атрибутов для товаров внутри категории
     */

    public function attributes_groups() {
        return $this->hasMany('CatalogAttributeGroup', 'category_id', 'id')
            ->orderBy('lft', 'ASC')
            ;
    }


    /**
     * Атрибуты категории
     */

    public function category_attributes_values() {
        return $this->hasMany('CatalogCategoryAttributeValue', 'category_id', 'id')
            ->with('attribute')
            ;
    }

    public function category_attributes_value() {
        return $this->hasMany('CatalogCategoryAttributeValue', 'category_id', 'id')
            ->where('language', Config::get('app.locale'))
            ->with('attribute')
            ;
    }


    /**
     * Подключаем к выборке значение атрибута.
     *
     * ПРИМЕРЫ ВЫЗОВА:
     *
     * Выведет только категории, у которых отмечен атрибут mainpage:
     * ВАЖНО! Having выполняется непосредственно ПЕРЕД выдачей результатов выборки юзеру.
     * Т.е. на момент срабатывания HAVING все условия для выборки уже учтены,
     * и никакой оптимизации проводиться не будет - MySQL просто фильтрует результаты!
     *

        $categories = (new CatalogCategories())
            ->left_join_attr('mainpage')
            ->having('mainpage', '=', 1)
            ->get()
         ;

     *
     * Выведет только категории, у которых атрибут min_price больше 1000:
     * ВАЖНО! Использовать нужно именно inner_join_attr в связке с $join->ON, а не $join->WHERE
     *

        $categories = (new CatalogCategories())
            ->inner_join_attr('min_price', function($join, $value){
                $join->on($value, '>', DB::raw(1000));
            })
            ->get()
         ;

     */

    public function scopeInner_join_attr($query, $attr_name, $additional_rules = null) {
        return $this->scopeJoin_attr($query, $attr_name, $additional_rules, 'join');
    }

    public function scopeLeft_join_attr($query, $attr_name, $additional_rules = null) {
        return $this->scopeJoin_attr($query, $attr_name, $additional_rules, 'leftJoin');
    }

    public function scopeJoin_attr($query, $attr_name, $additional_rules = null, $method = 'leftJoin') {
        #dd($attr_name);
        $tbl_cc = $this->getTable();
        $tbl_cca = (new CatalogCategoryAttribute())->getTable();
        $tbl_ccav = (new CatalogCategoryAttributeValue())->getTable();
        $tbl_ccav_alias = md5(time() . '_' . rand(999999, 9999999));

        $attr = (new CatalogCategoryAttribute())
            ->where('slug', $attr_name)
            ->first();

        if (isset($attr) && is_object($attr) && $attr->id) {

            $query->$method(DB::raw('`' . $tbl_ccav . '` AS `' . $tbl_ccav_alias . '`'), function($join) use ($attr, $tbl_cc, $tbl_cca, $tbl_ccav, $tbl_ccav_alias, $additional_rules) {
                $join->on($tbl_ccav_alias.'.category_id', '=', $tbl_cc.'.id');
                $join->on($tbl_ccav_alias.'.attribute_id', '=', DB::raw($attr->id));
                $join->on($tbl_ccav_alias.'.language', '=', DB::raw("'".Config::get('app.locale')."'"));
                $join->on($tbl_ccav_alias.'.value', DB::raw('IS NOT'), DB::raw('NULL'));
                #$join->on($tbl_ccav_alias.'.value', '>', DB::raw(100));

                /**
                 * Не всегда JOIN с доп.условиями приводит к ожидаемому результату...
                 */
                if (is_callable($additional_rules)) {
                    /**
                     * Правильный способ применения доп. условий через функцию-замыкание
                     */
                    call_user_func($additional_rules, $join, DB::raw('`' . $tbl_ccav_alias . '`.`value`'));
                }

            });

            $query->addSelect(DB::raw('`'.$tbl_ccav_alias.'`.`value` AS "' . $attr_name . '"'));
        }

        return $query;
    }

    /**
     * DON'T USE IT!
     */
    /*
    public function scopeLeft_join_attrs($query, $attrs_name) {
        #dd($attr_name);
        $tbl_cc = $this->getTable();
        $tbl_cca = (new CatalogCategoryAttribute())->getTable();
        $tbl_ccav = (new CatalogCategoryAttributeValue())->getTable();
        $tbl_ccav_alias = md5(time() . '_' . rand(999999, 9999999));

        $attrs = (new CatalogCategoryAttribute())
            ->whereIn('slug', (array)$attrs_name)
            ->get();

        $attrs = Dic::modifyKeys($attrs, 'slug');
        #Helper::tad($attrs);

        $attrs_ids = Dic::makeLists($attrs, null, 'id');
        #Helper::tad($attrs_ids);

        if (isset($attrs) && is_object($attrs) && count($attrs)) {

            $query->leftJoin(DB::raw('`' . $tbl_ccav . '` AS `' . $tbl_ccav_alias . '`'), function($join) use ($attrs, $attrs_ids, $tbl_cc, $tbl_cca, $tbl_ccav, $tbl_ccav_alias) {
                $join->on($tbl_ccav_alias.'.category_id', '=', $tbl_cc.'.id');
                $join->on($tbl_ccav_alias.'.language', '=', DB::raw("'".Config::get('app.locale')."'"));

                $i = 0;
                foreach ($attrs_ids as $attr_id) {
                    ++$i;
                    $method = ($i == 1) ? 'where' : 'orWhere';
                    $join->$method($tbl_ccav_alias.'.attribute_id', '=', $attr_id);
                }

            });
            #$query->whereIn($tbl_ccav_alias.'.attribute_id', $attrs_ids);

            #foreach ($attrs as $attr) {
            #    $query->addSelect(DB::raw($tbl_ccav_alias.'.value AS "' . $attr->slug . '"'));
            #}

            $query->addSelect(DB::raw($tbl_ccav_alias.'.*'));

        }

        return $query;
    }
    */


    /**
     * Возвращает значение атрибута $attr_name, если оно было установлено для категории
     *
     * ПРИМЕР:
     * return $category->attr_value('min_price');
     *
     * @param $attr_name
     * @return null
     */
    public function attr_value($attr_name) {
        $value = NULL;
        if (isset($this->category_attributes_value) && isset($this->category_attributes_value[$attr_name]))
            $value = $this->category_attributes_value[$attr_name];
        return $value;
    }


    /**
    * Связь возвращает все META-данные записи (для всех языков)
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function metas() {
        return $this->hasMany('CatalogCategoryMeta', 'category_id', 'id');
    }

    /**
     * Связь возвращает META для записи, для текущего языка запроса
     *
     * @return mixed
     */
    public function meta() {
        return $this->hasOne('CatalogCategoryMeta', 'category_id', 'id')
            ->where('language', Config::get('app.locale'))
            ;
    }

    /**
     * Возвращает SEO-данные записи, для текущего языка запроса
     *
     * @return mixed
     */
    public function seo() {
        return $this->hasOne('Seo', 'unit_id', 'id')
            ->where('module', 'CatalogCategory')
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
            ->where('module', 'CatalogCategory')
            ;
    }

    /**
     * Экстрактит категорию
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
                #var_dump($meta);
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

            #Helper::dd($this);
            if ($unset) {
                unset($this->relations['meta']);
            }
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

        ## Extract attributes_groups
        if (isset($this->attributes_groups) && is_object($this->attributes_groups) && count($this->attributes_groups)) {

            #Helper::tad($this->relations['attributes_groups']);

            $attributes_groups = new Collection();
            foreach ($this->relations['attributes_groups'] as $ag => $attributes_group) {

                $temp = $attributes_group->extract($unset);
                #Helper::ta($temp->relations);

                if (is_object($temp) && @count($temp->relations['attributes'])) {

                    $attributes = new Collection();
                    foreach ($temp->relations['attributes'] as $ra => $attribute) {

                        #Helper::ta($attribute);

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

            ## Extract attributes count
            $count = 0;
            if (isset($this->attributes_groups) && is_object($this->attributes_groups) && count($this->attributes_groups)) {
                foreach ($this->attributes_groups as $group) {
                    #Helper::dd($group->relations['attributes']);
                    if (isset($group->relations['attributes']) && is_object($group->relations['attributes']) && count($group->relations['attributes'])) {
                        $count += count($group->relations['attributes']);
                    }
                }
            }
            $this->attributes_count = $count;
        }



        ## Extract products
        if (isset($this->products)) {

            $products = new Collection();

            foreach ($this->products as $p => $product) {

                $product->extract($unset);
                $products[$product->id] = $product;
            }

            $products = DicLib::loadImages($products, ['image_id']);
            $products = DicLib::loadGallery($products, ['gallery_id']);

            #dd($products);

            $this->relations['products'] = $products;
            #unset($products);
        }

        ## Extract category attributes value
        if (isset($this->category_attributes_value)) {

            #Helper::ta($this->category_attributes_value);

            $category_attributes_value = new Collection();

            foreach ($this->category_attributes_value as $v => $value) {

                #$value->extract($unset);
                #$category_attributes_value[$value->id] = $value;

                if (is_object($value) && isset($value->attribute) && is_object($value->attribute) && $value->attribute->slug) {
                    $category_attributes_value[$value->attribute->slug] = $value->value;
                }

            }

            #Helper::tad($category_attributes_value);

            $this->relations['category_attributes_value'] = $category_attributes_value;
            #$this->category_attributes_value = $category_attributes_value;
        }



        ## Extract category attributes value
        if (isset($this->category_attributes_values)) {

            #Helper::ta($this->category_attributes_value);

            $category_attributes_values = new Collection();
            $category_attributes_values_arr = [];

            foreach ($this->category_attributes_values as $v => $value) {

                #$value->extract($unset);
                #$category_attributes_value[$value->id] = $value;

                if (is_object($value) && isset($value->attribute) && is_object($value->attribute) && $value->attribute->slug) {
                    $category_attributes_values_arr[$value->language][$value->attribute->slug] = $value->value;
                }

            }

            #Helper::tad($category_attributes_values_arr);
            #Helper::tad($category_attributes_value);

            if (count($category_attributes_values_arr)) {
                foreach ($category_attributes_values_arr as $locale_sign => $temp) {
                    $category_attributes_values[$locale_sign] = $temp;
                }
            }

            $this->relations['category_attributes_values'] = $category_attributes_values;
            #$this->category_attributes_value = $category_attributes_value;
        }




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
         * !!! товаров категории - ЗАПРЕТ!
         * + атрибуты/группы атрибутов товаров
         * + значения атрибутов категорий
         * + SEO-данных категории
         * + мета-данных
         * + фото
         * + и самой категории
         */

        /**
         * Загрузим нужные связи
         */
        $element->load('attributes_groups.attributes');

        #Helper::ta($element);

        if (
            isset($element->relations['attributes_groups'])
            && is_object($element->relations['attributes_groups'])
            && $element->relations['attributes_groups']->count()
        ) {

            /**
             * Получим IDs атрибутов/групп атрибутов товаров в категории
             */
            $groups_ids = array();
            $attributes_ids = array();
            foreach ($element->attributes_groups as $group) {

                $groups_ids[] = $group->id;

                if (
                    isset($group->relations['attributes'])
                    && is_object($group->relations['attributes'])
                    && $group->relations['attributes']->count()
                ) {
                    foreach ($group->relations['attributes'] as $attribute) {
                        $attributes_ids[] = $attribute->id;
                    }
                }
            }
            #Helper::d($attributes_ids);
            #Helper::dd($groups_ids);

            if (count($attributes_ids)) {
                /**
                 * Атрибуты товаров в категории
                 */
                CatalogAttributeMeta::whereIn('attribute_id', $attributes_ids)->delete();
                CatalogAttribute::whereIn('id', $attributes_ids)->delete();
            }
            if (count($groups_ids)) {
                /**
                 * Группы атрибутов товаров в категории
                 */
                CatalogAttributeGroupMeta::whereIn('attributes_group_id', $groups_ids)->delete();
                CatalogAttributeGroup::whereIn('id', $groups_ids)->delete();
            }
        }

        /**
         * Значения атрибутов категории
         */
        CatalogCategoryAttributeValue::where('category_id', $element->id)->delete();

        /**
         * SEO
         */
        if (Allow::module('seo')) {
            Seo::where('module', 'CatalogCategory')
                ->where('unit_id', $element->id)
                ->delete()
            ;
        }

        /**
         * META-данные категории
         */
        $element->metas()->delete();

        /**
         * Фото
         */
        if ($element->image_id) {
            Photo::where('id', $element->image_id)->delete();
        }

        /**
         * Удаление самой категории
         */
        $element->delete();

        /**
         * Сдвигаем остальные категории в общем дереве
         */
        if ($element->rgt) {

            DB::update(DB::raw("UPDATE " . $element->getTable() . " SET lft = lft - 2 WHERE lft > " . $element->lft . ""));
            DB::update(DB::raw("UPDATE " . $element->getTable() . " SET rgt = rgt - 2 WHERE rgt > " . $element->rgt . ""));
        }

        return true;
    }

}