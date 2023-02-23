<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Admin\Models\AdminUser;
use App\Admin\Admin;
use App\Models\Province;
use App\Models\District;

class SystemDocument extends Model
{
    public $table           = 'system_documents';
    protected $guarded      = [];

    const DOCUMENT_TYPE = [0 => 'Thông tin chung', 1 => 'Tài liệu nghiệp vụ', 2 => 'Thông tư'];

    public static function bytesToHuman($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function creator(){
        return $this->belongsTo(AdminUser::class, 'creator', 'id');
    }

    public function canAccessDocument(){
        if(Admin::user()->isAdministrator()){
            return true;
        }
        $user = Admin::user();
        $creator = AdminUser::find($this->creator_id);
        // Todo: check role permissions
    }
}