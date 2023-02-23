<?php 
namespace App\Library\Helpers;

Class Sms {
    
    const HOST = "https://sms.banhda.net";
    const EMAIL = "hotro.banhda@gmail.com";
    const PASSWORD = "banhda999";
    

    public static function SendSingleMessage($number, $message, $device = 1)
    {
        $url = self::HOST . "/services/send.php";
        $postData = array('messages' => json_encode([['number' => $number, 'message' => $message]]), 'email' => self::EMAIL, 'password' => self::PASSWORD, 'devices' => $device);
        return self::SendRequest($url, $postData);
    }

    public static function SendSMS($number, $message) {
        $url = self::HOST. "/service/send.php";
        $postData = array('number' => $number,'message' => $message,'email' => self::EMAIL,'password' => self::PASSWORD);
        return self::SendRequest($url, $postData);
    }
    
    public static function SendBulkSMS($bulk) {
        $url = self::HOST. "/service/send.php";
        $postData = array('bulk' =>json_encode($bulk), 'email' => self::EMAIL, 'password' => self::PASSWORD);
        return self::SendRequest($url, $postData);
    }
    
    public static function SendRequest($url, $postData) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        if ($httpCode == 200) {
            $json = json_decode($response, true);
            if($json == false) {
                if(empty($response)) {
                    return "Missing data in request. Please provide all the required information to send messages.";
                } else {
                    return $response;
                }
            }
            else if (!$json["success"]) {
                return $json["error"]["message"];
            }
        } else {
            return "HTTP Error Code : {$httpCode}";
        };
        return false;
    }
    
    public static function CreatePostString($postData) {
        $postString = "";
        $first = true;
        foreach ($postData as $key => $postData_value) {
            if($first){
                $first = false;
            }
            else{
                $postString .= "&";
            }
            $postString .= urlencode($key);
            $postString .= "=";
            $postString .= urlencode($postData_value);
        }
        return $postString;
    }
}