<?php
#app/Modules/Api/Controllers/ApiController.php
namespace App\Modules\Api\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ApiController extends Controller
{
    /**
     * Response success
     *
     * @param $data
     * @param $message
     * @return JsonResponse
     */
    public function respSuccess($data, $message = null)
    {
        $message = $message ?? "get data success";
        return response()->json([
            "success" => true,
            "message" => $message,
            "data" => $data
        ], Response::HTTP_OK);
    }

    /**
     * Response Error
     *
     * @param $message
     * @param $errorCode
     * @return JsonResponse
     */
    public function respError($message, $errorCode = null)
    {
        $errorCode = $errorCode ?? Response::HTTP_BAD_REQUEST;
        return response()->json([
            "success" => false,
            "message" => $message,
            "error_code" => $errorCode
        ], $errorCode);
    }
}