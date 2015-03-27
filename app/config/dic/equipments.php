<?php

return array(

    'fields' => function() {

        return array(
            'price' => array(
                'title' => 'Минимальная цена (целое число)',
                'type' => 'text',
            ),
            'engine' => array(
                'title' => 'Двигатель',
                'type' => 'text',
            ),
            'description' => array(
                'title' => 'Описание',
                'type' => 'textarea',
            ),
            'image' => array(
                'title' => 'Фото',
                'type' => 'image',
            ),
        );
    },

    'seo' => 0,

    'versions' => 0,
);