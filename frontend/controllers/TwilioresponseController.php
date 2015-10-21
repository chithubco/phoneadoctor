<?php

include_once("REST/rest.inc.php");
include_once("REST/XmlDomConstruct.php");
include_once("REST/xmlToArray.inc.php");

class TwilioresponseController extends Controller {
    
    public $api_methods;
    public $twilio_from_phone;
    public $twilio_from_phone2;
    public $twilio_from_phone3;
    public $url;
    public $country_code = '+234';
    public static $static_country_code = '+234';
    
    public function beforeAction($action) {
        $available_methods = ApiMethod::model()->findAll();
        if($available_methods) {
            foreach($available_methods as $method){
                $this->api_methods[$method->name] = $method->id;
            }
        }
        
        $settings = Settings::model()->findAll();
        foreach($settings as $setting){
            switch($setting->name){
                case 'twilio_from_phone':
                    $this->twilio_from_phone = $setting->value;
                    break;
                case 'twilio_from_phone2':
                    $this->twilio_from_phone2 = $setting->value;
                    break;
                case 'twilio_from_phone3':
                    $this->twilio_from_phone3 = $setting->value;
                    break;
                default:
                    break;
            }
        }        
        $this->url = 'https://' . $_SERVER['SERVER_NAME'] . '/phoneadoctor';
        
        return true;
    }
    
    /*
     * Receive SMS & process it
     */
    public function actionIndex() {
        //format the incoming no.
        $sender_no    = str_replace($this->country_code, '0', $_REQUEST['From']);
        $sender_no2   = str_replace($this->country_code, '', $_REQUEST['From']);
        
        $receiver_no  = $_REQUEST['To'];
        
        $user = User::model()->findByAttributes(array('phone' => $sender_no));
        if(!user) {
            //check with the backup number without preceeding 0
            $user = User::model()->findByAttributes(array('phone' => $sender_no2));
        }
        
        if($receiver_no == $this->twilio_from_phone) {
            
            //Its a case of account/phone no. authentication
            if($user) {

                $is_user_validated       = User::model()->findByAttributes(array('id' => $user->user_id));
                $is_user_phone_validated = UserPhoneVerification::model()->findByAttributes(array('user_id' => $user->user_id));

                //is the user response correct
                if($is_user_phone_validated && $is_user_phone_validated->primary_phone_verification_status == 'PENDING') {

                    if($is_user_phone_validated->primary_phone_verification_code == trim($_REQUEST['Body'])) {

                        $this->addLogEntry('user.create', 'Success', 3, 'Authentication [Twilio] :- Activation successful.', $user->user_id);
                        // send success notification
                        $message = "<Message>Activation successful!</Message>\n\r";

                        //update user details
                        $is_user_validated->activationKey   = 'ACTIVATED';
                        $is_user_validated->status          = 1;
                        $is_user_validated->save();

                        //update phone verification status
                        $is_user_phone_validated->primary_phone_verification_status = 'VERIFIED';
                        $is_user_phone_validated->save();

                    } else {

                        $this->addLogEntry('user.create', 'Failure', 9, 'Authentication [Twilio] :- Incorrect verification code [' . trim($_REQUEST['Body']) . '].', $user->user_id);
                        //alert the user
                        $message = "<Message>Sorry! The code you sent was incorrect, please try again or enter the correct code in the verification page.</Message>\n\r";

                    }                    

                } else {

                    $this->addLogEntry('user.create', 'Failure', 9, 'Authentication [Twilio] :- Duplicate activation attempt.', $user->user_id);
                    //alert the user
                    $message = "<Message>Activation already done!</Message>\n\r";

                }

            } else {

                $this->addLogEntry('user.create', 'Failure', 9, 'Authentication [Twilio] :- Unknown phone no. [' . $sender_no . '].');
                //alert the user
                $message = "<Message>Unknown number. Please contact support@chithub.com.</Message>\n\r";

            }
            
            //send the reply
            $this->sendSMSResponse($message);            
        }
        
    }
    
    /*
     * Send SMS to the phone no.
     */
    public static function sendSMS($message, $account_sid, $auth_token, $receiver_phone, $sender_phone, $sms_type = 'verification_code') {
        include_once(dirname(__FILE__) . '/../extensions/twilio/Services/Twilio.php');
        
        $client = new Services_Twilio($account_sid, $auth_token); 
        
        /*
        * 234 is Nigeria's country code, strip preceeding zeros and append 234
        */
        $receiver_phone = preg_replace('/[^A-Za-z0-9]/', '', $receiver_phone); //remove any formatting
        $receiver_phone = preg_replace('/^234/', '', $receiver_phone);         //remove country code if already present
        $receiver_phone = preg_replace('/^0+/', '', $receiver_phone);
        $receiver_phone = TwilioresponseController::$static_country_code . $receiver_phone;
        
        if($sms_type == 'verification_code') {
            $sms_text = "Your verification code is $message. To activate your account, you can send your verification code as a reply to this sms or enter it in the verification page.";
        } else if($sms_type == 'invite') {
            $sms_text = $message;
        } else {
            $sms_text = "Your confirmation code is : $message.";
        }

        try {
            $res = $client->account->sms_messages->create($sender_phone, $receiver_phone, $sms_text);
        } catch (Exception $e) {
            return array('error' => true, 'description' => $e->getMessage());
        } 
        
        return array('sid' => $res->sid, 'created_date' => $res->date_created, 'cost' => $res->price, 'cost_unit' => $res->price_unit, 'account_sid' => $res->account_sid, 'status' => $res->status, 'to' => $res->to);
    }
    
    /*
     * Add log entry
     */
    private function addLogEntry($api_method, $type, $log_description, $notes = '', $user_id = 0, $trans_id = 0) {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $remote_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $remote_ip = $_SERVER['REMOTE_ADDR'];
        }
        
        $api_log = new ApiLog;
        $api_log->api_method_id             = $this->api_methods[$api_method];
        $api_log->type                      = $type;
        $api_log->api_log_description_id    = $log_description;
        $api_log->notes                     = $notes;
        $api_log->user_id                   = $user_id;
        $api_log->created                   = date("Y-m-d h:i:s");
        $api_log->device_ip_address         = $remote_ip;
        $api_log->trans_id                  = $trans_id;
        
        $api_log->save(false);
        return true;
    }
    
    /*
     * MISCELLANEOUS FUNCTIONS
     * =========================
     * generateXMLRequest($request, $method)
     * processCURL($data)
     * sendSMSResponse($message)
     */

    private function generateXMLRequest($request, $method) {

        $dom = new XmlDomConstructCustomizedForAPI('1.0', 'utf-8');
        $dom->parseMixed(array("request" => $request));
        $data = $dom->saveXML();

        $data = str_replace('<request/>', '<request method="' . $method . '"/>', $data);
        $data = str_replace('<request>', '<request method="' . $method . '">', $data);

        return $data;
    }

    private function processCURL($data, $isPost = true) {

        $curl_respose = array();

        $ch = curl_init();    // initialize curl handle
        if($isPost) {
            curl_setopt($ch, CURLOPT_URL, $this->url); // set url to post to   
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields
            
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->url . $data);
        }
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        curl_setopt($ch, CURLOPT_TIMEOUT, 120); // times out after 120s
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            $curl_respose['status'] = 'error';
            $curl_respose['response'] = 'A cURL error occured: ' . curl_error($ch);
        } else {
            $curl_respose['status'] = 'success';
            $curl_respose['response'] = $result;
            curl_close($ch);
        }

        return $curl_respose;
    }
    
    private function sendSMSResponse($message) {
        $response_text  = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n\r";
        $response_text .= "<Response>\n\r";
        $response_text .= $message;
        $response_text .= "</Response>";  

        ob_start();
        header("content-type: text/xml");
        echo $response_text;
        exit;
    }
}