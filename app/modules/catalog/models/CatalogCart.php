<?php

class CatalogCart {

    private static $session_key = 'catalog.cart';
    private static $goods;
    protected static $_instance = null;

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    protected function __clone() {}
    private function __construct() {
        self::load();
    }


    public static function load() {

        #self::$goods = Session::has(self::$session_key) ? json_decode(Session::get(self::$session_key), true) : [];
        #self::$goods = Session::has(self::$session_key) ? Session::get(self::$session_key)) : [];
        #self::$goods = Session::get(self::$session_key) ?: [];
        self::$goods = Session::get(self::$session_key, []);
        #self::$goods = [];
    }

    public static function save() {

        #Session::put(self::$session_key, json_encode(self::$goods));
        Session::put(self::$session_key, self::$goods);
    }

    public static function get($load = false) {

        if ($load)
            self::load();
        return self::$goods;
    }

    public static function clear() {

        Session::forget(self::$session_key);
    }

    public static function update($hash, $fields, $save = false) {

        #Helper::ta($hash);
        #Helper::tad($fields);
        #Helper::ta($hash);

        if (isset(self::$goods[$hash])) {

            $fields = (array)$fields;

            if (isset($fields['amount']) && $fields['amount'] == -1) {

                unset(self::$goods[$hash]);

            } else {

                foreach ($fields as $key => $value) {
                    self::$goods[$hash][$key] = $value;
                }
            }
        }

        if ($save)
            self::save();
    }

    public static function delete($hash, $save = false) {

        self::update($hash, -1, $save);
    }

    public static function get_full($load = false) {

        if ($load)
            self::load();

        #Helper::tad(self::$goods);

        $ids = [];
        if (count(self::$goods)) {
            #$ids = array_keys(self::$goods);
            foreach (self::$goods as $good_variant) {
                $ids[] = $good_variant['id'];
            }
        }
        $ids = array_unique($ids);

        if (!count($ids))
            return null;

        #Helper::tad($ids);

        $goods = (new CatalogProduct())
            ->whereIn('id', $ids)
            ->with(['meta'])
            ->get()
        ;
        if (is_object($goods) && $goods->count()) {
            $goods = DicVal::extracts($goods, null, true, true);
            #Helper::ta($goods);
            $goods = DicLib::loadImages($goods, ['image_id']);
        }
        #Helper::tad($goods);

        $return = new Collection();

        foreach (self::$goods as $good_variant_hash => $good_variant) {
            #foreach ($good_variants as $good_variant) {
                #Helper::tad($good_variant);
                $good_id = @$good_variant['id'];
                if (!isset($goods[$good_id]) || !@$good_variant['amount'])
                    continue;
                $good = $goods[$good_id];
                $good->_hash = $good_variant_hash;
                $good->_amount = $good_variant['amount'];
                if (isset($good_variant['options']) && is_array($good_variant['options']) && count($good_variant['options']))
                    $good->_options = $good_variant['options'];
                $return[$good->_hash] = $good;
            #}
        }
        #Helper::tad($return);

        return $return;
    }

    public static function count($load = false) {

        if ($load)
            self::load();

        /*
        $amount = 0;
        foreach (self::$goods as $good_variants) {
            foreach ($good_variants as $good_variant) {
                $amount += $good_variant['amount'];
            }
        }
        */

        $amount = 0;
        foreach (self::$goods as $good) {
            $amount += @(int)$good['amount'];
        }

        #$amount = count(self::$goods);

        return $amount;
    }

    public static function add($good_id, $amount = 1, $options = [], $save = false) {

        /**
         * Такая штука: нам нужно хранить свойства покупаемого товара, если они могут различаться.
         * Например, товар разного цвета. Клиент заказывает 1 единицу товара красного цвета и две - черного.
         * По id это один и тот же товар, а свойства и количество - разные.
         */

        /*
         * Сортируем опции по ключам - обязательно для точного вычисления хэша
         */
        ksort($options);

        /*
         * Вычисляем хэш позиции - ID и хэш от Options JSON
         */
        $good_variant_hash = $good_id . '_' . sha1(json_encode($options));

        /*
         * Ищем позицию в списке (или создаем новую) и заполняем нужными данными
         */
        if (isset(self::$goods[$good_variant_hash])) {

            ## Если текущая позиция уже есть в корзине - добавляем нужное количество
            self::$goods[$good_variant_hash]['amount'] += $amount;

        } else {

            ## Если позиция еще не в корзине - добавляем ее
            self::$goods[$good_variant_hash] = [
                'id' => $good_id,
                'amount' => $amount,
                'options' => $options,
            ];
        }

        /*
         * Если в корзине уже есть массив с данными о товаре с таким id -
         * пройдемся по всем элементам массива (группы одного и того же товара, но с разными свойствами),
         * и если найдем совпадение в свойствах с текущим набором - добавим туда текущее кол-во.
         */
        /*
        $found = false;
        if (count(self::$goods[$good_id])) {
            ## Ищем группу товаров с подходящими свойствами
            foreach (self::$goods[$good_id] as $good_variant_hash => $good_variant) {
                if ($good_variant['options'] == $options) {
                    $good_variant['amount'] += $amount;
                    self::$goods[$good_id][$good_variant_hash] = $good_variant;
                    $found = true;
                    break;
                }
            }
        }
        */
        /*
         * Если группа товаров с подходящими свойствами не найдена - добавляем ее
         */
        /*
        if (!$found) {
            self::$goods[$good_id][$good_variant_hash] = [
                'options' => $options,
                'amount' => $amount,
            ];
        }
        */

        /*
         * Если передана команда о сохранении - сохраняем корзину
         */
        if ($save)
            self::save();

        return true;
    }
}