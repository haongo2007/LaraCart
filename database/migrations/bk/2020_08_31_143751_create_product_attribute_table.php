<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductAttributeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_attribute', function (Blueprint $table) {
            $table->id();
            $table->string('value');
            $table->integer('order');
            $table->tinyInteger('is_active')->default(0);
            $table->integer('price')->nullable();
            $table->string('unit')->nullable();
            $table->unsignedBigInteger('attribute_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('storage_id');
            $table->timestamps();

            $table->foreign('attribute_id')
                ->references('id')
                ->on('attribute');

            $table->foreign('product_id')
                ->references('id')
                ->on('products');

            $table->foreign('storage_id')
                ->references('id')
                ->on('storage_file');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_attribute');
    }
}
