<?php 
class Start {
    public $posSignature;
    public $notifyUrl;
    public $redirectUrl;
    public $cancelUrl;
    public $apiKey;
    public $isLive;
    public $backUrl;

    
    function __construct(){
        //
    }

        // Send request json
        protected function sendRequest($jsonStr) {
            if(!isset($this->apiKey) || is_null($this->apiKey)) {
                throw new \Exception('INVALID_APIKEY');
                exit;
            }

            $url = $this->isLive ? 'https://secure.mobilpay.ro/pay/payment/card/start' : 'https://secure.sandbox.netopia-payments.com/payment/card/start';
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
                            'message' => "You send your request, successfully",
                            'data'    => json_decode($result)
                        );
                    break;
                    case 404:  # Not Found 
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "You send request to wrong URL",
                            'data'    => json_decode($result)
                        );
                    break;
                    case 400:  # Bad Request
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "You send Bad Request",
                            'data'    => json_decode($result)
                        );
                    break;
                    case 401:  # Authorization required
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "Authorization required",
                            'data'    => json_decode($result)
                        );
                    break;
                    case 405:  # Method Not Allowed
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "Your method of sending data are not Allowed",
                            'data'    => json_decode($result)
                        );
                    break;
                    default:
                        $arr = array(
                            'status'  => 0,
                            'code'    => $http_code,
                            'message' => "Opps! Something is wrong, verify how you send data & try again!!!",
                            'data'    => json_decode($result)
                        );
                    break;
                }
            } else {
                $arr = array(
                    'status'  => 0,
                    'code'    => 0,
                    'message' => "Opps! There is some problem, you are not able to send data!!!"
                );
            }
            
            // Close cURL resource
            curl_close($ch);
            
            $finalResult = json_encode($arr, JSON_FORCE_OBJECT);
            return $finalResult;
        }
}