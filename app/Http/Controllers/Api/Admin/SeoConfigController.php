<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;

class SeoConfigController extends Controller
{

    public function __construct()
    {
    }
    
    public function index()
    {
        return lc_config_global('url_seo_lang');
    }
}
