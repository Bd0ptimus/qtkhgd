<?php

namespace App\Modules\Api\Controllers;

use App\Admin\Models\AdminUser;
use App\Modules\Api\Requests\Auth\ChangePasswordRequest;
use App\Modules\Api\Requests\Auth\LoginRequest;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends ApiController
{
    /**
     * Login for parents
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('username', 'password');
        $credentials = array_merge($credentials, ['status' => AdminUser::STATUS_ACTIVE]);

        $token = auth('api')->attempt($credentials);
        if ($token) {
            /** @var $user AdminUser */
            $user = auth('api')->user();

            // Only allow parents user login
            if ($user->isRole('parents')) {
                return $this->respWithToken($user, $token);
            }
        }


        return $this->respError(trans('api.unauthenticated'));
    }

    /**
     * Get the token array structure.
     *
     * @param Authenticatable $user
     * @param string $token
     *
     * @return JsonResponse
     */
    protected function respWithToken($user, $token)
    {
        $user['token'] = $token;

        return $this->respSuccess($user);
    }

    /**
     * Get user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkUser(Request $request)
    {
        return $this->respWithToken($request->user(), $this->getToken());
    }

    /**
     * Get Token
     *
     * @return mixed
     */
    protected function getToken()
    {
        return auth('api')->getToken('token')->get();
    }

    /**
     * Change password
     *
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        /** @var $user AdminUser */
        $data = $request->only(['password', 'new_password']);
        $user = $request->user();

        if (Hash::check($data['password'], $user['password'])) {
            $user->update([
                'password' => bcrypt($data['new_password']),
                'force_change_pass' => 0
            ]);
            return $this->respWithToken($user, $this->getToken());
        }

        return $this->respError(trans('api.password'));
    }
}