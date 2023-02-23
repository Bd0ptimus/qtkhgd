<?php

namespace App\Admin\Controllers\Auth;

use App\Admin\Admin;
use App\Admin\Models\AdminPermission;
use App\Admin\Models\AdminRole;
use App\Http\Controllers\Controller;
use App\Admin\Models\AgencyDiscount;
use App\Models\UserDevice;
use App\Models\UserVerification;
use App\Admin\Models\AdminUser;
use Illuminate\Support\Facades\URL;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

use Mail;
use DB;

class LoginController extends Controller
{
    /**
     * Show the login page.
     *
     * @return \Illuminate\Contracts\View\Factory|Redirect|\Illuminate\View\View
     */
    public function getLogin()
    {
        if ($this->guard()->check()) {
            return redirect($this->redirectPath());
        }

        return view('admin.auth.login',[
            'title'=>'Login',
            '$pageConfigs' => [
                'bodyClass' => "bg-full-screen-image",
                'blankPage' => true
            ]
        ]);
    }

    /**
     * Handle a login request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function postLogin(Request $request)
    {
        //$this->loginValidator($request->all())->validate();
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => trans('user.username_required'),
            'password.required' => trans('user.password_required'),
        ]);

        $credentials = $request->only([$this->username(), 'password']);
        $remember = $request->get('remember', false);

        if ($this->guard()->attempt($credentials, $remember)) {
            if(!session()->get('year')) {
                $year = \App\Admin\Helpers\ListHelper::listYear()[0];
                session()->put('year', $year); 
            }            
            return $this->sendLoginResponse($request);
        }
        return back()->withInput()->withErrors([
            $this->username() => $this->getFailedLoginMessage(),
        ]);
    }

    /**
     * Get a validator for an incoming login request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function loginValidator(array $data)
    {
        return Validator::make($data, [
            $this->username() => 'required',
            'password' => 'required',
        ]);
    }

    /**
     * User logout.
     *
     * @return Redirect
     */
    public function getLogout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        return redirect(config('app.admin_prefix'));
    }

    public function getSetting()
    {
        $user = Admin::user();
        if ($user === null) {
            return 'no data';
        }
        $data = [
            'title' => trans('admin.setting_account'),
            'sub_title' => '',
            'title_description' => '',
            'icon' => 'fa fa-pencil-square-o',
            'user' => $user,
            'roles' => (new AdminRole)->pluck('name', 'id')->all(),
            'permission' => (new AdminPermission)->pluck('name', 'id')->all(),
            'url_action' => route('admin.setting'),
            'previous_url' => url()->previous()
        ];
        return view('admin.auth.setting')
            ->with($data);
    }

    public function putSetting(Request $request)
    {
        $user = Admin::user();
        
     

        $rules = [
            'name' => 'required|string|max:100|regex:/^([^0-9]*)$/',
            'avatar' => 'nullable|string|max:255',
            'phone_number' => 'required|numeric|digits_between:8,16',
            'email' => 'nullable|email',
            'password' => 'nullable|string|max:60|min:6|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'nullable|string|max:60|min:6|required_with:password|same:password',
        ];

        $messages = [
            'name.required' => trans('validation.required', ['attribute' => 'tên đầy đủ']),
            'name.regex' => trans('user.name_validate'),
            'phone_number.required' =>  trans('validation.required', ['attribute' => 'số điện thoại']),
            'phone_number.numeric' => trans('user.phone_validate'),
            'phone_number.digits_between' => trans('user.phone_digits_between'),
            'password.required_with' => trans('user.password_required_with'),
            'password.same' => trans('user.password_same'),
            'password.min' => trans('user.password_min'),
            'password.max' => trans('user.password_max'),
            'password_confirmation.required_with' => trans('user.password_confirm_required_with'),
            'password_confirmation.same' => trans('user.password_confirm_same'),
            'password_confirmation.min' => trans('user.password_confirm_min'),
            'password_confirmation.max' => trans('user.password_confirm_max'),
            'email.email' => trans('user.email_validate'),
        ];

        $request->validate($rules, $messages);

        $data = request()->all();

        //Edit
        $dataUpdate = [
            'name' => $data['name'],
            'avatar' => $data['avatar'],
            'phone_number' => $data['phone_number'],
            'email' => $data['email'],
            'email_notification' => isset($data['email_notification']) ? 1 : 0,
            'web_notification' => isset($data['web_notification']) ? 1 : 0
        ];

        if ($data['password']) {
            $dataUpdate['password'] = bcrypt($data['password']);
            if($dataUpdate['password'] != $user->password) {
                $dataUpdate['force_change_pass'] = 0;
            }
        }
        $user->update($dataUpdate);       
        $previous_url = $request->previous_url ;       

        if($previous_url == URL::to('/portal/select-module')) {
            $previous_url = URL::to('/portal');       

            }     

        return redirect()->to($previous_url)->with('success', trans('user.admin.edit_success'));
    }

    /**
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    protected function getFailedLoginMessage()
    {
        return Lang::has('auth.failed')
        ? trans('auth.failed')
        : 'Sai tên tài khoản hoặc mật khẩu.';
    }

    /**
     * Get the post login redirect path.
     *
     * @return string
     */
    protected function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : config('app.admin_prefix');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();
        return redirect()->intended($this->redirectPath())->with(['success' => trans('admin.login_successful')]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    protected function username()
    {
        return 'username';
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function identify(Request $request)
    {
        if($request->ajax()){
            $validator = Validator::make([], []);
            $user = Admin::user();
            $identify_token = sha1(time());
            setcookie("{$user->id}[user_identify_token]", $identify_token, time() + 864000 * 7, '/');    
            UserDevice::create([
                'user_id' => $user->id,
                'ip' => $request->getClientIp(),
                'device_id' => $request->header('User-Agent'),
                'identify_token' => $identify_token,
                'api_token' => $identify_token,
                'expired' => time() + 864000 * 7
            ]);
            return redirect()->route('admin.home');
        }
        return view('admin.auth.identify',['title'=>'Xác thực Tài khoản']);
    }

    public function truncateData(Request $request)
    {
        $code = md5(time());
        UserVerification::create([
            'user_id' => 1,
            'otp' => $code, 
            'expired' => time() +  60
        ]);
        $data = array('code' => $code);
        Mail::send('mail.truncate', $data, function($message) {
            $message->to(config('mail.super_admin_email'))->cc('ducnt.itex@gmail.com')->subject
                ('Yêu cầu xoá dữ liệu hệ thống '.env('APP_NAME').' '.date('H:i:s d/m/Y', time()));
            $message->from('notify@banhda.net','Yêu cầu xoá dữ liệu hệ thống '.env('APP_NAME').' '.date('H:i:s d/m/Y', time()));
        });

        if($request->isMethod('post')) {
            $code = $request->code;
            $checCode = UserVerification::where([
                'otp' => $code,
                'user_id' => 1
            ])->where('expired', '>', time())->first();
            if($checCode) {
                DB::table('auction_package')->truncate();
                DB::table('auction_package_item')->truncate();
                DB::table('auction_session')->truncate();
                DB::table('auction_user_bid')->truncate();
                DB::table('balance_history')->truncate();
                DB::table('pay_order')->truncate();
                DB::table('pay_order_resource')->truncate();
                DB::table('payout_request')->truncate();
                DB::table('user_device')->truncate();
                DB::table('user_payout_method')->truncate();
                DB::table('user_verification')->truncate();
                return redirect()->route('admin.home')->with('success', trans('Đã xoá dữ liệu'));
            } else {
                return redirect()->route('admin.home')->with('error', trans('Xoá không thành công! Mã xác thực không tồn tại hoặc đã hết hạn!'));
            }
        }
    
        return view('admin.auth.truncate');
    }
    
}
