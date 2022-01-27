<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('sku');
            $table->string('slug', 100);
            $table->decimal('price', 15, 4);
            $table->decimal('discount', 15, 4)->nullable();
            $table->integer('votes')->default(0);
            $table->integer('stock');
            $table->integer('viewed')->default(0);
            $table->integer('sold')->default(0);
            $table->text('description')->nullable();
            $table->text('tags');
            $table->integer('tax_id')->default(0);
            $table->integer('order');
            $table->integer('is_active')->default(0);
            $table->timestamp('date_sell')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('creator_id');


            $table->foreign('category_id')
                ->references('id')->on('category');

            $table->foreign('brand_id')
                ->references('id')->on('brand');

            $table->foreign('creator_id')
                ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
