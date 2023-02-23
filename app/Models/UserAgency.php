<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Models\AdminUser;
use App\Admin\Admin;
use App\Models\District;


class UserAgency extends Model
{
    public $table           = 'user_agency';
    protected $guarded      = [];
    private static $getList = null;
}