<?php
include_once('../lib/request.php');


class VerifyAuth extends Request {
    public $paRes;
    public function __construct(){
        parent::__construct();
    }

    public function setVerifyAuth() {
        $paymentCartVerifyAuthParam = [
            "authenticationToken" => (string) $this->authenticationToken,
            "ntpID" => (string) $this->ntpID,
            "formData" => [
                "paRes" => (string) $this->paRes
            ]
        ];

        return (json_encode($paymentCartVerifyAuthParam));
    }

    // Send request to /payment/card/verify-auth
    public function sendRequestVerifyAuth($jsonStr) {
        $url = $this->isLive ? 'https://secure.mobilpay.ro/pay/payment/card/verify-auth' : 'https://secure.sandbox.netopia-payments.com/payment/card/verify-auth';
        $ch = curl_init($url);
    
        $headers  = [
            'Authorization: '.$this->apiKey,
            'Content-Type: application/json'
        ];

        $payload = $jsonStr; // json DATA
    
        // Attach encoded JSON string to the POST fields
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        
        // Return response instead of outputting
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    

        // Set the content type to application/json
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // Execute the POST request
        $result = curl_exec($ch);
        
        if (!curl_errno($ch)) {
                switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                    case 200:  # OK  
                        $arr = array(
                            'status'  => 1,
                            'code'    => $http_code,
                            'message' => "Successfully verify authentication ",
                            'data'    => json_decode($result)
                        );
                        break;
                    case 404:  # Not Found
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "You send verify-auth request to wrong URL",
                            'data'    => json_decode($result)
                        );
                        break;
                    case 400:  # Bad Request
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "You send Bad Request to verify-auth",
                            'data'    => json_decode($result)
                        );
                        break;
                    case 401:  # Authorization required
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "You send without authorization",
                            'data'    => json_decode($result)
                        );
                        break;
                    case 405:  # Method Not Allowed
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "Your method of sending data to verify-auth are Not Allowed",
                            'data'    => json_decode($result)
                        );
                        break;
                    default:
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "Opps! Something is wrong, verify how you send data to verify-auth & try again!!!",
                            'data'    => json_decode($result)
                        );
                }
            } else {
                $arr = array(
                    'status'  => 0,
                    'code'    => 0,
                    'message' => "Opps! There is some problem, you are not able to send data to verify-auth!!!"
                );
            }
        
        // Close cURL resource
        curl_close($ch);
        
        $finalResult = json_encode($arr, JSON_FORCE_OBJECT);
        return $finalResult;
    }
}