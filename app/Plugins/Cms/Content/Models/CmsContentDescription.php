<?php
#app/Plugins/Cms/Content/Models/CmsContentDescription.php
namespace App\Plugins\Cms\Content\Models;

use Illuminate\Database\Eloquent\Model;

class CmsContentDescription extends Model
{
    protected $primaryKey = ['lang', 'content_id'];
    public $incrementing  = false;
    protected $guarded    = [];
    public $timestamps    = false;
    public $table = 'cms_content_description';
    protected $connection = LC_CONNECTION;
}
