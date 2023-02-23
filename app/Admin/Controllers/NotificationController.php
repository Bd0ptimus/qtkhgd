<?php 
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Admin\Admin;
use App\Admin\Models\AdminUser;
use App\Models\Notification;
use DB;

class NotificationController extends Controller
{

    public function index(Request $request){

        $notifications = Notification::where('user_id', Admin::user()->id)->orderBy('read', 'ASC')->get();
        return view('admin.notification.index', [
            'notifications' => $notifications,
            'title' => 'Thông báo'
        ]);
    }

    public function view (Request $request, $id) 
    {
        Notification::where('id', $id)->update(['read' => 1]);
        return redirect()->route('notification.index');
    }
}