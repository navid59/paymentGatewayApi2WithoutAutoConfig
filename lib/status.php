<?php 
class Status extends Start{
    public $ntpID;
    public $orderID;
    public $apiKey;
    public $posSignature;
    
    function __construct(){
        parent::__construct();
    }

    public function validateParam() {
        if(!isset($this->apiKey) || empty($this->apiKey)){
            throw new \Exception('apiKey is not defined');
            exit;
        }
        if(!isset($this->posSignature) || empty($this->posSignature)){
            throw new \Exception('posSignature is not defined');
            exit;
        }
        if(!isset($this->ntpID) || empty($this->ntpID)){
            throw new \Exception('ntpID is not defined');
            exit;
        }
        if(!isset($this->orderID) || empty($this->orderID)){
            throw new \Exception('orderID is not defined');
            exit;
        }
    }

    public function setStatus() {
        $paymentStatusParam = [
            "posID" => (string) $this->posSignature,
            "ntpID" => (string) $this->ntpID,
            "orderID" => (string) $this->orderID
        ];

        return (json_encode($paymentStatusParam));
    }

        // Send request to get payment status
        public function getStatus($jsonStr) {  
            $this->validateParam();

            $url = $this->isLive ? 'https://secure.netopia-payments.com/operation/status' : 'https://secure.sandbox.netopia-payments.com/operation/status';
            $ch = curl_init($url);
        
            $headers  = [
                'Authorization: '.$this->apiKey,
                'Content-Type: application/json'
            ];
            
            $payload = $jsonStr; // json DATA
        
            // Attach encoded JSON string to the POST fields
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        
            // Set the content type to application/json
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
            // Return response instead of outputting
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
            // Execute the POST request
            $result = curl_exec($ch);
            // die(print_r($result));

            if (!curl_errno($ch)) {
                    switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                        case 200:  # OK                                    
                            $arr = array(
                                'status'  => 1,
                                'code'    => $http_code,
                                'message' => "Successfully get payment status ",
                                'data'    => json_decode($result)
                            );
                            break;
                        case 400:  # Bad Request
                            $arr = array(
                                'status'  => 0,
                                'code'    => $http_code,
                                'message' => "You send Bad Request to /operation/status",
                                'data'    => json_decode($result)
                            );
                            break;
                        case 404:  # Not Found                            
                            $arr = array(
                                'status'  => 0,
                                'code'    => $http_code,
                                'message' => "You  request to wrong URL",
                                'data'    => json_decode($result)
                            );
                            break;
                        case 401:  # Not Allowed                           
                            $arr = array(
                                'status'  => 0,
                                'code'    => $http_code,
                                'message' => "Not allowed request",
                                'data'    => json_decode($result)
                            );
                            break;
                        case 405:  # Method Not Allowed
                            $arr = array(
                                'status'  => 0,
                                'code'    => $http_code,
                                'message' => "Your method of sending data are Not Allowed",
                                'data'    => json_decode($result)
                            );
                            break;
                        default:
                            $arr = array(
                                'status'  => 0,
                                'code'    => $http_code,
                                'message' => "Opps! Something is wrong, verify how you send data to /operation/status & try again!!!",
                                'data'    => json_decode($result)
                            );
                    }
                } else {
                    $arr = array(
                        'status'  => 0,
                        'code'    => 0,
                        'message' => "Opps! There is some problem, you are not able to send data to /operation/status!!!"
                    );
                }
            
            // Close cURL resource
            curl_close($ch);
            
            $finalResult = json_encode($arr, JSON_FORCE_OBJECT);
            return $finalResult;
        }
}