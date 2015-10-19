<?php
namespace app\controllers;
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
include_once("REST/rest.inc.php");
include_once("REST/XmlDomConstruct.php");
include_once("REST/xmlToArray.inc.php");

class ApiController extends Controller {
    
    public $administrator_email;
    public $salt                = 'phoneDoctor';
    public $gateways            = array('INTERSWITCH', 'PAYPAL');
    public $triggerKey;
    public $currencyLabel       = 'N';  
    public $configURL;

    public function beforeAction($action) {        
        $settings = Settings::model()->findAll();
        foreach($settings as $setting) {
            switch($setting->name) {  
                
                case 'administrator_email':
                    $this->administrator_email = $setting->value;
                    break;                
                   
                case 'base_currency_code':
                    $this->base_currency_code = $setting->value;
                    break;

                case 'config_server_url':
                    $this->configURL = $setting->value;
                    break;
                default:
                    break;
            }
        }
        
        //set the home URL
        $this->homeURL = 'http://' . $_SERVER['SERVER_NAME'];
        
        $admin_email = Settings::model()->findByAttributes(array('name' => 'admin_email'));
        $this->administrator_email = $admin_email->value;
        
        $this->triggerKey = Yii::app()->params['trigger_key'];

        $available_methods = ApiMethod::model()->findAll();
        if($available_methods) {
            foreach($available_methods as $method){
                $this->api_methods[$method->name] = $method->id;
            }
        }
        return true;
    }
    
    /*
     * API Method : N.A
     * Purpose    : Parse the request data & invoke the concerned API method
     * Returns    : N.A
     */

    public function actionIndex() {
        $checkMethod = new REST();
        $requestMethod = $checkMethod->get_request_method();
        $connectionType = $checkMethod->get_connection_type();
       
        //use php://input to get the raw $_POST results
        $xmlInput = file_get_contents('php://input');

        //parse the incoming XML to array
        $xmlArray = xml2array($xmlInput);
        //echo'<pre>';print_r($xmlArray);echo'</pre>';

        if (!isset($xmlArray['request']) || !isset($xmlArray['request_attr'])) {
            $this->generateXMLResponse(array("response_code" => 112, "description" => 'Invalid request, please check your input.'), 'error', 400);
        } else if ($requestMethod <> 'POST') {
            $this->generateXMLResponse(array("response_code" => 101, "description" => 'Only POST method supported.'), 'error', 403);
        } else if ($connectionType <> 443) {
            $this->generateXMLResponse(array("response_code" => 111, "description" => 'Insecure connection.'), 'error', 406);
        } else {  
            //determine the method
            switch ($xmlArray['request_attr']['method']) {
                case 'user.create':
                    $this->createUser($xmlArray['request']);
                    break;
                case 'user.update':
                    $this->updateUser($xmlArray['request']);
                    break;
                case 'user.changePassword':
                    $this->updatePassword($xmlArray['request']);
                    break;                   
                default:
                    $this->generateXMLResponse(array("response_code" => 999, "description" => 'Unknown method.'), 'error', 400);
                    break;
            }
        }
        
    }
    
       public function createUser($xmlUserDetails) {
      //check the mandatory fields
        if (!isset($xmlUserDetails['user']['name']) || trim($xmlUserDetails['user']['name']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Name missing.');
            $this->generateXMLResponse(array("response_code" => 113, "description" => 'Name missing.'), 'error', 400);
            
        } else if (!isset($xmlUserDetails['user']['email']) || trim($xmlUserDetails['user']['email']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'User email missing.');
            $this->generateXMLResponse(array("response_code" => 113, "description" => 'User email missing.'), 'error', 400);
            
        } else if (!isset($xmlUserDetails['user']['contacts']['contact']['phone1']) || trim($xmlUserDetails['user']['contacts']['contact']['phone1']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Phone1 missing.');
            $this->generateXMLResponse(array("response_code" => 113, "description" => 'Phone1 missing.'), 'error', 400);
            
        } else if (!isset($xmlUserDetails['user']['password']) || trim($xmlUserDetails['user']['password']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Password missing.');
            $this->generateXMLResponse(array("response_code" => 113, "description" => 'Password missing.'), 'error', 400);
            
        } else if((!isset($xmlUserDetails['user']['pin']) || trim($xmlUserDetails['user']['pin']) == '') && !$specialDeviceFlag) {
                
            $this->addLogEntry('user.create', 'Failure', 9, 'PIN missing.');
            $this->generateXMLResponse(array("response_code" => 113, "description" => 'PIN missing.'), 'error', 400);

        } else {
            $userFullName = $this->sanitizeXML($xmlUserDetails['user']['name']);
            $userPhone    = $this->sanitizeXML($xmlUserDetails['user']['contacts']['contact']['phone1']);
            $pin          = $this->sanitizeXML($xmlUserDetails['user']['pin']);

            if(!isset($xmlUserDetails['user']['username']) || trim($xmlUserDetails['user']['username']) == '') {
                $userName = preg_replace('/\s+/', '', $userFullName); //remove any whitespaces
            } else {
                $userName = $xmlUserDetails['user']['username'];
            }
            
            //check if phone no. has been pre-verified
            if(!$specialDeviceFlag && (isset($xmlUserDetails['user']['preverified']) && trim($xmlUserDetails['user']['preverified']) == 1)) {
                $pre_verified_user = true;
            } else {
                $pre_verified_user = false;
            }
            
            //check if username is available
            $userFlag = true;
            if($this->checkIfUserExists($userName)){
                while($userFlag){
                    $userName .= rand(100, 999);
                    $userFlag = $this->checkIfUserExists($userName);
                }
            }
            
            //check if email is already in use
            $email_exists = MerchantDetails::model()->findByAttributes(array('email' => $this->sanitizeXML($xmlUserDetails['user']['email'], true)));
            
            //check if phone no. is already in use
            $phone_exists = MerchantDetails::model()->findByAttributes(array('phone_no' => $userPhone));
            
            if(preg_match('/[^A-Za-z0-9]/', $userName)) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Illegal characters in username.');
                $this->generateXMLResponse(array("response_code" => 113, "description" => 'Illegal characters in username. Only alphanuerics allowed.'), 'error', 400);
                
            } else if(!$this->validateEmail($this->sanitizeXML($xmlUserDetails['user']['email'], true))) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Invalid email.');
                $this->generateXMLResponse(array("response_code" => 113, "description" => 'Invalid email.'), 'error', 400);
                
            } else if($email_exists) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Email [' . $this->sanitizeXML($xmlUserDetails['user']['email']) . '] already in use.');
                $this->generateXMLResponse(array("response_code" => 113, "description" => 'Email already in use.'), 'error', 400);
                
            } else if($phone_exists) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Phone [' . $userPhone . '] already in use.');
                $this->generateXMLResponse(array("response_code" => 113, "description" => 'Phone already in use.'), 'error', 400);
                
            } else if((!is_numeric($pin) || strlen($pin) <> 4) && !$specialDeviceFlag) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'PIN length/type error.');
                $this->generateXMLResponse(array("response_code" => 113, "description" => 'PIN must consist of numbers only with a length of 4.'), 'error', 400);
                
            } else {
            
                //create a new merchant account
                $model_user         = new User;
                $model              = new MerchantDetails;
                $model_accounts     = new Accounts;
                $terminal_val       = new Terminals;
                $phone_verification = new UserPhoneVerification;

                $user_pass          = $this->sanitizeXML($xmlUserDetails['user']['password'], true) <> '' ? $this->sanitizeXML($xmlUserDetails['user']['password'], true) : 'seCret!7';
                $user_address       = $this->sanitizeXML($xmlUserDetails['user']['p_street1']) . ', ' . $this->sanitizeXML($xmlUserDetails['user']['p_street2']);
                $user_address       = $user_address <> '' ? $user_address : 'N.A';

                $city               = trim($xmlUserDetails['user']['p_city']) <> '' ? $this->sanitizeXML($xmlUserDetails['user']['p_city']) : 'N.A';
                $state              = trim($xmlUserDetails['user']['p_state']) <> '' ? $this->sanitizeXML($xmlUserDetails['user']['p_state']) : 'N.A';
                $mobile_no          = trim($xmlUserDetails['user']['contacts']['contact']['phone1']) <> '' ? $this->sanitizeXML($xmlUserDetails['user']['contacts']['contact']['phone1']) : '0000000000';
                $contact_phone_no   = trim($xmlUserDetails['user']['contacts']['contact']['phone1']) <> '' ? $this->sanitizeXML($xmlUserDetails['user']['contacts']['contact']['phone1']) : '0000000000';
                $contact_mobile_no  = trim($xmlUserDetails['user']['contacts']['contact']['phone2']) <> '' ? $this->sanitizeXML($xmlUserDetails['user']['contacts']['contact']['phone2']) : '0000000000';
                $contact_name       = $userFullName;
                $activate_key       = time() . rand(1000, 9999);

                $model_user->username       = $userName;
                $model_user->password       = md5($user_pass);
                $model_user->createtime     = (int) time();
                $model_user->role           = 'MOBILE';
                //USSD requires auto account activation
                if (!$specialDeviceFlag && !$pre_verified_user) {
                    $model_user->status         = 2;
                    $model_user->activationKey  = $activate_key;
                } else {
                    $model_user->status         = 1;
                    $model_user->activationKey  = 'ACTIVATED';                    
                }
                $model_user->save();

                $model->user_id             = $model_user->id;
                $model->email               = $this->sanitizeXML($xmlUserDetails['user']['email'], true);
                $model->no_of_terminals     = 1;
                $model->company_name        = 'N.A';
                $model->address             = $user_address;
                $model->phone_no            = $mobile_no;
                $model->mobile_no           = $mobile_no;
                $model->contact_name        = trim($contact_name);
                $model->contact_email       = $this->sanitizeXML($xmlUserDetails['user']['email'], true);
                $model->contact_mobile_no   = $contact_mobile_no;
                $model->contact_phone_no    = $contact_phone_no;
                $model->city                = $city;
                $model->state               = $state;
                $model->plan                = 'N.A';
                $model->retailer_id         = $this->getRetailerID();
                $model->save();

                $plan = Plans::model()->find("name = '" . $model->plan . "'");
                $acct = Accounts::model()->findBySql("SELECT account_no FROM accounts WHERE account_no LIKE '50%' ORDER BY account_no DESC LIMIT 1");
                $model_accounts->account_no         = $acct == NULL ? 5000000001 : $acct->account_no + 1;
                $model_accounts->user               = $model_user->id;                
                $model_accounts->plan               = $plan->id;
                $model_accounts->status             = 1;
                $model_accounts->account_status     = 1;
                $model_accounts->balance            = 0; //set initial balance as 0
                $model_accounts->key                = $this->triggerKey; //security validator
                $model_accounts->save();

                $term = Terminals::model()->findBySql("SELECT terminal_id FROM terminals WHERE terminal_id LIKE '50%' ORDER BY terminal_id DESC LIMIT 1");
                $terminal_val->terminal_id              = $term == NULL ? 5000000001 : $term->terminal_id + 1;
                $terminal_val->account                  = $model_accounts->account_no;
                $terminal_val->retailer_id              = $model->retailer_id;
                $terminal_val->user                     = $model_user->id;
                $terminal_val->plan                     = $plan->id;
                $terminal_val->retailer_name            = trim($contact_name);
                $terminal_val->retailer_location        = $city;
                $terminal_val->retailer_phone           = $contact_mobile_no;
                $terminal_val->status                   = 1; //1 seems to be active
                $terminal_val->save();
                
                /*--------------------- Send SMS to user ---------------------*/
                $phone_verification_code = rand(100000, 999999);               
                
                $phone_verification->user_id                            = $model_user->id;
                $phone_verification->primary_phone_verification_code    = $phone_verification_code;
                if ($specialDeviceFlag || $pre_verified_user) {
                    $phone_verification->primary_phone_verification_status = 'VERIFIED'; //set as verified for USSD
                }
                $phone_verification->save();
                
                //phone verification not needed for USSD
                if (!$specialDeviceFlag && !$pre_verified_user) {
                    $twilioResp = TwilioresponseController::sendSMS($phone_verification_code, $this->twilio_account_sid, $this->twilio_auth_token, $mobile_no, $this->twilio_from_phone);

                    if(isset($twilioResp['error'])) {
                        $this->addLogEntry('user.create', 'Failure', 9, 'Twilio SMS status :- ' . serialize($twilioResp), $model_user->id);
                    } else {
                        $this->addLogEntry('user.create', 'Success', 3, 'Twilio SMS status :- ' . serialize($twilioResp), $model_user->id);
                    }
                }
                
                /* -------------Add an entry in AuthAssignment tbl----------- */
                $criteria = new CDbCriteria;
                $criteria->addCondition("userid = $model_user->id");
                $auth_tbl_entry_check = AuthAssignment::model()->find($criteria);

                if (!$auth_tbl_entry_check) {
                    $auth_model = new AuthAssignment;
                    $auth_model->itemname = 'Mobile'; //changed from Merchant
                    $auth_model->userid = $model_user->id;
                    $auth_model->data = 'N;';

                    $auth_model->save();
                }

                $invoice_array = array();
                $account_num_array = array();
                $total_amount = 0;

                //------------update the array to be passed to Freshbooks API--------------
                $account_num_array[] = $model_accounts->account_no;

                $invoice_array['line'][0]['name'] = 'Chitbox plan';
                $invoice_array['line'][0]['unit_cost'] = (float) $plan->amount;
                $invoice_array['line'][0]['quantity'] = 1;
                $invoice_array['line'][0]['description'] = $plan->name . ' [' . $model_accounts->account_no . ']';
                $invoice_array['line'][0]['type'] = 'Item';

                $invoice_array['line'][1]['name'] = 'Chitbox device';
                $invoice_array['line'][1]['unit_cost'] = 0.00;
                $invoice_array['line'][1]['quantity'] = 1;
                $invoice_array['line'][1]['description'] = 'Terminal : ' . $terminal_val->terminal_id;
                $invoice_array['line'][1]['type'] = 'Item';
                
                //need to create random transaction PIN for USSD requests
                if ($specialDeviceFlag) {
                    $new_pin = rand(1000, 9999);
                } else {
                    $new_pin = $pin;
                }                    
                $user_pin = new TransactionsPins;
                $user_pin->account_no   = $model_accounts->account_no;
                $user_pin->pin          = md5($new_pin . $this->salt); //add a salt to improve security
                $user_pin->set_date     = date('Y-m-d');
                $user_pin->update_date  = date('Y-m-d', strtotime('+60 days')); //set it 60 days from current date     

                $user_pin->save();                

                /*--------------Send notification email to admin-------------------*/
                $mail_content   = Cms::model()->findByAttributes(array('name' => 'user_mobile_signup_admin'));
                $mail_title     = $mail_content->title;
                $mail_tmp_body  = $mail_content->content;
                $mail_body      = str_replace(array('{DATE}', '{USERNAME}', '{EMAIL}', '{COMPANY}', '{PHONE}', '{CONTACT_NAME}', '{RETAILER_ID}'),
                                              array(date('m-d-Y'), $model_user->username, $model->email, $model->company_name, $model->phone_no, $model->contact_name, $model->retailer_id),
                                              $mail_tmp_body);

                //get the list of all notification emails
                $emails_array = array();
                $email_list = Settings::model()->findAllByAttributes(array('name' => 'notification_email'));
                if($email_list){
                    foreach($email_list as $emails){
                        $emails_array[] = $emails->value;
                    }
                }

                $this->sendSystemEmail($mail_body, $mail_title, $this->administrator_email, $emails_array);

                //activation link not needed for USSD [send transaction PIN instead]
                if (!$specialDeviceFlag && !$pre_verified_user) {
                    $activate_link  = "click <a href='" . $this->homeURL . '/mobile/index.php?r=site/emailvalidate&activateKey='. $activate_key ."'>HERE</a> to activate your account.";
                } else {
                    $activate_link  = '';
                }
                $authKey            = md5($model_user->username);
                $deactivate_link    = "<a href='" . $this->homeURL . '/mobile/index.php?r=site/userdeactivate&userAuth='. $authKey ."'>HERE</a>";

                /*--------------Send notification email to merchant-------------------*/
                $mail_content   = Cms::model()->findByAttributes(array('name' => 'mobile_signup_activate'));
                $mail_title     = $mail_content->title;
                $mail_tmp_body  = $mail_content->content;
                $mail_body      = str_replace(array('{DATE}', '{USER}', '{USERNAME}', '{PASSWORD}', '{ACTIVATE_LINK}', '{DEACTIVATE_LINK}', '{PIN}'),
                                              array(date('m-d-Y'), $model->contact_name, $model_user->username, $user_pass, $activate_link, $deactivate_link, $new_pin),
                                              $mail_tmp_body);                
                
                $this->sendSystemEmail($mail_body, $mail_title, $model->email);
              

                /*--------------Send transaction details to merchant-------------------*/
                $trans_id = date('ymdHis');

                $logArray[0] = array('account_no' => '1000000002', 'debit' => $plan->amount, 'credit' => 0, 'trans_id' => $trans_id, 'trans_code' => '616', 'sort' => 1);
                $logArray[1] = array('account_no' => '1000000000', 'debit' => $plan->amount, 'credit' => 0, 'trans_id' => $trans_id, 'trans_code' => '111', 'sort' => 2);
                $logArray[2] = array('account_no' => '1000000000', 'debit' => 0, 'credit' => $plan->amount, 'trans_id' => $trans_id, 'trans_code' => '101', 'sort' => 3);
                $logArray[3] = array('account_no' => $model_accounts->account_no, 'debit' => 0, 'credit' => $plan->amount, 'trans_id' => $trans_id, 'trans_code' => '303', 'sort' => 4);

                $this->addTransactionLogs($logArray);
                
                //set the balance
                $created_account = Accounts::model()->findByPk($model_accounts->id);
                $created_account->balance = $plan->amount;
                $created_account->key     = $this->triggerKey; //security validator
                $created_account->save();
                
                //format the response array
                if ($specialDeviceFlag) {
                    $response_array['PIN']          = $new_pin;
                    $response_array['account_id']   = $model_accounts->account_no;
                } else {
                    $response_array = array('retailer_id' => $model->retailer_id, 
                                            'account_id' => $model_accounts->account_no, 
                                            'terminal_id' => $terminal_val->terminal_id, 
                                            'user_id' => $model_user->id,
                                            'username' => $model_user->username,
                                            'password' => $user_pass,
                                            'pin' => $new_pin,
                                            'base_currency_code' => $this->base_currency_code);
                }
                
                /* ------------------------ Return retailer_id, terminal_id and account_id ------------------------ */
                $this->addLogEntry('user.create', 'Success', 3, 'User successfully created. Username :- ' . $model_user->username, $model_user->id);
                $this->generateXMLResponse(array("response_code" => 100, "user" => $response_array), 'ok', 200);
                exit;
            }
        }
       }

   
    /*
     * API Method : N.A
     * Purpose    : Add an entry in the api_log tbl
     * Returns    : Response of the operation
     */
    
    public function addLogEntry($api_method, $type, $log_description, $notes = '', $user_id = 0, $trans_id = 0) {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $remote_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $remote_ip = $_SERVER['REMOTE_ADDR'];
        }        
       
        $t      = microtime(true);
        $micro  = sprintf("%06d",($t - floor($t)) * 1000000);
        $d      = new DateTime( date('Y-m-d H:i:s.' . $micro, $t) );
        $date   = $d->format("Y-m-d H:i:s.u");
        
        $api_log = new ApiLog;
        $api_log->api_method_id             = $this->api_methods[$api_method];
        $api_log->type                      = $type;
        $api_log->api_log_description_id    = $log_description;
        $api_log->notes                     = $notes;
        $api_log->user_id                   = $user_id;
        $api_log->created                   = $date;
        $api_log->device_ip_address         = $remote_ip;
        $api_log->trans_id                  = $trans_id;
        
        $api_log->save(false);
        return true;
    }       
}
