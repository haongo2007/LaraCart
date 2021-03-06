<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
class CreateTablesShop extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Drop table if exist
        $this->down();
        Schema::create('store_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image', 100)->nullable();
            $table->integer('parent')->default(0);
            $table->string('alias', 120)->index();
            $table->integer('store_id')->default(1)->index();
            $table->tinyInteger('sort')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->text('parent_list')->nullable();
            $table->integer('top')->nullable()->default(0);
        });

        Schema::create('store_category_description', function (Blueprint $table) {
            $table->integer('category_id');
            $table->string('lang', 10);
            $table->string('title', 200)->nullable();
            $table->string('keyword', 200)->nullable();
            $table->string('description', 300)->nullable();
            $table->primary(['category_id', 'lang']);
        });

        Schema::create('shop_banner', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('url', 100)->nullable();
            $table->string('target', 50)->nullable();
            $table->text('html')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('sort')->default(0);
            $table->integer('click')->default(0);
            $table->string('type', 20)->index();
            $table->integer('store_id')->default(1)->index();
            $table->timestamps();
            }
        );

        Schema::create('shop_banner_type', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 100)->unique();
            $table->string('name', 100);
            $table->integer('store_id')->default(1)->index();

            }
        );
        

        Schema::create('shop_email_template', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('group', 50);
            $table->text('content');
            $table->text('design');
            $table->integer('store_id')->default(1)->index();
            $table->tinyInteger('status')->default(0);
            }
        );

        Schema::create('shop_language', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('code', 50);
            $table->string('icon', 100)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->tinyInteger('rtl')->nullable()->default(0)->comment('Layout RTL');
            $table->integer('sort')->default(0);
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_store_block', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('position', 100);
            $table->string('page', 200)->nullable();
            $table->string('type', 200);
            $table->text('text')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('sort')->default(0);
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_layout_page', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 100)->unique();
            $table->string('name', 100);
            }
        );

        Schema::create('shop_layout_position', function (Blueprint $table) {
            $table->increments('id');
            $table->string('key', 100)->unique();
            $table->string('name', 100);
            }
        );

        Schema::create('shop_link', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('url', 100);
            $table->string('target', 100);
            $table->string('group', 100);
            $table->string('module', 100)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('sort')->default(0);
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_password_resets', function (Blueprint $table) {
            $table->string('email', 150);
            $table->string('token', 255);
            $table->dateTime('created_at');
            $table->index('email');
            }
        );

        Schema::create('shop_shipping_standard', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('fee');
            $table->integer('shipping_free');
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_brand', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('alias', 120)->index();
            $table->string('image', 255)->nullable();
            $table->string('url', 100)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('sort')->default(0);
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_category', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image', 255)->nullable();
            $table->string('alias', 120)->index();
            $table->integer('parent')->default(0);
            $table->text('parent_list')->nullable();
            $table->integer('top')->nullable()->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('sort')->default(0);
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_category_description', function (Blueprint $table) {
            $table->integer('category_id');
            $table->string('lang', 10)->index();
            $table->string('title', 200)->nullable();
            $table->string('keyword', 200)->nullable();
            $table->string('description', 300)->nullable();
            $table->unique(['category_id', 'lang']);
            }
        );

        Schema::create('shop_currency', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('code', 10);
            $table->string('symbol', 10);
            $table->float('exchange_rate');
            $table->tinyInteger('precision')->default(2);
            $table->tinyInteger('symbol_first')->default(0);
            $table->string('thousands')->default(',');
            $table->tinyInteger('status')->default(0);
            $table->integer('sort')->default(0);
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_order', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->index();
            $table->string('domain')->nullable();
            $table->integer('subtotal')->nullable()->default(0);
            $table->integer('shipping')->nullable()->default(0);
            $table->integer('discount')->nullable()->default(0);
            $table->integer('payment_status')->default(1);
            $table->integer('shipping_status')->default(1);
            $table->integer('status')->default(0);
            $table->integer('tax')->nullable()->default(0);
            $table->integer('total')->nullable()->default(0);
            $table->string('currency', 10);
            $table->float('exchange_rate')->nullable();
            $table->integer('received')->nullable()->default(0);
            $table->integer('balance')->nullable()->default(0);
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('first_name_kana', 100)->nullable();
            $table->string('last_name_kana', 100)->nullable();
            $table->string('address1', 100)->nullable();
            $table->string('address2', 100)->nullable();
            $table->string('address3', 100)->nullable();
            $table->string('country', 10)->nullable()->default('VN');
            $table->string('company', 100)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', 150);
            $table->string('comment', 300)->nullable();
            $table->string('payment_method', 100)->nullable();
            $table->string('shipping_method', 100)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->string('ip', 100)->nullable();
            $table->string('transaction', 100)->nullable();
            $table->integer('store_id')->default(1)->index();
            $table->timestamps();
            }
        );

        Schema::create('shop_order_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->integer('product_id');
            $table->string('name', 100);
            $table->integer('price')->default(0);
            $table->integer('qty')->default(0);
            $table->integer('store_id')->default(1)->index();
            $table->integer('total_price')->default(0);
            $table->integer('tax')->default(0);
            $table->string('sku', 50);
            $table->string('currency', 10);
            $table->float('exchange_rate')->nullable();
            $table->string('attribute', 100)->nullable();
            $table->timestamps();
            }
        );

        Schema::create('shop_order_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->string('content', 300);
            $table->integer('admin_id')->default(0);
            $table->integer('customer_id')->default(0);
            $table->integer('order_status_id')->default(0);
            $table->dateTime('add_date');
            }
        );

        Schema::create('shop_order_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('label', 50);
            $table->integer('store_id')->default(1)->index();

            }
        );

        Schema::create('shop_order_total', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id');
            $table->string('title', 100);
            $table->string('code', 100);
            $table->integer('value')->default(0);
            $table->string('text', 200)->nullable();
            $table->integer('sort')->default(1);
            $table->timestamps();
            }
        );

        Schema::create('shop_page', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image', 255)->nullable();
            $table->string('alias', 120)->index();
            $table->integer('status')->default(0);
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_page_description', function (Blueprint $table) {
            $table->integer('page_id');
            $table->string('lang', 10)->index();
            $table->string('title', 200)->nullable();
            $table->string('keyword', 200)->nullable();
            $table->string('description', 300)->nullable();
            $table->text('content')->nullable();
            $table->text('design')->nullable();
            $table->unique(['page_id', 'lang']);
            }
        );

        Schema::create('shop_payment_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('label', 50);
            $table->integer('store_id')->default(1)->index();

            }
        );

        Schema::create('shop_product', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku', 50)->index();
            $table->string('upc', 20)->nullable()->comment('upc code');
            $table->string('ean', 20)->nullable()->comment('ean code');
            $table->string('jan', 20)->nullable()->comment('jan code');
            $table->string('isbn', 20)->nullable()->comment('isbn code');
            $table->string('mpn', 64)->nullable()->comment('mpn code');
            $table->text('image')->nullable();
            $table->integer('brand_id')->nullable()->default(0)->index();
            $table->integer('supplier_id')->nullable()->default(0)->index();
            $table->float('price')->nullable()->default(0);
            $table->float('cost')->nullable()->default(0);
            $table->integer('stock')->nullable()->default(0);
            $table->integer('sold')->nullable()->default(0);
            $table->integer('minimum')->nullable()->default(0);
            $table->string('weight_class')->nullable();
            $table->integer('weight')->nullable()->default(0);
            $table->string('length_class')->nullable();
            $table->integer('length')->nullable()->default(0);
            $table->integer('width')->nullable()->default(0);
            $table->integer('height')->nullable()->default(0);
            $table->tinyInteger('kind')->nullable()->default(0)->comment('0:single, 1:bundle, 2:group')->index();
            $table->string('property', 50)->nullable()->default('physical')->index();
            $table->string('tax_id', 50)->nullable()->default(0)->comment('0:No-tax, auto: Use tax default')->index();
            $table->tinyInteger('status')->default(0)->index();
            $table->integer('sort')->default(0);
            $table->integer('view')->default(0);
            $table->string('alias', 120)->index();
            $table->integer('category_store_id')->default(1)->nullable()->index();
            $table->integer('store_id')->default(1)->index();
            $table->dateTime('date_lastview')->nullable();
            $table->date('date_available')->nullable();
            $table->integer('top')->default(0)->index();
            $table->integer('rate_point')->default(0)->index();
            $table->integer('rate_count')->default(0)->index();
            $table->string('currency', 55);
            $table->timestamps();
            }
        );

        Schema::create('shop_product_description', function (Blueprint $table) {
            $table->integer('product_id');
            $table->string('lang', 10)->index();
            $table->string('name', 200)->nullable();
            $table->string('keyword', 200)->nullable();
            $table->string('description', 300)->nullable();
            $table->text('content')->nullable();
            $table->unique(['product_id', 'lang']);
            }
        );

        Schema::create('shop_product_image', function (Blueprint $table) {
            $table->increments('id');
            $table->text('image');
            $table->integer('product_id')->default(0)->index();
            $table->enum('type_show', ['default', 'threesixty'])->default('default');
            }
        );

        Schema::create('shop_product_build', function (Blueprint $table) {
            $table->integer('build_id');
            $table->integer('product_id')->nullable();
            $table->string('spot_type',50);
            $table->string('spot_size',50);
            $table->string('spot_color',50);
            $table->string('spot_tooltip_width',50);
            $table->string('spot_tooltip_width_auto',50);
            $table->string('spot_popup_position',50);
            $table->string('sopt_tooltip_visible',50);
            $table->string('spot_content_type',50);
            $table->text('spot_content')->nullable();
            $table->text('spot_inset')->nullable();
            $table->primary('build_id',50);
            }
        );

        Schema::create('shop_product_group', function (Blueprint $table) {
            $table->integer('group_id');
            $table->integer('product_id');
            $table->primary(['group_id', 'product_id']);
            }
        );

        Schema::create('shop_product_category', function (Blueprint $table) {
            $table->integer('product_id');
            $table->integer('category_id');
            $table->primary(['product_id', 'category_id']);
            }
        );

        Schema::create('shop_attribute_group', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->tinyInteger('status')->default(0);
            $table->string('type', 50)->comment('radio,select,checkbox');
            $table->integer('picker')->default(0);
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_product_attribute', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->integer('attribute_group_id');
            $table->integer('product_id');
            $table->float('add_price')->default(0);
            $table->text('images')->nullable();
            $table->integer('parent')->default(0);
            $table->integer('sort')->default(0);
            $table->index(['product_id', 'attribute_group_id']);
            }
        );

        Schema::create('shop_attribute_palette', function (Blueprint $table) {
            $table->string('name', 100);
            $table->string('type',100);
            $table->string('hex',100);
            $table->integer('attribute_id');
            $table->integer('product_id');
            $table->integer('active');
            }
        );

        Schema::create('shop_product_property', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 100)->unique();
            $table->string('name', 100);

            }
        );

        Schema::create('shop_shipping_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('label', 50);
            $table->integer('store_id')->default(1)->index();

            }
        );

        // Schema::create('shop_shoppingcart', function (Blueprint $table) {
        //     $table->string('identifier', 100);
        //     $table->string('instance', 100);
        //     $table->text('content');
        //     $table->timestamps();
        //     $table->index(['identifier', 'instance']);
        //     $table->integer('store_id')->default(1)->index();
        //     }
        // );

        Schema::create('shop_customer', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('first_name_kana', 100)->nullable();
            $table->string('last_name_kana', 100)->nullable();
            $table->string('email', 150)->nullable();
            $table->tinyInteger('sex')->nullable()->comment('0:women, 1:men');
            $table->date('birthday')->nullable();
            $table->string('password', 100)->nullable();;
            $table->integer('address_id')->default(0)->index();
            $table->string('postcode', 10)->nullable();
            $table->string('address1', 100)->nullable();
            $table->string('address2', 100)->nullable();
            $table->string('address3', 100)->nullable();
            $table->string('company', 100)->nullable();
            $table->string('country', 10)->nullable()->default('VN');
            $table->string('phone', 20)->nullable();
            $table->integer('store_id')->default(1)->index();
            $table->string('remember_token', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('group')->default(1);
            $table->timestamp('email_verified_at', $precision = 0)->nullable();
            $table->timestamps();
            //Login social
            $table->string('provider', 50)->nullable();
            $table->string('provider_id', 50)->nullable();
            $table->unique(['email', 'provider', 'provider_id']);
            }
        );

        Schema::create('shop_customer_address', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('customer_id')->index();
            $table->string('first_name', 100);
            $table->string('last_name', 100)->nullable();
            $table->string('first_name_kana', 100)->nullable();
            $table->string('last_name_kana', 100)->nullable();
            $table->string('postcode', 10)->nullable();
            $table->string('address1', 100)->nullable();
            $table->string('address2', 100)->nullable();
            $table->string('address3', 100)->nullable();
            $table->string('country', 10)->nullable()->default('VN');
            $table->string('phone', 20)->nullable();
            }
        );


        Schema::create('shop_supplier', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('alias', 120)->index();
            $table->string('email', 150)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('image', 255)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('url', 100)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('store_id')->default(1)->index();
            $table->integer('sort')->default(0);
            }
        );

        Schema::create('shop_subscribe', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email', 120)->index();
            $table->string('phone', 20)->nullable();
            $table->string('content', 300)->nullable();
            $table->tinyInteger('status')->default(1);
            $table->integer('store_id')->default(1)->index();
            $table->timestamps();
            }
        );

        Schema::create('shop_country', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 10)->unique();
            $table->string('name', 100);
            }
        );
        
        Schema::create('shop_news', function (Blueprint $table) {
            $table->increments('id');
            $table->string('image', 200)->nullable();
            $table->string('alias', 120)->index();
            $table->integer('sort')->default(0);
            $table->tinyInteger('status')->default(0);
            $table->integer('store_id')->default(1)->index();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            }
        );

        Schema::create('shop_news_description', function (Blueprint $table) {
            $table->integer('news_id');
            $table->string('lang', 10);
            $table->string('title', 200)->nullable();
            $table->string('keyword', 200)->nullable();
            $table->string('description', 300)->nullable();
            $table->text('content')->nullable();
            $table->unique(['news_id', 'lang']);
            }
        );

        Schema::create('shop_sessions', function ($table) {
            $table->string('id',100)->unique();
            $table->unsignedInteger('customer_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');
            }
        );

        Schema::create('shop_product_flash', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id')->unique();
            $table->integer('stock');
            $table->integer('sold');
            $table->integer('sort');
            $table->integer('store_id')->default(1)->index();
        });
        //Passport
        // Schema::create('oauth_auth_codes', function (Blueprint $table) {
        //     $table->string('id', 100)->primary();
        //     $table->unsignedBigInteger('user_id')->index();
        //     $table->unsignedBigInteger('client_id');
        //     $table->text('scopes')->nullable();
        //     $table->boolean('revoked');
        //     $table->dateTime('expires_at')->nullable();
        //     }
        // );

        // Schema::create('oauth_access_tokens', function (Blueprint $table) {
        //     $table->string('id', 100)->primary();
        //     $table->unsignedBigInteger('user_id')->nullable()->index();
        //     $table->unsignedBigInteger('client_id');
        //     $table->string('name')->nullable();
        //     $table->text('scopes')->nullable();
        //     $table->boolean('revoked');
        //     $table->timestamps();
        //     $table->dateTime('expires_at')->nullable();
        //     }
        // );

        // Schema::create('oauth_refresh_tokens', function (Blueprint $table) {
        //     $table->string('id', 100)->primary();
        //     $table->string('access_token_id', 100)->index();
        //     $table->boolean('revoked');
        //     $table->dateTime('expires_at')->nullable();
        //     }
        // );

        // Schema::create('oauth_clients', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->unsignedBigInteger('user_id')->nullable()->index();
        //     $table->string('name');
        //     $table->string('secret', 100)->nullable();
        //     $table->string('provider')->nullable();
        //     $table->text('redirect');
        //     $table->boolean('personal_access_client');
        //     $table->boolean('password_client');
        //     $table->boolean('revoked');
        //     $table->timestamps();
        //     }
        // );
        
        // Schema::create('oauth_personal_access_clients', function (Blueprint $table) {
        //     $table->bigIncrements('id');
        //     $table->unsignedBigInteger('client_id');
        //     $table->timestamps();
        //     }
        // );


        Schema::create('api_connection', function (Blueprint $table) {
            $table->increments('id');
            $table->string('description', 255);
            $table->string('apiconnection', 50)->unique();
            $table->string('apikey', 128);
            $table->date('expire')->nullable();
            $table->datetime('last_active')->nullable();
            $table->tinyInteger('status')->default(0);
            }
        );

        Schema::create('shop_tax', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->integer('value')->default(0);
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_weight', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('description', 100);
            $table->integer('store_id')->default(1)->index();

            }
        );

        Schema::create('shop_length', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('description', 100);
            $table->integer('store_id')->default(1)->index();

            }
        );

        Schema::create('jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue', 150)->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
            }
        );

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('uuid', 200)->nullable()->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
            }
        );

        Schema::create('shop_store_css', function (Blueprint $table) {
            $table->text('css');
            $table->integer('store_id')->unique();
            }
        );

        Schema::create('shop_product_promotion', function (Blueprint $table) {
            $table->integer('product_id')->primary();
            $table->float('price_promotion');
            $table->dateTime('date_start')->nullable();
            $table->dateTime('date_end')->nullable();
            $table->integer('status_promotion')->default(1);
            $table->timestamps();
            }
        );

        Schema::create('shop_product_download', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('product_id');
            $table->string('path', 255);

            }
        );

        Schema::create('shop_custom_field', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type', 50)->index()->comment('product, customer');
            $table->string('code', 100)->index();
            $table->string('name', 250);
            $table->integer('required')->default(0);
            $table->integer('status')->default(1);
            $table->string('option', 50)->nullable()->comment('radio, select, input');
            $table->string('default', 250)->nullable()->comment('{"value1":"name1", "value2":"name2"}');
            $table->integer('store_id')->default(1)->index();
            }
        );

        Schema::create('shop_custom_field_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('custom_field_id')->index();
            $table->integer('rel_id')->index();
            $table->text('text')->nullable();
            }
        );


        Schema::create('shop_discount', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->unique();
            $table->integer('reward')->default(2);
            $table->enum('type', ['point', 'percent'])->default('point');
            $table->string('data', 300)->nullable();
            $table->integer('limit')->default(1);
            $table->integer('used')->default(0);
            $table->integer('login')->default(0);
            $table->integer('store_id')->default(1)->index();
            $table->tinyInteger('status')->default(0);
            $table->dateTime('expires_at')->nullable();
        });

        Schema::create('shop_discount_customer', function (Blueprint $table) {
            $table->integer('customer_id');
            $table->integer('discount_id');
            $table->text('log')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->primary(['customer_id', 'discount_id']);
        });

        Schema::create('product_rating', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('product_id');
            $table->uuid('customer_id');
            $table->string('name', 100);
            $table->integer('point');
            $table->string('comment', 300);
            $table->integer('status')->default(0);
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_banner');
        Schema::dropIfExists('shop_banner_type');
        Schema::dropIfExists('shop_email_template');
        Schema::dropIfExists('shop_language');
        Schema::dropIfExists('shop_store_block');
        Schema::dropIfExists('shop_layout_page');
        Schema::dropIfExists('shop_layout_position');
        Schema::dropIfExists('shop_link');
        Schema::dropIfExists('shop_password_resets');
        Schema::dropIfExists('shop_shipping_standard');
        Schema::dropIfExists('shop_api');
        Schema::dropIfExists('shop_api_process');
        Schema::dropIfExists('shop_brand');
        Schema::dropIfExists('shop_category');
        Schema::dropIfExists('shop_category_description');
        Schema::dropIfExists('shop_currency');
        Schema::dropIfExists('shop_order');
        Schema::dropIfExists('shop_order_detail');
        Schema::dropIfExists('shop_order_history');
        Schema::dropIfExists('shop_order_status');
        Schema::dropIfExists('shop_order_total');
        Schema::dropIfExists('shop_page');
        Schema::dropIfExists('shop_page_description');
        Schema::dropIfExists('shop_payment_status');
        Schema::dropIfExists('shop_product');
        Schema::dropIfExists('shop_product_description');
        Schema::dropIfExists('shop_product_image');
        Schema::dropIfExists('shop_product_build');
        Schema::dropIfExists('shop_product_attribute');
        Schema::dropIfExists('shop_product_property');
        Schema::dropIfExists('shop_attribute_group');
        Schema::dropIfExists('shop_product_group');
        Schema::dropIfExists('shop_product_category');
        Schema::dropIfExists('shop_shipping_status');
        // Schema::dropIfExists('shop_shoppingcart');
        Schema::dropIfExists('shop_product_promotion');
        Schema::dropIfExists('shop_customer');
        Schema::dropIfExists('shop_supplier');
        Schema::dropIfExists('shop_customer_address');
        Schema::dropIfExists('shop_subscribe');
        Schema::dropIfExists('shop_country');
        Schema::dropIfExists('shop_news');
        Schema::dropIfExists('shop_news_description');
        Schema::dropIfExists('shop_sessions');
        Schema::dropIfExists('shop_tax');
        Schema::dropIfExists('shop_weight');
        Schema::dropIfExists('shop_length');
        Schema::dropIfExists('shop_product_download');
        //Passport
        // Schema::dropIfExists('oauth_auth_codes');
        // Schema::dropIfExists('oauth_access_tokens');
        // Schema::dropIfExists('oauth_refresh_tokens');
        // Schema::dropIfExists('oauth_clients');
        // Schema::dropIfExists('oauth_personal_access_clients');
        //Api connection
        Schema::dropIfExists('api_connection');
        //Job
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('shop_store_css');
        //Custom field
        Schema::dropIfExists('shop_custom_field');
        Schema::dropIfExists('shop_custom_field_detail');
        Schema::dropIfExists('shop_discount');
        Schema::dropIfExists('shop_discount_customer');
        Schema::dropIfExists('product_rating');
    }

}
