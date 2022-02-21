<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataAdminSeeder extends Seeder
{
    public $adminUser = 'admin';
    //admin
    public $adminPassword = '$2y$10$JcmAHe5eUZ2rS0jU1GWr/.xhwCnh2RU13qwjTPcqfmtZXjZxcryPO';
    public $adminEmail = 'your-email@your-domain.com';
    public $timezone_default = 'Asia/Ho_Chi_Minh';
    public $language_default = 'en';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::connection(config('const.LC_CONNECTION'))->table('admin_menu')->insert(
            [
            ['id' => 1, 'parent_id' => 6, 'sort' => 1, 'title' => 'lang::admin.menu_titles.order_manager', 'icon' => '', 'uri' => '', 'key' => 'ORDER_MANAGER', 'type' => 0],
            ['id' => 2, 'parent_id' => 6, 'sort' => 2, 'title' => 'lang::admin.menu_titles.catalog_mamager', 'icon' => '', 'uri' => '', 'key' => 'CATALOG_MANAGER', 'type' => 0],
            ['id' => 3, 'parent_id' => 25, 'sort' => 3, 'title' => 'lang::admin.menu_titles.customer_manager', 'icon' => '', 'uri' => '', 'key' => 'CUSTOMER_MANAGER', 'type' => 0],
            ['id' => 4, 'parent_id' => 8, 'sort' => 201, 'title' => 'lang::admin.menu_titles.template_layout', 'icon' => '', 'uri' => '', 'key' => 'TEMPLATE', 'type' => 0],
            ['id' => 5, 'parent_id' => 9, 'sort' => 2, 'title' => 'lang::admin.menu_titles.admin_global', 'icon' => '', 'uri' => '', 'key' => 'CONFIG_SYSTEM', 'type' => 0],
            ['id' => 6, 'parent_id' => 0, 'sort' => 10, 'title' => 'lang::admin.menu_titles.ADMIN_SHOP', 'icon' => 'icon-bag-16', 'uri' => '', 'key' => 'ADMIN_SHOP', 'type' => 0],
            ['id' => 7, 'parent_id' => 0, 'sort' => 100, 'title' => 'lang::admin.menu_titles.ADMIN_CONTENT', 'icon' => 'icon-book-bookmark', 'uri' => '', 'key' => 'ADMIN_CONTENT', 'type' => 0],
            ['id' => 8, 'parent_id' => 0, 'sort' => 300, 'title' => 'lang::admin.menu_titles.ADMIN_EXTENSION', 'icon' => 'icon-atom', 'uri' => '', 'key' => 'ADMIN_EXTENSION', 'type' => 0],
            ['id' => 9, 'parent_id' => 0, 'sort' => 400, 'title' => 'lang::admin.menu_titles.ADMIN_SYSTEM', 'icon' => 'icon-settings-gear-63', 'uri' => '', 'key' => 'ADMIN_SYSTEM', 'type' => 0],
            ['id' => 10, 'parent_id' => 7, 'sort' => 102, 'title' => 'lang::page.admin.title', 'icon' => '', 'uri' => 'admin::page', 'key' => null, 'type' => 0],
            ['id' => 11, 'parent_id' => 27, 'sort' => 2, 'title' => 'lang::shipping_status.admin.title', 'icon' => '', 'uri' => 'admin::shipping_status', 'key' => null, 'type' => 0],
            ['id' => 12, 'parent_id' => 1, 'sort' => 3, 'title' => 'lang::order.admin.title', 'icon' => '', 'uri' => 'admin::order', 'key' => null, 'type' => 0],
            ['id' => 13, 'parent_id' => 27, 'sort' => 1, 'title' => 'lang::order_status.admin.title', 'icon' => '', 'uri' => 'admin::order_status', 'key' => null, 'type' => 0],
            ['id' => 14, 'parent_id' => 27, 'sort' => 3, 'title' => 'lang::payment_status.admin.title', 'icon' => '', 'uri' => 'admin::payment_status', 'key' => null, 'type' => 0],
            ['id' => 15, 'parent_id' => 2, 'sort' => 0, 'title' => 'lang::product.admin.title', 'icon' => '', 'uri' => 'admin::product', 'key' => null, 'type' => 0],
            ['id' => 16, 'parent_id' => 2, 'sort' => 0, 'title' => 'lang::category.admin.title', 'icon' => '', 'uri' => 'admin::category', 'key' => null, 'type' => 0],
            ['id' => 17, 'parent_id' => 27, 'sort' => 4, 'title' => 'lang::supplier.admin.title', 'icon' => '', 'uri' => 'admin::supplier', 'key' => null, 'type' => 0],
            ['id' => 18, 'parent_id' => 27, 'sort' => 5, 'title' => 'lang::brand.admin.title', 'icon' => '', 'uri' => 'admin::brand', 'key' => null, 'type' => 0],
            ['id' => 19, 'parent_id' => 27, 'sort' => 8, 'title' => 'lang::attribute_group.admin.title', 'icon' => '', 'uri' => 'admin::attribute_group', 'key' => null, 'type' => 0],
            ['id' => 20, 'parent_id' => 3, 'sort' => 0, 'title' => 'lang::customer.admin.title', 'icon' => '', 'uri' => 'admin::customer', 'key' => null, 'type' => 0],
            ['id' => 21, 'parent_id' => 3, 'sort' => 0, 'title' => 'lang::subscribe.admin.title', 'icon' => '', 'uri' => 'admin::subscribe', 'key' => null, 'type' => 0],
            ['id' => 22, 'parent_id' => 67, 'sort' => 1, 'title' => 'lang::store_block.admin.title', 'icon' => '', 'uri' => 'admin::store_block', 'key' => null, 'type' => 0],
            ['id' => 23, 'parent_id' => 67, 'sort' => 2, 'title' => 'lang::admin.menu_titles.block_link', 'icon' => '', 'uri' => 'admin::store_link', 'key' => null, 'type' => 0],
            ['id' => 24, 'parent_id' => 4, 'sort' => 0, 'title' => 'lang::admin.menu_titles.template_manager', 'icon' => '', 'uri' => 'admin::template', 'key' => null, 'type' => 0],
            ['id' => 25, 'parent_id' => 0, 'sort' => 200, 'title' => 'lang::admin.menu_titles.ADMIN_MARKETING', 'icon' => 'icon-key-25', 'uri' => '', 'key' => 'MARKETING', 'type' => 0],
            ['id' => 26, 'parent_id' => 65, 'sort' => 1, 'title' => 'lang::store.admin.title', 'icon' => '', 'uri' => 'admin::store_info', 'key' => null, 'type' => 0],
            ['id' => 27, 'parent_id' => 9, 'sort' => 3, 'title' => 'lang::admin.menu_titles.setting_system', 'icon' => '', 'uri' => '', 'key' => 'SETTING_SYSTEM', 'type' => 0],
            ['id' => 28, 'parent_id' => 9, 'sort' => 4, 'title' => 'lang::admin.menu_titles.error_log', 'icon' => '', 'uri' => '', 'key' => '', 'type' => 0],
            ['id' => 29, 'parent_id' => 25, 'sort' => 0, 'title' => 'lang::email_template.admin.title', 'icon' => '', 'uri' => 'admin::email_template', 'key' => null, 'type' => 0],
            ['id' => 30, 'parent_id' => 9, 'sort' => 5, 'title' => 'lang::admin.menu_titles.localisation', 'icon' => '', 'uri' => '', 'key' => null, 'type' => 0],
            ['id' => 31, 'parent_id' => 30, 'sort' => 0, 'title' => 'lang::language.admin.title', 'icon' => '', 'uri' => 'admin::language', 'key' => null, 'type' => 0],
            ['id' => 32, 'parent_id' => 30, 'sort' => 0, 'title' => 'lang::currency.admin.title', 'icon' => '', 'uri' => 'admin::currency', 'key' => null, 'type' => 0],
            ['id' => 33, 'parent_id' => 7, 'sort' => 101, 'title' => 'lang::banner.admin.title', 'icon' => '', 'uri' => 'admin::banner', 'key' => null, 'type' => 0],
            ['id' => 34, 'parent_id' => 5, 'sort' => 5, 'title' => 'lang::backup.admin.title', 'icon' => '', 'uri' => 'admin::backup', 'key' => null, 'type' => 0],
            ['id' => 35, 'parent_id' => 8, 'sort' => 202, 'title' => 'lang::admin.menu_titles.plugins', 'icon' => '', 'uri' => '', 'key' => 'PLUGIN', 'type' => 0],
            ['id' => 36, 'parent_id' => 28, 'sort' => 2, 'title' => 'lang::admin.menu_titles.webhook', 'icon' => '', 'uri' => 'admin::config/webhook', 'key' => null, 'type' => 0],
            ['id' => 37, 'parent_id' => 25, 'sort' => 5, 'title' => 'lang::admin.menu_titles.report_manager', 'icon' => '', 'uri' => '', 'key' => 'REPORT_MANAGER', 'type' => 0],
            ['id' => 38, 'parent_id' => 9, 'sort' => 1, 'title' => 'lang::admin.menu_titles.user_permission', 'icon' => '', 'uri' => '', 'key' => 'ADMIN', 'type' => 0],
            ['id' => 39, 'parent_id' => 35, 'sort' => 0, 'title' => 'plugin.Payment', 'icon' => '', 'uri' => 'admin::plugin/payment', 'key' => null, 'type' => 0],
            ['id' => 40, 'parent_id' => 35, 'sort' => 0, 'title' => 'plugin.Shipping', 'icon' => '', 'uri' => 'admin::plugin/shipping', 'key' => null, 'type' => 0],
            ['id' => 41, 'parent_id' => 35, 'sort' => 0, 'title' => 'plugin.Total', 'icon' => '', 'uri' => 'admin::plugin/total', 'key' => null, 'type' => 0],
            ['id' => 42, 'parent_id' => 35, 'sort' => 100, 'title' => 'plugin.Other', 'icon' => '', 'uri' => 'admin::plugin/other', 'key' => null, 'type' => 0],
            ['id' => 43, 'parent_id' => 35, 'sort' => 0, 'title' => 'plugin.Cms', 'icon' => '', 'uri' => 'admin::plugin/cms', 'key' => null, 'type' => 0],
            ['id' => 44, 'parent_id' => 67, 'sort' => 2, 'title' => 'lang::admin.menu_titles.css', 'icon' => '', 'uri' => 'admin::store_css', 'key' => null, 'type' => 0],
            ['id' => 45, 'parent_id' => 25, 'sort' => 4, 'title' => 'lang::admin.menu_titles.seo_manager', 'icon' => '', 'uri' => '', 'key' => 'SEO_MANAGER', 'type' => 0],
            ['id' => 46, 'parent_id' => 38, 'sort' => 0, 'title' => 'lang::admin.menu_titles.users', 'icon' => '', 'uri' => 'admin::user', 'key' => null, 'type' => 0],
            ['id' => 47, 'parent_id' => 38, 'sort' => 0, 'title' => 'lang::admin.menu_titles.roles', 'icon' => '', 'uri' => 'admin::role', 'key' => null, 'type' => 0],
            ['id' => 48, 'parent_id' => 38, 'sort' => 0, 'title' => 'lang::admin.menu_titles.permission', 'icon' => '', 'uri' => 'admin::permission', 'key' => null, 'type' => 0],
            ['id' => 49, 'parent_id' => 5, 'sort' => 0, 'title' => 'lang::admin.menu_titles.menu', 'icon' => '', 'uri' => 'admin::menu', 'key' => null, 'type' => 0],
            ['id' => 50, 'parent_id' => 28, 'sort' => 0, 'title' => 'lang::admin.menu_titles.operation_log', 'icon' => '', 'uri' => 'admin::log', 'key' => null, 'type' => 0],
            ['id' => 51, 'parent_id' => 45, 'sort' => 0, 'title' => 'lang::admin.menu_titles.seo_config', 'icon' => '', 'uri' => 'admin::seo/config', 'key' => null, 'type' => 0],
            ['id' => 52, 'parent_id' => 7, 'sort' => 103, 'title' => 'lang::news.admin.title', 'icon' => '', 'uri' => 'admin::news', 'key' => null, 'type' => 0],
            ['id' => 53, 'parent_id' => 5, 'sort' => 0, 'title' => 'lang::admin.menu_titles.env_config', 'icon' => '', 'uri' => 'admin::env/config', 'key' => null, 'type' => 0],
            ['id' => 54, 'parent_id' => 37, 'sort' => 0, 'title' => 'lang::admin.menu_titles.report_product', 'icon' => '', 'uri' => 'admin::report/product', 'key' => null, 'type' => 0],
            ['id' => 57, 'parent_id' => 65, 'sort' => 2, 'title' => 'lang::admin.menu_titles.store_config', 'icon' => '', 'uri' => 'admin::store_config', 'key' => null, 'type' => 0],
            ['id' => 58, 'parent_id' => 5, 'sort' => 5, 'title' => 'lang::admin.menu_titles.cache_manager', 'icon' => '', 'uri' => 'admin::cache_config', 'key' => null, 'type' => 0],
            ['id' => 59, 'parent_id' => 9, 'sort' => 7, 'title' => 'lang::admin.menu_titles.api_manager', 'icon' => '', 'uri' => '', 'key' => 'API_MANAGER', 'type' => 0],
            ['id' => 60, 'parent_id' => 65, 'sort' => 3, 'title' => 'lang::store_maintain.config_manager.title', 'icon' => '', 'uri' => 'admin::store_maintain', 'key' => null, 'type' => 0],
            ['id' => 61, 'parent_id' => 27, 'sort' => 9, 'title' => 'lang::tax.admin.admin_title', 'icon' => '', 'uri' => 'admin::tax', 'key' => null, 'type' => 0],
            ['id' => 62, 'parent_id' => 27, 'sort' => 6, 'title' => 'lang::weight.admin.title', 'icon' => '', 'uri' => 'admin::weight_unit', 'key' => null, 'type' => 0],
            ['id' => 63, 'parent_id' => 27, 'sort' => 7, 'title' => 'lang::length.admin.title', 'icon' => '', 'uri' => 'admin::length_unit', 'key' => null, 'type' => 0],
            ['id' => 65, 'parent_id' => 0, 'sort' => 250, 'title' => 'lang::admin.menu_titles.ADMIN_SHOP_SETTING', 'icon' => 'icon-settings', 'uri' => '', 'key' => 'ADMIN_SHOP_SETTING', 'type' => 0],
            ['id' => 66, 'parent_id' => 59, 'sort' => 1, 'title' => 'lang::admin.menu_titles.api_config', 'icon' => '', 'uri' => 'admin::api_connection', 'key' => null, 'type' => 0],
            ['id' => 67, 'parent_id' => 65, 'sort' => 5, 'title' => 'lang::admin.menu_titles.layout', 'icon' => '', 'uri' => '', 'key' => null, 'type' => 0],
            ['id' => 68, 'parent_id' => 27, 'sort' => 5, 'title' => 'lang::custom_field.admin.title', 'icon' => '', 'uri' => 'admin::custom_field', 'key' => null, 'type' => 0],
            ]
        );

        DB::connection(config('const.LC_CONNECTION'))->table('admin_permission')->insert(
            [
            ['id' => '1', 'name' => 'Auth manager', 'slug' => 'auth.full', 'http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/auth/*', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '2', 'name' => 'Dashboard', 'slug' => 'dashboard', 'http_uri' => 'GET::'.config('const.LC_ADMIN_PREFIX').'', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '3', 'name' => 'Base setting', 'slug' => 'base.setting', 'http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/order_status/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/shipping_status/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/payment_status/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/supplier/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/brand/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/custom_field/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/weight_unit/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/length_unit/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/attribute_group/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/tax/*', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '4', 'name' => 'Store manager', 'slug' => 'store.full','http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/store_info/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/store_maintain/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/store_config/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/store_css/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/store_block/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/store_link/*','created_at' => date('Y-m-d H:i:s')],
            ['id' => '5', 'name' => 'Product manager', 'slug' => 'product.full','http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/product/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/product_property/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/product_tag/*','created_at' => date('Y-m-d H:i:s')],
            ['id' => '6', 'name' => 'Category manager', 'slug' => 'category.full','http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/category/*','created_at' => date('Y-m-d H:i:s')],
            ['id' => '7', 'name' => 'Order manager', 'slug' => 'order.full', 'http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/order/*', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '8', 'name' => 'Upload management', 'slug' => 'upload.full', 'http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/uploads/*', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '9', 'name' => 'Extension manager', 'slug' => 'extension.full', 'http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/template/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/plugin/*', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '10', 'name' => 'Marketing manager', 'slug' => 'marketing.full', 'http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/shop_discount/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/email_template/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/customer/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/subscribe/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/seo/*', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '11', 'name' => 'Report manager', 'slug' => 'report.full', 'http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/report/*', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '12', 'name' => 'CMS manager', 'slug' => 'cms.full', 'http_uri' => 'ANY::'.config('const.LC_ADMIN_PREFIX').'/page/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/banner/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/banner_type/*, ANY::'.config('const.LC_ADMIN_PREFIX').'/cms_category/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/cms_content/*,ANY::'.config('const.LC_ADMIN_PREFIX').'/news/*', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '13', 'name' => 'Update config', 'slug' => 'change.config', 'http_uri' => 'POST::'.config('const.LC_ADMIN_PREFIX').'/store_config/update', 'created_at' => date('Y-m-d H:i:s')],
             ]
        );

        DB::connection(config('const.LC_CONNECTION'))->table('admin_role')->insert(
            [
            ['id' => '1', 'name' => 'Administrator', 'slug' => 'administrator', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '2', 'name' => 'Group only View', 'slug' => 'view.all', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '3', 'name' => 'Manager', 'slug' => 'manager', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '4', 'name' => 'CMS', 'slug' => 'cms', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '5', 'name' => 'Accountant', 'slug' => 'accountant', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => '6', 'name' => 'Marketing', 'slug' => 'maketing', 'created_at' => date('Y-m-d H:i:s')]]
        );

        DB::connection(config('const.LC_CONNECTION'))->table('admin_role_permission')->insert(
            [
            ['role_id' => 3, 'permission_id' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 2, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 3, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 4, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 5, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 6, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 13, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 7, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 8, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 9, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 10, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 11, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 3, 'permission_id' => 12, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 4, 'permission_id' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 4, 'permission_id' => 12, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 5, 'permission_id' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 5, 'permission_id' => 2, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 5, 'permission_id' => 7, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 5, 'permission_id' => 11, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 6, 'permission_id' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 6, 'permission_id' => 2, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 6, 'permission_id' => 8, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 6, 'permission_id' => 10, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 6, 'permission_id' => 11, 'created_at' => date('Y-m-d H:i:s')],
            ['role_id' => 6, 'permission_id' => 12, 'created_at' => date('Y-m-d H:i:s')],
            ]
        );
        
        DB::connection(config('const.LC_CONNECTION'))->table('admin_role_user')->insert(
            ['role_id' => '1', 'user_id' => '1']
        );

        if (!empty(session('infoInstall')['admin_user'])) {
            $this->adminUser = session('infoInstall')['admin_user'];
        }
        if (!empty(session('infoInstall')['admin_password'])) {
            $this->adminPassword = session('infoInstall')['admin_password'];
        }
        if (!empty(session('infoInstall')['admin_email'])) {
            $this->adminEmail = session('infoInstall')['admin_email'];
        }
        DB::connection(config('const.LC_CONNECTION'))->table('admin_user')->insert(
            ['id' => '1', 'username' => $this->adminUser, 'password' => $this->adminPassword, 'email' => $this->adminEmail, 'name' => 'Administrator', 'avatar' => '/admin/avatar/user.jpg', 'created_at' => date('Y-m-d H:i:s')]
        );
        
        
        if (!empty(session('infoInstall')['timezone_default'])) {
            $this->timezone_default = session('infoInstall')['timezone_default'];
        }
        if (!empty(session('infoInstall')['language_default'])) {
            $this->language_default = session('infoInstall')['language_default'];
        }
        DB::connection(config('const.LC_CONNECTION'))->table('admin_config')->insert(
            [
            ['group' => 'Plugins', 'code' => 'Payment', 'key' => 'Cash', 'value' => '1', 'sort' => '0', 'detail' => 'Plugins/Payment/Cash::lang.title', 'store_id' => 0],
            ['group' => 'Plugins', 'code' => 'Shipping', 'key' => 'ShippingStandard', 'value' => '1', 'sort' => '0', 'detail' => 'lang::Shipping Standard', 'store_id' => 0],

            ['group' => 'global', 'code' => 'env_global', 'key' => 'ADMIN_LOG', 'value' => 'on', 'sort' => '0', 'detail' => 'lang::env.ADMIN_LOG', 'store_id' => 0],
            ['group' => 'global', 'code' => 'env_global', 'key' => 'ADMIN_LOG_EXP', 'value' => '', 'sort' => '0', 'detail' => 'lang::env.ADMIN_LOG_EXP', 'store_id' => 0],
            ['group' => 'global', 'code' => 'env_global', 'key' => 'ADMIN_FOOTER_OFF', 'value' => '0', 'sort' => '0', 'detail' => 'lang::env.ADMIN_FOOTER_OFF', 'store_id' => 0],
            ['group' => 'global', 'code' => 'seo_config', 'key' => 'url_seo_lang', 'value' => '0', 'sort' => '1', 'detail' => 'lang::seo.url_seo_lang', 'store_id' => 0],
            ['group' => 'global', 'code' => 'webhook_config', 'key' => 'LOG_SLACK_WEBHOOK_URL', 'value' => '', 'sort' => '0', 'detail' => 'lang::config.LOG_SLACK_WEBHOOK_URL', 'store_id' => 0],
            ['group' => 'global', 'code' => 'webhook_config', 'key' => 'GOOGLE_CHAT_WEBHOOK_URL', 'value' => '', 'sort' => '0', 'detail' => 'lang::config.GOOGLE_CHAT_WEBHOOK_URL', 'store_id' => 0],
            ['group' => 'global', 'code' => 'webhook_config', 'key' => 'CHATWORK_CHAT_WEBHOOK_URL', 'value' => '', 'sort' => '0', 'detail' => 'lang::config.CHATWORK_CHAT_WEBHOOK_URL', 'store_id' => 0],
            ['group' => 'global', 'code' => 'api_config', 'key' => 'api_connection_required', 'value' => '1', 'sort' => '1', 'detail' => 'lang::api_connection.api_connection_required', 'store_id' => 0],

            ['group' => 'global', 'code' => 'cache', 'key' => 'cache_status', 'value' => '0', 'sort' => '0', 'detail' => 'lang::cache.config_manager.cache_status', 'store_id' => 0],
            ['group' => 'global', 'code' => 'cache', 'key' => 'cache_time', 'value' => '600', 'sort' => '0', 'detail' => 'lang::cache.config_manager.cache_time', 'store_id' => 0],
            ['group' => 'global', 'code' => 'cache', 'key' => 'cache_category', 'value' => '0', 'sort' => '3', 'detail' => 'lang::cache.config_manager.cache_category', 'store_id' => 0],
            ['group' => 'global', 'code' => 'cache', 'key' => 'cache_product', 'value' => '0', 'sort' => '4', 'detail' => 'lang::cache.config_manager.cache_product', 'store_id' => 0],
            ['group' => 'global', 'code' => 'cache', 'key' => 'cache_news', 'value' => '0', 'sort' => '5', 'detail' => 'lang::cache.config_manager.cache_news', 'store_id' => 0],
            ['group' => 'global', 'code' => 'cache', 'key' => 'cache_category_cms', 'value' => '0', 'sort' => '6', 'detail' => 'lang::cache.config_manager.cache_category_cms', 'store_id' => 0],
            ['group' => 'global', 'code' => 'cache', 'key' => 'cache_content_cms', 'value' => '0', 'sort' => '7', 'detail' => 'lang::cache.config_manager.cache_content_cms', 'store_id' => 0],
            ['group' => 'global', 'code' => 'cache', 'key' => 'cache_page', 'value' => '0', 'sort' => '8', 'detail' => 'lang::cache.config_manager.cache_page', 'store_id' => 0],
            ['group' => 'global', 'code' => 'cache', 'key' => 'cache_country', 'value' => '0', 'sort' => '10', 'detail' => 'lang::cache.config_manager.cache_country', 'store_id' => 0],

            ['group' => 'global', 'code' => 'env_mail', 'key' => 'smtp_mode', 'value' => '', 'sort' => '0', 'detail' => 'lang::env.email.smtp_mode', 'store_id' => 0],
            ['group' => 'global', 'code' => 'multiple_store', 'key' => 'MultiStorePro', 'value' => '1', 'sort' => '0', 'detail' => 'lang::env.MULTI_STORE_PRO', 'store_id' => 0],
            ['group' => 'global', 'code' => 'domain_strict', 'key' => 'domain_strict', 'value' => '1', 'sort' => '0', 'detail' => 'lang::env.DOMAIN_STRICT', 'store_id' => 0],

            
            ]
        );
        DB::connection(config('const.LC_CONNECTION'))->table('admin_store')->insert(
            [
                'logo' => 'data/logo/Scart-mid.png', 
                'template' => 'black-cart-light', 
                'phone' => '0123456789', 
                'long_phone' => 'Support: 0987654321', 
                'email' => $this->adminEmail, 
                'time_active' => '', 
                'address' => '123st - abc - xyz', 
                'timezone' => $this->timezone_default, 
                'language' => $this->language_default, 
                'currency' => 'USD', 
                'code' => 'root', 
                'domain' => str_replace(['http://','https://', '/install.php'], '', url('/')),
            ]
        );
        
        DB::connection(config('const.LC_CONNECTION'))->table('admin_store_description')->insert(
            [
                ['store_id' => '1', 'lang' => 'en', 'title' => 'Demo black-cart : Free Laravel eCommerce', 'description' => 'Free website shopping cart for business', 'keyword' => '', 'maintain_content' => '<center><img src="/images/maintenance.png" />
    <h3><span style="color:#e74c3c;"><strong>Sorry! We are currently doing site maintenance!</strong></span></h3>
    </center>', 'maintain_note' => 'Website is in maintenance mode!'],
                ['store_id' => '1', 'lang' => 'vi', 'title' => 'Demo black-cart: Mã nguồn website thương mại điện tử miễn phí', 'description' => 'Laravel shopping cart for business', 'keyword' => '', 'maintain_content' => '<center><img src="/images/maintenance.png" />
    <h3><span style="color:#e74c3c;"><strong>Xin lỗi! Hiện tại website đang bảo trì!</strong></span></h3>
    </center>', 'maintain_note' => 'Website đang trong chế độ bảo trì!'],
            ]
        );
    }
}
