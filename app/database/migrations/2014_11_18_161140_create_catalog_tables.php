<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCatalogTables extends Migration {

    private $prefix = 'catalog_';

	public function up(){

        $this->table = $this->prefix . "categories";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->smallInteger('active')->unsigned()->default(1)->index();
                $table->string('slug')->nullable()->unique();
                $table->integer('image_id')->unsigned()->default(0);

                $table->text('settings')->nullable();
                $table->integer('lft')->unsigned()->nullable()->index();
                $table->integer('rgt')->unsigned()->nullable()->index();
                $table->timestamps();
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "categories_meta";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('category_id')->unsigned()->index();
                $table->string('language')->nullable()->index();
                $table->smallInteger('active')->unsigned()->default(1)->index();
                $table->string('name')->nullable();
                $table->string('min_price')->nullable()->index();

                $table->text('settings')->nullable();
                $table->timestamps();

                $table->unique(array('category_id', 'language'), 'category_language');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }


        $this->table = $this->prefix . "categories_attributes";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->smallInteger('active')->unsigned()->default(1)->index();
                $table->string('slug')->nullable()->unique();
                $table->string('type')->nullable();

                $table->text('settings')->nullable();
                $table->timestamps();

                #$table->unique(array('category_id', 'language'), 'category_language');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "categories_attributes_meta";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('attribute_id')->unsigned()->index();
                $table->string('language')->nullable()->index();
                $table->string('name')->nullable();

                $table->text('settings')->nullable();
                $table->timestamps();

                $table->unique(array('attribute_id', 'language'), 'attribute_language');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "categories_attributes_values";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('category_id')->unsigned()->index();
                $table->integer('attribute_id')->unsigned()->index();
                $table->string('language')->nullable()->index();
                $table->string('value')->nullable();

                $table->text('settings')->nullable();
                $table->timestamps();

                $table->unique(array('category_id', 'attribute_id', 'language'), 'category_attribute_language');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }




        $this->table = $this->prefix . "products";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->smallInteger('active')->unsigned()->default(0)->index();
                $table->string('slug')->nullable()->unique();
                $table->integer('category_id')->unsigned()->index();

                $table->string('article')->default('')->index();
                $table->integer('amount')->unsigned()->nullable()->index();

                $table->integer('image_id')->unsigned()->default(0);
                $table->integer('gallery_id')->unsigned()->default(0);

                $table->text('settings')->nullable();
                $table->integer('lft')->unsigned()->nullable()->index();
                $table->integer('rgt')->unsigned()->nullable()->index();

                $table->softDeletes();
                $table->timestamps();
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "products_meta";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('product_id')->unsigned()->nullable()->index();
                $table->string('language')->nullable()->index();
                $table->smallInteger('active')->unsigned()->default(0)->index();

                $table->string('name')->nullable()->index();
                $table->text('description');
                $table->text('full_description');
                $table->string('price')->nullable()->index();

                $table->text('settings')->nullable();

                $table->softDeletes();
                $table->timestamps();

                $table->unique(array('product_id', 'language'), 'product_id_language');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }



        $this->table = $this->prefix . "attributes_groups";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('category_id')->unsigned()->index();
                $table->smallInteger('active')->unsigned()->default(0)->index();
                $table->string('slug')->nullable()->index();

                $table->text('settings')->nullable();
                $table->integer('lft')->unsigned()->nullable()->index();
                $table->integer('rgt')->unsigned()->nullable()->index();

                $table->softDeletes(); ## нет поддержки в коде магазина
                $table->timestamps();

                $table->unique(array('category_id', 'slug'), 'attributes_groups_category_id_slug_language');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "attributes_groups_meta";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('attributes_group_id')->unsigned()->index();
                $table->string('language')->nullable()->index();
                $table->smallInteger('active')->unsigned()->default(0)->index();
                $table->string('name')->nullable()->index();

                $table->text('settings')->nullable();

                $table->softDeletes(); ## нет поддержки в коде магазина
                $table->timestamps();

                $table->unique(array('attributes_group_id', 'language'), 'attributes_group_id_language');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "attributes";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->smallInteger('active')->unsigned()->default(0)->index();
                $table->string('slug')->nullable()->index();
                $table->integer('attributes_group_id')->unsigned()->index();
                $table->string('type')->default('text')->index();

                $table->text('settings')->nullable();
                $table->integer('lft')->unsigned()->nullable()->index();
                $table->integer('rgt')->unsigned()->nullable()->index();

                $table->softDeletes(); ## нет поддержки в коде магазина
                $table->timestamps();
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "attributes_meta";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('attribute_id')->unsigned()->index();
                $table->string('language')->nullable()->index();
                $table->smallInteger('active')->unsigned()->default(0)->index();
                $table->string('name')->nullable()->index();

                $table->text('settings')->nullable();

                $table->softDeletes(); ## нет поддержки в коде магазина
                $table->timestamps();

                $table->unique(array('attribute_id', 'language'), 'attribute_id_language');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "attributes_values";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('product_id')->unsigned()->index();
                $table->integer('attribute_id')->unsigned()->index();
                $table->string('language')->nullable()->index();

                $table->string('value')->nullable();

                $table->text('settings')->nullable();
                $table->integer('lft')->unsigned()->nullable()->index();
                $table->integer('rgt')->unsigned()->nullable()->index();
                $table->timestamps();

                $table->unique(array('attribute_id', 'product_id', 'language'), 'attribute_product_language');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }



        $this->table = $this->prefix . "orders";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->smallInteger('status_id')->unsigned()->nullable()->default(1)->index();
                $table->float('total_sum')->unsigned()->index();
                $table->integer('client_id')->nullable()->unsigned()->index();
                $table->string('client_name')->nullable();
                $table->text('delivery_info')->nullable();
                $table->text('comment')->nullable();

                $table->softDeletes();
                $table->timestamps();
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "orders_products";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('order_id')->unsigned()->index();
                $table->integer('product_id')->unsigned()->index();
                $table->string('product_hash')->nullable()->index();
                $table->integer('count')->unsigned()->default(1);
                $table->float('price')->unsigned()->index();

                $table->text('product_cache')->nullable();

                $table->timestamps();

                $table->unique(array('order_id', 'product_hash'), 'order_product_hash');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "orders_products_attributes";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('order_id')->unsigned()->nullable()->index();
                $table->integer('product_id')->unsigned()->nullable()->index();
                $table->integer('attribute_id')->unsigned()->nullable()->index();
                $table->string('attribute_cache')->nullable();
                $table->string('value')->nullable()->index();

                $table->timestamps();
                $table->unique(array('order_id', 'product_id', 'attribute_id'), 'order_product_attribute');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "orders_statuses";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('sort_order')->unsigned()->index();

                $table->timestamps();
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "orders_statuses_meta";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('status_id')->nullable()->unsigned()->index();
                $table->string('language')->nullable()->index();
                $table->string('title')->nullable();

                $table->timestamps();

                $table->unique(array('status_id', 'language'), 'status_id_language');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }

        $this->table = $this->prefix . "orders_statuses_history";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->increments('id');
                $table->integer('order_id')->unsigned()->nullable()->index();
                $table->integer('status_id')->unsigned()->nullable()->index();

                $table->text('comment')->nullable();
                $table->string('changer_id')->nullable()->index();
                $table->string('changer_name')->nullable()->index();
                $table->text('status_cache')->nullable();

                $table->timestamps();
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }


        $this->table = $this->prefix . "related_products";
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function(Blueprint $table) {

                $table->integer('product_id')->unsigned()->index();
                $table->integer('related_product_id')->unsigned()->index();

                #$table->timestamps();
                $table->unique(array('product_id', 'related_product_id'), 'related_products_pair');
            });
            echo(' + ' . $this->table . PHP_EOL);
        } else {
            echo('...' . $this->table . PHP_EOL);
        }


    }


	public function down(){

        Schema::dropIfExists($this->prefix . "categories");
        echo(' - ' . $this->prefix . "categories" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "categories_meta");
        echo(' - ' . $this->prefix . "categories_meta" . PHP_EOL);



        Schema::dropIfExists($this->prefix . "categories_attributes");
        echo(' - ' . $this->prefix . "categories_attributes" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "categories_attributes_meta");
        echo(' - ' . $this->prefix . "categories_attributes_meta" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "categories_attributes_values");
        echo(' - ' . $this->prefix . "categories_attributes_values" . PHP_EOL);



        Schema::dropIfExists($this->prefix . "products");
        echo(' - ' . $this->prefix . "products" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "products_meta");
        echo(' - ' . $this->prefix . "products_meta" . PHP_EOL);



        Schema::dropIfExists($this->prefix . "attributes_groups");
        echo(' - ' . $this->prefix . "attributes_groups" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "attributes_groups_meta");
        echo(' - ' . $this->prefix . "attributes_groups_meta" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "attributes");
        echo(' - ' . $this->prefix . "attributes" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "attributes_meta");
        echo(' - ' . $this->prefix . "attributes_meta" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "attributes_values");
        echo(' - ' . $this->prefix . "attributes_values" . PHP_EOL);



        Schema::dropIfExists($this->prefix . "orders");
        echo(' - ' . $this->prefix . "orders" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "orders_products");
        echo(' - ' . $this->prefix . "orders_products" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "orders_products_attributes");
        echo(' - ' . $this->prefix . "orders_products_attributes" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "orders_statuses");
        echo(' - ' . $this->prefix . "order_statuses" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "orders_statuses_meta");
        echo(' - ' . $this->prefix . "order_statuses_meta" . PHP_EOL);

        Schema::dropIfExists($this->prefix . "orders_statuses_history");
        echo(' - ' . $this->prefix . "order_statuses_history" . PHP_EOL);


        Schema::dropIfExists($this->prefix . "related_products");
        echo(' - ' . $this->prefix . "related_products" . PHP_EOL);
	}

}

