<?php

namespace app\controllers;
namespace frontend\controllers;
//namespace api\modules\v1\controllers;

use Yii;
use yii\db\Query;
use app\models\User;
use app\models\UserSearch;
use app\models\Patient;
use app\models\VerifyPhone;
use app\models\PatientAllergies;
use app\models\PatientMedications;
use app\models\PatientActiveProblems;
use app\models\Settings;
use app\models\Cms;
use app\models\ApiLog;
use app\models\UserSecurityQueValues;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\XmlDomConstruct;

include_once("common/components/xmlToArray.php");
include_once("common/components/XmlDomConstruct.php");
include_once("common/components/services/Twilio.php");

class UserController extends Controller {

    public $administrator_email;
    public $base_currency_code;
    public $configURL;
    public $homeURL;
    public $salt                = 'phoneDoctor';
    public $currencyLabel       = 'N';  
    public $twilio_from_phone;
    public $twilio_account_sid;
    public $twilio_auth_token;
    public $call_center_phone;
    
    
   public function beforeAction($action) { 
        

        $settingValues = Settings::find()->all();                        


        //$settingValues = Settings::model()->findAll();
        foreach($settingValues as $setting) {
            switch($setting->name) {  
                
                case 'administrator_email':
                    $this->administrator_email = $setting->value;
                    break;                
                   
                case 'base_currency_code':
                    $this->base_currency_code = $setting->value;
                    break;

                case 'configURL':
                    $this->configURL = $setting->value;
                    break;
                
                case 'twilio_auth_token':
                    $this->twilio_auth_token = $setting->value;
                    break;
                
                case 'twilio_account_sid':
                    $this->twilio_account_sid = $setting->value;
                    break;
                
                case 'twilio_from_phone':
                    $this->twilio_from_phone = $setting->value;
                    break; 
                
                case 'call_center_phone':
                    $this->call_center_phone = $setting->value;
                    break;                    
                
                default:
                    break;
            }
        }        
        //set the home URL
        $this->homeURL = 'http://' . $_SERVER['SERVER_NAME'].'phoneadoctor';
        return true;
    }    
    
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }
    
    // User APIS
    public function actionApi(){
        
        $requestMethod =  Yii::$app->REST->get_request_method();
        $connectionType = Yii::$app->REST->get_connection_type();                
       
        //use php://input to get the raw $_POST results
        $xmlInput = file_get_contents('php://input');

        //parse the incoming XML to array
        $xmlArray = xml2array($xmlInput);
        //echo'<pre>';print_r($xmlArray);echo'</pre>';

        if (!isset($xmlArray['request']) || !isset($xmlArray['request_attr'])) {
            $this->generateJsonResponce(array("response_code" => 112, "description" => 'Invalid request, please check your input.'), 'error', 400);
        } else if ($requestMethod <> 'POST') {
            $this->generateJsonResponce(array("response_code" => 101, "description" => 'Only POST method supported.'), 'error', 403);
        } /*else if ($connectionType <> 443) {
            $this->generateJsonResponce(array("response_code" => 111, "description" => 'Insecure connection.'), 'error', 406);
        }*/ else {  
            //determine the method
            switch ($xmlArray['request_attr']['method']) {
                case 'user.create':
                    $this->createUser($xmlArray['request']);
                    break;
                case 'user.createPassword':
                    $this->createPassword($xmlArray['request']);
                    break;  
                case 'user.getuserinfo':
                    $this->getUserinfo($xmlArray['request']);
                    break;                 
                case 'user.update':
                    $this->updateUser($xmlArray['request']);
                    break;
                case 'user.sendCode':
                    $this->sendVerificationCode($xmlArray['request']);
                    break;   
                case 'user.verifyPhone':
                    $this->verifyPhone($xmlArray['request']);
                    break; 
                case 'user.changePin':
                    $this->changePin($xmlArray['request']);
                    break;  
                case 'user.recoverPin':
                    $this->RecoverPin($xmlArray['request']);
                    break;  
                case 'user.getMedicalHistory':
                    $this->getMedicalHistory($xmlArray['request']);
                    break;  
                case 'user.sendSMS':
                    $this->sendSMS($xmlArray['request']);
                    break;
                case 'user.login':
                    $this->userLogin($xmlArray['request']);
                    break;                 
                case 'user.logout':
                    $this->userLogout($xmlArray['request']);
                    break;
                case 'user.addmedical':
                    $this->addMedicals($xmlArray['request']);
                    break;               
                
                default:
                   $this->generateJsonResponce(array("response_code" => 999, "description" => 'Unknown method.'), 'error', 400);
                    break;
            }
        }
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    { 
        $searchModel = new UserSearch();
        
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    public function actionSignup()
    { 
        $model = new User();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }
     /*
     * API Method : user.sendCode
     * Purpose    : Send verification code to user - user signup step 1
     * Returns    : Send code to User phone
     */  
    public function sendVerificationCode($xmlUserDetails) {

        if (!isset($xmlUserDetails['user']['phone']) || trim($xmlUserDetails['user']['phone']) == '') {
            $this->addLogEntry('user.sendCode', 'Failure', 9, 'Phone number missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone number missing.'), 'error', 400);
        }
        if ($this->validatePhone($xmlUserDetails['user']['phone'])) {
            //check if phone no. is already in use
            $phone_exists = $this->checkIfPhoneExists($xmlUserDetails['user']['phone']);
            if ($phone_exists) {

                $this->addLogEntry('user.sendCode', 'Failure', 9, 'Phone [' . $xmlUserDetails['user']['phone'] . '] already in use.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone already in use.'), 'error', 400);
            } else {
                $code = rand(111111, 999999);
                $twilio_message = "Phone a doctor\nPlease use verification Code: " . $code . " to sign up.";
                //---------------------- TWILIO ----------------------//         
                $twillio = Yii::$app->Twillio;
                $message = $twillio->getClient()->account->sms_messages->create($this->twilio_from_phone, // From a valid Twilio number
                        $xmlUserDetails['user']['phone'], // Text this number
                        $twilio_message
                );


                if (isset($message->description) && ($message->description != NULL)) {
                    $this->addLogEntry('user.sendCode', 'Failure', 3, 'User signup verification attempt with invalid phone number :- ' . $xmlUserDetails['user']['phone']);
                    $this->generateJsonResponce(array("response_code" => 113, "description" => $message->description), 'ok', 200);
                } else {
                    $model = new VerifyPhone();
                    $model->phone_no = $xmlUserDetails['user']['phone'];
                    $model->verification_code = $code;
                    $model->verified = 'NO';
                    $model->save();
                    $this->addLogEntry('user.sendCode', 'Success', 3, 'User signup verification code send to phone :- ' . $xmlUserDetails['user']['phone']);
                    $this->generateJsonResponce(array("response_code" => 100, "description" => 'Verification code sent.'), 'ok', 200);
                }
            }
        }
    }
     /*
     * API Method : user.verifyPhone
     * Purpose    : Verify user phone before sign up -  - user signup step 3
     * Returns    : Result success or failed
     */    
    public function verifyPhone($xmlUserDetails) {
        if (!isset($xmlUserDetails['user']['phone']) || trim($xmlUserDetails['user']['phone']) == '') {            
            $this->addLogEntry('user.verifyPhone', 'Failure', 9, 'Phone number missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone number missing.'), 'error', 400);            
        }else if (!isset($xmlUserDetails['user']['verification_code']) || trim($xmlUserDetails['user']['verification_code']) == '') {            
            $this->addLogEntry('user.verifyPhone', 'Failure', 9, 'Phone number missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Six digit code missing.'), 'error', 400);            
        }
        $userPhone = $xmlUserDetails['user']['phone'];
        $code = $xmlUserDetails['user']['verification_code'];
        //check if phone no. and code exist 
        $code_exists = VerifyPhone::find()->where('phone_no LIKE "'.$userPhone.'" AND verification_code LIKE "'.$code.'"')->one();        
        if($code_exists){
            $id         = $code_exists->id;
            $model      = VerifyPhone::findOne($id);  
            $model->verified      = 'YES';      
            $model->save();
            $this->addLogEntry('user.sendCode', 'Success', 9, 'User signup verification completed successfully for phone:'.$userPhone );
            $this->generateJsonResponce(array("response_code" => 100, "description" => 'Phone number verified.'), 'ok', 200);               
        }
        else{
            $this->addLogEntry('user.sendCode', 'Success', 3, 'User signup verification failed for phone:'.$userPhone );
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your code do not match.'), 'ok', 400);               
        }
    }
    
    //Function to check user phone verification status
    public function checkPhoneVerification($userPhone) {

        if ($userPhone != NULL) {
            //check if phone no. is verified
            $phone_verification = VerifyPhone::find()->where('phone_no LIKE "' . $userPhone . '"')->all();                    

            if ($phone_verification != NULL) {
                 $rec_count = count($phone_verification);  
                 if($rec_count>1 && $phone_verification[$rec_count-1]->verified =='NO'){
                    $this->addLogEntry('user.create', 'Failure', 9, 'Multiple entries found for same number: '.$userPhone.' and the latest not verified.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Step 2 failed, Multiple entries found for your mobile number and the latest not verified, redirect:user.verifyphone'), 'error', 400);
                  }
                return ($phone_verification[$rec_count-1]->verified == 'YES') ? true : false;
            } else {
                $this->addLogEntry('user.create', 'Failure', 9, 'Mobile number not verified, phone no not found in verification table.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Step 2 failed, Please verifiy your mobile number.'), 'error', 400);
            }
        } else {
            $this->addLogEntry('user.create', 'Failure', 9, 'user mobile number missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Please enter mobile number.'), 'error', 400);
        }
    }
     /*
     * API Method : user.create
     * Purpose    : Create a user  - user signup step 3
     * Returns    : Result of insert operation
     */
       public function createUser($xmlUserDetails) {

        if ($xmlUserDetails['user']['userinfo']) {
            $userFullName = $this->sanitizeXML($xmlUserDetails['user']['userinfo']['fname']) . $this->sanitizeXML($xmlUserDetails['user']['userinfo']['lname']);
            $userPhone = $xmlUserDetails['user']['patients']['mobile_phone'];
            $userFirstName = $this->sanitizeXML($xmlUserDetails['user']['userinfo']['fname']);
            $userLastName = $this->sanitizeXML($xmlUserDetails['user']['userinfo']['lname']);
            if (!isset($xmlUserDetails['user']['userinfo']['fname']) || trim($xmlUserDetails['user']['userinfo']['fname']) == '') {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Firstname missing.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Firstname missing.'), 'error', 400);
            } else if (!isset($xmlUserDetails['user']['userinfo']['lname']) || trim($xmlUserDetails['user']['userinfo']['lname']) == '') {

                $this->addLogEntry('user.create', 'Failure', 9, 'Lastname missing.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Lastname missing.'), 'error', 400);
            }

            if ($this->validatePhone($userPhone) && $this->checkPhoneVerification($userPhone)) {

                if (!isset($xmlUserDetails['user']['userinfo']['username']) || trim($xmlUserDetails['user']['userinfo']['username']) == '') {
                    $userName = preg_replace('/\s+/', '', $userFullName); //remove any whitespaces
                } else {
                    $userName = $xmlUserDetails['user']['userinfo']['username'];
                }

                //check if username is available
                $userFlag = true;
                if ($this->checkIfUserExists($userName)) {
                    while ($userFlag) {
                        $userName .= rand(100, 999);
                        $userFlag = $this->checkIfUserExists($userName);
                    }
                }

                $security_flag = $email_exists = $email = 0;

                if (isset($xmlUserDetails['user']['patients']['email']) && $xmlUserDetails['user']['patients']['email'] != NULL) {
                    //check if email is already in use.
                    $email = $this->sanitizeXML($xmlUserDetails['user']['patients']['email'], true);
                    $email_exists = Patient::find()->where('email LIKE "' . $email . '"')->one();
                } else {
                    if (!isset($xmlUserDetails['user']['patients']['security_que_value']) || trim($xmlUserDetails['user']['patients']['security_que_value']) == '') {
                        $this->addLogEntry('user.create', 'Failure', 9, 'Security question and email missing.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Please enter email or answer security question.'), 'error', 400);
                    }
                    $security_flag = 1;
                }
                //check if phone no. is already in use
                    $phone_exists = $this->checkIfPhoneExists($userPhone);

                if (preg_match('/[^A-Za-z0-9]/', $userName)) {

                    $this->addLogEntry('user.create', 'Failure', 9, 'Illegal characters in username.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Illegal characters in username. Only alphanuerics allowed.'), 'error', 400);
                } else if (!$this->validateEmail($email) && $security_flag == 0) {

                    $this->addLogEntry('user.create', 'Failure', 9, 'Invalid email.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Invalid email.'), 'error', 400);
                } else if ($email_exists && $security_flag == 0) {

                    $this->addLogEntry('user.create', 'Failure', 9, 'Email [' . $email . '] already in use.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Email [' . $email . '] already in use.'), 'error', 400);
                } else if ($phone_exists) {

                    $this->addLogEntry('user.create', 'Failure', 9, 'Phone [' . $userPhone . '] already in use.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone already in use.'), 'error', 400);
                } else {

                    //create a new user account                      

                    if (is_array($xmlUserDetails['user']['userinfo'])) {

                        $model = new User();
                        $model->username = $userName;
                        $model->createtime = (int) time();
                        $model->status = 1;
                        $model->auth_key = rand(1000, 9999);
                        $model->role = 'PATIENT';
                        $model->save();
                    }

                    if (is_array($xmlUserDetails['user']['userinfo']) && $model->id != NULL) {
                        //create a new patient
                        $userId = $model->id;
                        $model_patients = new Patient;
                        $xmlUserDetails['user']['patients']['user_id'] = $userId;
                        $xmlUserDetails['user']['patients']['create_date'] = (int) time();
                        $xmlUserDetails['user']['patients']['fname'] = $userFirstName;
                        $xmlUserDetails['user']['patients']['lname'] = $userLastName;
                        $xmlUserDetails['user']['patients']['pubpid'] = $xmlUserDetails['user']['patients']['mobile_phone']; 
                        $model_patients->setAttributes($xmlUserDetails['user']['patients']);
                        $model_patients->validate();
                        if ($model_patients->getErrors()) {
                            $this->addLogEntry('user.create', 'Failure', 9, 'Correct the validation errors.');
                            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Correct the validation errors.', 'errors' => $model->getErrors()), 'error', 400);
                            exit;
                        }
                        $model_patients->save(false);
                    }
                    if ($security_flag == 1) {
                        // Record security que value
                        $model_security_que = new UserSecurityQueValues();
                        $model_security_que->user_id = $userId;
                        if (isset($xmlUserDetails['user']['patients']['security_que_id']) && $xmlUserDetails['user']['patients']['security_que_id'] != NULL)
                            $model_security_que->que_id = $xmlUserDetails['user']['patients']['security_que_id'];
                        else
                            $model_security_que->custom_question = $xmlUserDetails['user']['patients']['custom_question'];

                        $model_security_que->user_value = $this->sanitizeXML($xmlUserDetails['user']['patients']['security_que_value'], true);
                        $model_security_que->save();
                    }

                    $this->addLogEntry('user.create', 'Success', 3, 'User successfully created. Username :- ' . $userName . ',User Id:-' . $userId, $model->id);
                    $this->generateJsonResponce(array("response_code" => 100, "description" => 'User successfully created', "user_id" => $userId, "authkey" => $model->auth_key), 'ok', 200);
                    exit;
                }
            } else {
                $this->addLogEntry('user.create', 'Failure', 9, 'Mobile number not verified for phone:' . $userPhone);
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Step 2 failed, Your mobile number is not yet verified, redirect:user.verifyphone'), 'error', 400);
            }
        }
    }
       
    /*
     * API Method : user.update
     * Purpose    : Update existing user
     * Returns    : Result of update operation
     */
    
    public function updateUser($xmlUserDetails) {
        
       if (!isset($xmlUserDetails['user']['userinfo']['password']) || trim($xmlUserDetails['user']['userinfo']['password']) == '') {
            
            $this->addLogEntry('user.update', 'Failure', 9, 'Pin missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Pin missing.'), 'error', 400);
            
        }else if((!isset($xmlUserDetails['user']['userinfo']['auth_key']) || trim($xmlUserDetails['user']['userinfo']['auth_key']) == '')) {
                
            $this->addLogEntry('user.update', 'Failure', 9, 'Auth key missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Auth key missing.'), 'error', 400);

        } else {
            //Authenticate user before update
            $user_exists = User::find()->where('id = ' . $xmlUserDetails['user']['userinfo']['id'] . ' AND password LIKE "' . md5($xmlUserDetails['user']['userinfo']['password']) . '"')->one();
            //Authenticate access key before update
            Yii::$app->AuthoriseUser->userId = $xmlUserDetails['user']['userinfo']['id'];
            Yii::$app->AuthoriseUser->auth_key = $xmlUserDetails['user']['userinfo']['auth_key'];
            $accessAuthorised =  Yii::$app->AuthoriseUser->checkAuthKey();
            if($accessAuthorised){            
                if ($user_exists) {
                    
                    $model = $this->findModel($this->sanitizeXML($xmlUserDetails['user']['userinfo']['id']));
                    $userFullName = $this->sanitizeXML($xmlUserDetails['user']['userinfo']['fname']) . $this->sanitizeXML($xmlUserDetails['user']['userinfo']['lname']);
                    $userFirstName = $this->sanitizeXML($xmlUserDetails['user']['userinfo']['fname']);
                    $userLastName = $this->sanitizeXML($xmlUserDetails['user']['userinfo']['lname']);
                    $userPhone = $this->sanitizeXML($xmlUserDetails['user']['patients']['mobile_phone']);
                                      

                    if (!isset($xmlUserDetails['user']['userinfo']['username']) || trim($xmlUserDetails['user']['userinfo']['username']) == '') {
                        $userName = preg_replace('/\s+/', '', $userFullName); //remove any whitespaces
                    } else {
                        $userName = $xmlUserDetails['user']['userinfo']['username'];
                    }

                    //check if username is available
                    $userFlag = true;
                    $user = User::find()
                            ->where('username = "' . $userName . '"')
                            ->all();
                    $userNameUpdateFlag = count($user) >= 2 ? true : false;

                    if ($userNameUpdateFlag) {
                        while ($userFlag) {
                            $userName .= rand(100, 999);
                            $userFlag = $this->checkIfUserExists($userName);
                        }
                    }
                    $email_check_flag = $email_exists = $email = 0 ;
                    if($xmlUserDetails['user']['patients']['email']!=NULL){
                    //check if email is already in use.
                    $email = $this->sanitizeXML($xmlUserDetails['user']['patients']['email'], true);

                    $email_exists = Patient::find()->where('email LIKE "' . $email . '"')->all();
                    $email_check_flag = count($email_exists) >= 2 ? true : false;
                    }

                    //check if phone no. is already in use
                    $phone_exists = 0;
                    $phone_exists = Patient::find()->where('mobile_phone LIKE "' . $userPhone . '"')->all();                    
                    $phone_check_flag = count($phone_exists) == 1 ? true : false;                    
                    

                    if (preg_match('/[^A-Za-z0-9]/', $userName)) {

                        $this->addLogEntry('user.update', 'Failure', 9, 'Illegal characters in username.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Illegal characters in username. Only alphanuerics allowed.'), 'error', 400);
                    } else if ($xmlUserDetails['user']['patients']['email']!=NULL && !$this->validateEmail($this->sanitizeXML($xmlUserDetails['user']['patients']['email'], true))) {

                        $this->addLogEntry('user.update', 'Failure', 9, 'Invalid email.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Invalid email.'), 'error', 400);
                    } else if ($email_check_flag) {

                        $this->addLogEntry('user.update', 'Failure', 9, 'Email [' . $this->sanitizeXML($xmlUserDetails['user']['userinfo']['email']) . '] already in use.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Email already in use.'), 'error', 400);
                    } else if (!$phone_check_flag) {

                        $this->addLogEntry('user.update', 'Failure', 9, 'Mobile phone number cannot be edited.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Mobile phone number cannot be edited'), 'error', 400);
                    } else {
                        
               if(is_array($xmlUserDetails['user']['userinfo'])){                        
                        $model->username = $userName;
                        $model->save();
                    }                        
                        
                        $patient_exists = Patient::find()->where('user_id = ' . $xmlUserDetails['user']['userinfo']['id'])->one();
                        if($patient_exists){
                        $model_patients = Patient::findOne($patient_exists->pid);
                        $xmlUserDetails['user']['patients']['fname']        = $userFirstName;
                        $xmlUserDetails['user']['patients']['lname']        = $userLastName;
                        $model_patients->setAttributes($xmlUserDetails['user']['patients']);                        
                        if($model_patients->getErrors()){
                            $this->addLogEntry('user.update', 'Failure', 9, 'Correct the validation errors.');
                            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Correct the validation errors.','errors'=>$model->getErrors()), 'error', 400);
                            exit;
                        }                        
                        $model_patients->save(false);                
                        }
                        $this->addLogEntry('user.update', 'Success', 3, 'User info successfully updated. Username :- ' . $model->username, $model->id);
                        $this->generateJsonResponce(array("response_code" => 100, "description" => 'User info successfully updated.'), 'ok', 200);
                        exit;
                    }
                } else {
                    $this->addLogEntry('user.update', 'Failure', 9, 'User profile update authentication failed for user :' . $xmlUserDetails['user']['userinfo']['id']);
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your pin is incorrect.'), 'error', 400);
                }
            } else {
                $this->addLogEntry('user.update', 'Failure', 9, 'User profile update auth key authentication failed for user :' . $xmlUserDetails['user']['userinfo']['id']);
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your auth key is invalid.'), 'error', 400);
            }
        }
    }
    
    
     /*
     * API Method : consultation.getMedicalHistory
     * Purpose    : get user medical details
     * Returns    : User related ino*/
    
    
    public function getMedicalHistory($xmlUserDetails) {
        
    if((!isset($xmlUserDetails['user']['auth_key']) || trim($xmlUserDetails['user']['auth_key']) == '')) {
                
            $this->addLogEntry('user.getMedicalHistory', 'Failure', 9, 'Auth key missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Auth key missing.'), 'error', 400);

        } elseif (!isset($xmlUserDetails['user']['id']) || trim($xmlUserDetails['user']['id']) == '') {            
            $this->addLogEntry('user.getMedicalHistory', 'Failure', 9, 'User ID missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User ID missing.'), 'error', 400);            
        }else {  
            
            //Authenticate access key before update
            Yii::$app->AuthoriseUser->userId = $xmlUserDetails['user']['id'];
            Yii::$app->AuthoriseUser->auth_key = $xmlUserDetails['user']['auth_key'];
            $accessAuthorised =  Yii::$app->AuthoriseUser->checkAuthKey();
            if($accessAuthorised){ 
                $user_exists = Patient::find()->where('user_id LIKE "' . $xmlUserDetails['user']['id'] . '"')->one();                    
                $patient_id = ($user_exists!=NULL)?$user_exists->pid:0;
                
                $query = new Query;
                $query->select('pm.STR,pm.begin_date,pm.end_date,pm.prescription_id,pm.route,pm.dispense,
                        pm.dose,pm.refill,pm.take_pills,pm.form,pa.allergy_type,pa.allergy,pa.begin_date,pa.end_date,pa.reaction,pa.severity,pa.location') 
                    ->from('patient_medications pm')
                    ->join('LEFT JOIN', 'patient_allergies pa',
                                'pa.pid =pm.pid')   
                    ->where('pm.pid = ' . $patient_id);
                $command = $query->createCommand();                
                $results = $command->queryAll();
                $history_data = ($results!=NULL)?$results:"No records found"; 
                
                $this->addLogEntry('user.getMedicalHistory', 'Success', 3, 'User MedicalHistory successfully returned. Username :- ' . $user_exists['fname'],$xmlUserDetails['user']['id']);
                $this->generateJsonResponce(array("response_code" => 100, "description" => $history_data), 'error', 400);
                
            } else {
                $this->addLogEntry('user.getMedicalHistory', 'Failure', 9, 'Fetch user info auth key authentication failed for user :' . $xmlUserDetails['user']['id']);
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your auth key is invalid.'), 'error', 400);
            }
            
        } 
    }
    
     /*
     * API Method : consultation.getConsultationHistory
     * Purpose    : get user consultation details
     * Returns    : User related info
     
    
    public function getConsultationHistory($xmlUserDetails) {
        
    if((!isset($xmlUserDetails['user']['auth_key']) || trim($xmlUserDetails['user']['auth_key']) == '')) {
                
            $this->addLogEntry('consultation.getConsultationHistory', 'Failure', 9, 'Auth key missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Auth key missing.'), 'error', 400);

        } elseif (!isset($xmlUserDetails['user']['id']) || trim($xmlUserDetails['user']['id']) == '') {            
            $this->addLogEntry('consultation.getConsultationHistory', 'Failure', 9, 'User ID missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User ID missing.'), 'error', 400);            
        }else {  
            
            //Authenticate access key before update
            Yii::$app->AuthoriseUser->userId = $xmlUserDetails['user']['id'];
            Yii::$app->AuthoriseUser->auth_key = $xmlUserDetails['user']['auth_key'];
            $accessAuthorised =  Yii::$app->AuthoriseUser->checkAuthKey();
            if($accessAuthorised){ 
                
                $query = new Query;
                $query->select('code,details,type,date') 
                    ->from('consultations')                     
                    ->where('user_id = '. $xmlUserDetails['user']['id']);
                $command = $query->createCommand(); 
                $results = $command->queryAll();                
                $history_data = ($results!=NULL)?$results:"No consultation records found";                
                
                $this->addLogEntry('consultation.getConsultationHistory', 'Success', 3, 'User Consultation History successfully returned. User ID :- ' . $xmlUserDetails['user']['id'],$xmlUserDetails['user']['id']);
                $this->generateJsonResponce(array("response_code" => 100, "description" => $history_data), 'error', 400);
                
            } else {
                $this->addLogEntry('consultation.getConsultationHistory', 'Failure', 9, 'Fetch user info auth key authentication failed for user :' . $xmlUserDetails['user']['id']);
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your auth key is invalid.'), 'error', 400);
            }
            
        } 
    
        
    }  */  
    
    /*
     * API Method : user.getuserinfo
     * Purpose    : get user details
     * Returns    : User related ino
     */
    
    public function getUserinfo($xmlUserDetails) {
        
    if((!isset($xmlUserDetails['user']['auth_key']) || trim($xmlUserDetails['user']['auth_key']) == '')) {
                
            $this->addLogEntry('user.getuserinfo', 'Failure', 9, 'Auth key missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Auth key missing.'), 'error', 400);

        } elseif (!isset($xmlUserDetails['user']['id']) || trim($xmlUserDetails['user']['id']) == '') {            
            $this->addLogEntry('user.getuserinfo', 'Failure', 9, 'User ID missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User ID missing.'), 'error', 400);            
        }else {  
            
            //Authenticate access key before update
            Yii::$app->AuthoriseUser->userId = $xmlUserDetails['user']['id'];
            Yii::$app->AuthoriseUser->auth_key = $xmlUserDetails['user']['auth_key'];
            $accessAuthorised =  Yii::$app->AuthoriseUser->checkAuthKey();
            if($accessAuthorised){ 
                $query = new Query;
                $query->select('title, fname, lname,sex,DOB,marital_status,SS,pubpid,address,city,state,country,zipcode,mobile_phone,email,mothers_name,guardians_name,pharmacy')
                        ->from('patient')
                        ->where('user_id = ' . $xmlUserDetails['user']['id']);

                $command = $query->createCommand();
                $patient_exists = $command->queryOne();
                //$patient_exists = Patient::find()->where('user_id = ' . $xmlUserDetails['user']['id'])->one();
                $this->addLogEntry('user.getuserinfo', 'Success', 3, 'User info successfully returned. Username :- ' . $patient_exists['fname'],$xmlUserDetails['user']['id']);
                $this->generateJsonResponce(array("response_code" => 100, "description" => $patient_exists), 'error', 400);
                
            } else {
                $this->addLogEntry('user.getuserinfo', 'Failure', 9, 'Fetch user info auth key authentication failed for user :' . $xmlUserDetails['user']['id']);
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your auth key is invalid.'), 'error', 400);
            }
            
        } 
    
        
    }
    
    
    /*
     * API Method : user.createPassword
     * Purpose    : user user signup step 4
     * Returns    : Result success or failed
     */      
    public function createPassword($xmlUserDetails){
       $email='';
       if (!isset($xmlUserDetails['user']['id']) || trim($xmlUserDetails['user']['id']) == '') {            
            $this->addLogEntry('user.create password', 'Failure', 9, 'User ID missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User ID missing.'), 'error', 400);            
        }else if (!isset($xmlUserDetails['user']['pin']) || trim($xmlUserDetails['user']['pin']) == '') {            
            $this->addLogEntry('user.create password', 'Failure', 9, 'Pin missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Pin missing.'), 'error', 400);            
        }else if (!isset($xmlUserDetails['user']['confirm_pin']) || trim($xmlUserDetails['user']['confirm_pin']) == '') {            
            $this->addLogEntry('user.changePin', 'Failure', 9, 'Confirm pin missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Confirm pin missing.'), 'error', 400);            
        }
        
        if ($xmlUserDetails['user']['pin'] != $xmlUserDetails['user']['confirm_pin']) {
            $this->addLogEntry('user.create password', 'Failure', 9, 'Your pin and confirmed pin does not match.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Pin and confirmed pin does not match.'), 'error', 400);
        }   
            
        $user_exists = User::find()->where('id = ' . $xmlUserDetails['user']['id'])->one();
        
        if($user_exists!=NULL){
            
            //Check if pin is already set
            if ($user_exists->password != NULL) {
                $this->addLogEntry('user.create password', 'Failure', 9, 'Pin already set.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'You have already set your pin, please login to change the same.'), 'error', 400);
            }            
            
            $model = $this->findModel($this->sanitizeXML($xmlUserDetails['user']['id']));
            $model->password  = md5($this->sanitizeXML($xmlUserDetails['user']['pin']));                         
            $model->save(); 
            //Send mail to registered user
            $patient_exists = Patient::find()->where('user_id = ' . $xmlUserDetails['user']['id'])->one();            
            if($patient_exists!=NULL){
                $email = $patient_exists->email;   
            }else{            
                $this->addLogEntry('user.create password', 'Failure', 9, 'user creation failed.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'user creation: step 3 failed.'), 'error', 400);            
            } 
           
            if($email != NULL){
                $mail_content   = Cms::find()->where('name LIKE "patient_signup"')->one();
                $mail_title     = $mail_content->title;
                $mail_tmp_body  = $mail_content->content;
                $mail_body      = str_replace(array('{DATE}', '{PHONE}', '{PASSWORD}', '{USER}'),
                                              array(date('m-d-Y'), $patient_exists->mobile_phone, $this->sanitizeXML($xmlUserDetails['user']['pin']), $patient_exists->fname." ".$patient_exists->lname),
                                              $mail_tmp_body);											 
                
                $this->sendSystemEmail($mail_body, $mail_title, $email);                    
                }
            $this->addLogEntry('user.create password', 'Success', 3, 'User sign up completed for ' . $model->username, $model->id);
            $this->generateJsonResponce(array("response_code" => 100, "description" => 'User sign up completed for ' . $model->username), 'ok', 200);        
        }else{
            $this->addLogEntry('user.create password', 'Failure', 9, 'User does not exist. redirect:user.verifyphone');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User does not exist.redirect:user.verifyphone'), 'error', 400); 
        }
    }


    /*
     * API Method : user.login
     * Purpose    : user login
     * Returns    : Result success or failed
     */    
    public function userLogin($xmlUserDetails) {
       if (!isset($xmlUserDetails['user']['phone']) || trim($xmlUserDetails['user']['phone']) == '') {            
            $this->addLogEntry('user.Login', 'Failure', 9, 'phone number missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'phone number missing.'), 'error', 400);            
        }else if (!isset($xmlUserDetails['user']['pin']) || trim($xmlUserDetails['user']['pin']) == '') {            
            $this->addLogEntry('user.Login', 'Failure', 9, 'Pin missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Pin missing.'), 'error', 400);            
        }
        
        $user_phone         = $xmlUserDetails['user']['phone'];
        $user_pass          = $this->sanitizeXML($xmlUserDetails['user']['pin'], true);
        $user_exists        = $patient_exists =0;
        //check if user exist 
        if($this->validatePhone($user_phone))
        $patient_exists = Patient::find()->where('mobile_phone = "'.$user_phone.'"')->one(); 
        
        if(($patient_exists) && ($patient_exists->user_id !=NULL))
        $user_exists = User::find()->where('id = '.$patient_exists->user_id.' AND password LIKE "'.md5($user_pass).'"')->one();        
        if($user_exists){
            $id         = $user_exists->id;            
            $model      = User::findOne($id);         
            $model->auth_key = rand(1000,9999);
            $model->save();     
            $userDetails = array('id'=>$id,'username'=>$user_exists->username,'auth_key'=>$model->auth_key);
            $this->addLogEntry('user.Login', 'Success', 9, 'User logged in sucesssfully, User Id : '.$id );
            $this->generateJsonResponce(array("response_code" => 100, "description" => $userDetails), 'ok', 200);               
        }
        else{
            $this->addLogEntry('user.Login', 'Failure', 9, 'Failed login attempt with phone:'.$user_phone);
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Login failed, please check your credentials.'), 'error', 400);               
        }     
    }
    
     /*
     * API Method : user.changePin
     * Purpose    : Change user pin
     * Returns    : Result success or failed
     */    
    public function changePin($xmlUserDetails) {        
   
        Yii::$app->AuthoriseUser->userId   = $xmlUserDetails['user']['user_id'];
        Yii::$app->AuthoriseUser->auth_key = $xmlUserDetails['user']['auth_key'];
        
        if (!isset($xmlUserDetails['user']['old_pin']) || trim($xmlUserDetails['user']['old_pin']) == '') {            
            $this->addLogEntry('user.changePin', 'Failure', 9, 'Old pin missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Old pin missing.'), 'error', 400);            
        }else if (!isset($xmlUserDetails['user']['new_pin']) || trim($xmlUserDetails['user']['new_pin']) == '') {            
            $this->addLogEntry('user.changePin', 'Failure', 9, 'New pin missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'New pin missing.'), 'error', 400);            
        }else if (!isset($xmlUserDetails['user']['confirm_pin']) || trim($xmlUserDetails['user']['confirm_pin']) == '') {            
            $this->addLogEntry('user.changePin', 'Failure', 9, 'Confirm pin missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'New pin missing.'), 'error', 400);            
        }else if (!isset($xmlUserDetails['user']['user_id']) || trim($xmlUserDetails['user']['user_id']) == '') {            
            $this->addLogEntry('user.changePin', 'Failure', 9, 'User ID missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User ID missing.'), 'error', 400);            
        }else if((!isset($xmlUserDetails['user']['auth_key']) || trim($xmlUserDetails['user']['auth_key']) == '')) {                
            $this->addLogEntry('user.update', 'Failure', 9, 'Auth key missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Auth key missing.'), 'error', 400);
        }
        
        
        //Authenticate access key before update
        $accessAuthorised =  Yii::$app->AuthoriseUser->checkAuthKey();
        
        if($accessAuthorised){        
            if ($xmlUserDetails['user']['new_pin'] != $xmlUserDetails['user']['confirm_pin']) {
                $this->addLogEntry('user.changePin', 'Failure', 9, 'New pin and confirmed pin does not match.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'New pin and confirmed pin does not match.'), 'error', 400);
            }

            //check if user exist 
            $user_exists = User::find()->where('id = ' . $xmlUserDetails['user']['user_id'] . ' AND password LIKE "' . md5($xmlUserDetails['user']['old_pin']) . '"')->one();
            if ($user_exists) {
                $id = $user_exists->id;
                $model = User::findOne($id);
                $model->password = md5($xmlUserDetails['user']['new_pin']);
                $model->save();
                $this->addLogEntry('user.changePin', 'Success', 9, 'User pin changed sucesssfully for user:' . $xmlUserDetails['user']['user_id']);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'User pin changed sucesssfully.'), 'ok', 200);
            } else {
                $this->addLogEntry('user.changePin', 'Failure', 9, 'User pin change failed for user:' . $xmlUserDetails['user']['user_id']);
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your old pin is incorrect.'), 'error', 400);
            }
        } else {
            $this->addLogEntry('user.changePin', 'Failure', 9, 'Change user pin access key authentication failed ');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your access key is invalid.'), 'error', 400);
        }
    }
    
    
    /*
     * API Method : consultation.appointmentNotification
     * Purpose    : send notification SMSto users
     * Returns    : Result success or failed
          
    public function appointmentNotification($userId,$appDate,$doctor,$sessionId) {
        $officePhone = 0;
        if (!isset($appDate) || trim($appDate) == '') {
            $this->addLogEntry('consultation.appointmentNotification', 'Failure', 9, 'Date is missing.');
            return 'Date is missing.'; //$this->generateJsonResponce(array("response_code" => 113, "description" => 'Date is missing.'), 'error', 400);
        } else if (!isset($doctor) || trim($doctor) == '') {
            $this->addLogEntry('consultation.appointmentNotification', 'Failure', 9, 'Doctor name missing');
            return 'Name of the doctor is missing';//$this->generateJsonResponce(array("response_code" => 113, "description" => 'Name of the doctor is missing'), 'error', 400);
        } else if (!isset($sessionId) || trim($sessionId) == '') {
            $this->addLogEntry('consultation.appointmentNotification', 'Failure', 9, 'sessionId missing');
            return 'sessionId is missing';//$this->generateJsonResponce(array("response_code" => 113, "description" => 'sessionId is missing'), 'error', 400);
        } else if (!isset($userId) || trim($userId) == '') {
            $this->addLogEntry('consultation.appointmentNotification', 'Failure', 9, 'sessionId missing');
            return 'userId is missing';//$this->generateJsonResponce(array("response_code" => 113, "description" => 'userId is missing'), 'error', 400);
        }
        
        // Get user phone
        $UserCondition = 'user_id = ' . $userId;
        $user_check = Patient::find()->where($UserCondition)->one();
        if ($user_check != NULL) {
            $userPhone = $user_check->mobile_phone;
            $officePhone = (isset($this->call_center_phone) && $this->call_center_phone != NULL)?$this->call_center_phone:07003628677;
            $twilio_message = "Hi ".$user_check->fname . " " . $user_check->lname."\n Your Phone A Doctor appointment with Dr. " . $doctor . " is scheduled for " . $appDate . "\n.Please call ".$officePhone." and present your session Id : " . $sessionId ." at the appointment time";
            //---------------------- TWILIO ----------------------//                         
            $twillio = Yii::$app->Twillio;
            $message = $twillio->getClient()->account->sms_messages->create($this->twilio_from_phone, // From a valid Twilio number
                    $userPhone, // Text this number
                    $twilio_message
            );
            $this->addLogEntry('consultation.appointmentNotification', 'Success', 3, 'Appointment nofificatiopn sms sent for userId: ' . $user_check->fname . " " . $user_check->lname, $userId);
            return 'Pin reset and sms sent for user ';//$this->generateJsonResponce(array("response_code" => 100, "description" => 'Pin reset and sms sent for user ' . $user_check->fname . " " . $user_check->lname), 'ok', 200);
        } else {
            $this->addLogEntry('consultation.appointmentNotification', 'Failure', 9, 'No user exists with the details provided.');
            return 'No user exists with the userId provided.';//$this->generateJsonResponce(array("response_code" => 113, "description" => 'No user exists with the userId provided.'), 'error', 400);
        }
    } */


    /*
     * API Method : user.recoverPin
     * Purpose    : recover user pin
     * Returns    : Result success or failed
     */    
    public function recoverPin($xmlUserDetails) {
        $newPin=0;
        if (isset($xmlUserDetails['user']['recovery_option'])) {
            $recovery_option = $xmlUserDetails['user']['recovery_option'];
        } else {
            $this->addLogEntry('user.recoverPin', 'Failure', 9, 'Recovery option missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Recovery option missing.'), 'error', 400);
        }
       
        switch ($recovery_option) {
            case 'email':
                if (!isset($xmlUserDetails['user']['email']) || trim($xmlUserDetails['user']['email']) == '') {
                    $this->addLogEntry('user.recoverPin', 'Failure', 9, 'Email missing.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Email missing.'), 'error', 400);
                }
                break;
            case 'phone':
                if (!isset($xmlUserDetails['user']['phone']) || trim($xmlUserDetails['user']['phone']) == '') {
                    $this->addLogEntry('user.recoverPin', 'Failure', 9, 'Phone no. missing.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone number missing.'), 'error', 400);
                } else if (!isset($xmlUserDetails['user']['security_que_value']) || trim($xmlUserDetails['user']['security_que_value']) == '') {
                    $this->addLogEntry('user.recoverPin', 'Failure', 9, 'Answer for security question missing.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Answer for security question missing.'), 'error', 400);
                } else if (!isset($xmlUserDetails['user']['security_que_id']) || trim($xmlUserDetails['user']['security_que_id']) == '') {
                    if (!isset($xmlUserDetails['user']['custom_question']) || trim($xmlUserDetails['user']['custom_question']) == '') {
                        $this->addLogEntry('user.recoverPin', 'Failure', 9, 'Both Security question Id and custom qusetion missing,please enter any one value. .');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Both Security question Id and custom qusetion missing.'), 'error', 400);
                    }
                }
                break;

            default:
                break;
        }

        // Get User Id to cross check question
        $UserCondition = $recovery_option == 'email' ? 'email LIKE "' . $xmlUserDetails['user']['email'] . '"' : 'mobile_phone LIKE "' . $xmlUserDetails['user']['phone'] . '"';        
        //echo $UserCondition;exit;
        $user_check = Patient::find()->where($UserCondition)->one();

        if ($user_check != NULL) {
            $userId = $user_check->user_id;

            //Reset password        
            $model = User::findOne($userId);
            
            $newPin = rand(10000, 99999);
            $model->password = md5($newPin);
            $model->save();

            switch ($recovery_option) {
                case 'email':

                    $mail_content = Cms::find()->where('name LIKE "recover_pin"')->one();
                    $mail_title = $mail_content->title;
                    $mail_tmp_body = $mail_content->content;
                    $mail_body = str_replace(array('{PIN}', '{USER}'), array($newPin, $user_check->fname . " " . $user_check->lname), $mail_tmp_body);
                    $this->sendSystemEmail($mail_body, $mail_title, $xmlUserDetails['user']['email']);

                    $this->addLogEntry('user.recoverPin', 'Success', 3, 'Pin reset and email sent for userId: ' . $userId, $model->id);
                    $this->generateJsonResponce(array("response_code" => 100, "description" => 'Your pin is reset and email sent for user ' . $user_check->fname . " " . $user_check->lname), 'ok', 200);

                    break;
                case 'phone':
                    // Check security question
                    if (isset($xmlUserDetails['user']['security_que_id']) && $xmlUserDetails['user']['security_que_id'] != NULL) {
                        //check if user exist and question value is valid
                        $user_exists = UserSecurityQueValues::find()->where('que_id = ' . $xmlUserDetails['user']['security_que_id'] . ' AND user_id = ' . $userId . ' AND user_value LIKE "' . $xmlUserDetails['user']['security_que_value'] . '"')->one();
                    } else {
                        //check if user exist and question value is valid with custom que                        
                        $user_exists = UserSecurityQueValues::find()->where('user_id = ' . $userId . ' AND user_value LIKE "' . $xmlUserDetails['user']['security_que_value'] . '"')->one();
                    }
                    if ($user_exists != NULL) {
                        $twilio_message = "Phone a doctor\nYour pin with phoneadoctor is reset, please use your new Pin: " . $newPin . " to login.";                         
                        //---------------------- TWILIO ----------------------//
                        $twillio = Yii::$app->Twillio;
                        $message = $twillio->getClient()->account->sms_messages->create($this->twilio_from_phone, // From a valid Twilio number
                                $xmlUserDetails['user']['phone'], // Text this number
                                $twilio_message
                        );
                        $this->addLogEntry('user.recoverPin', 'Success', 3, 'Pin reset and sms sent for userId: ' . $userId, $model->id);
                        $this->generateJsonResponce(array("response_code" => 100, "description" => 'Pin reset and sms sent for user ' . $user_check->fname . " " . $user_check->lname), 'ok', 200);
                    } else {
                        $this->addLogEntry('user.recoverPin', 'Failure', 9, 'Your answer for security question is invalid.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Invalid answer for security question.'), 'error', 400);
                    }

                    break;

                default:
                    break;
            }
        } else {
            $this->addLogEntry('user.recoverPin', 'Failure', 9, 'No user exists with the details provided.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'No user exists with the details provided.'), 'error', 400);
        }
    }

    /*
     * API Method : user.logout
     * Purpose    : user logout
     * Returns    : Result success or failed
     */    
    public function userLogout($xmlUserDetails) {
        
       if (!isset($xmlUserDetails['user']['id']) || trim($xmlUserDetails['user']['id']) == '') {
                    $this->addLogEntry('user.logout', 'Failure', 9, 'User Id missing.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'User Id missing.'), 'error', 400);
          } elseif (!isset($xmlUserDetails['user']['auth_key']) || trim($xmlUserDetails['user']['auth_key']) == '') {
                    $this->addLogEntry('user.logout', 'Failure', 9, 'Auth key missing.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Auth key missing.'), 'error', 400);
          }       
            //Authenticate access key before update
            Yii::$app->AuthoriseUser->userId = $xmlUserDetails['user']['id'];
            Yii::$app->AuthoriseUser->auth_key = $xmlUserDetails['user']['auth_key'];
            $accessAuthorised =  Yii::$app->AuthoriseUser->checkAuthKey();
            if($accessAuthorised){           
            
            $model = User::findOne($this->sanitizeXML($xmlUserDetails['user']['id']));
            if($model!=NULL){
                $model->auth_key  = NULL;
                $model->save();   
            }else{
                $this->addLogEntry('user.logout', 'Success', 9, 'Invalid User Id : '.$xmlUserDetails['user']['id'] );
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'Invalid User Id'), 'ok', 200);                 
            }
            
            $this->addLogEntry('user.logout', 'Success', 9, 'User logged out sucesssfully, User Id : '.$xmlUserDetails['user']['id'] );
            $this->generateJsonResponce(array("response_code" => 100, "description" => 'Logged out'), 'ok', 200); 
            }else{
                $this->addLogEntry('user.logout', 'Failure', 9, 'Un-authorised attempt to logout by UserId '.$xmlUserDetails['user']['id'] );
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'Un-authorised attempt to logout'), 'ok', 200);                 
            }
            
        
    } 
    
    /*
     * API Method : user.addmedical
     * Purpose    : user's current conditions & symptoms, allergies,medications/treatments if currently receiving
     * Returns    : Result success or failed
     */      
    public function addMedicals($xmlUserDetails) {

        if ((!isset($xmlUserDetails['user']['userinfo']['id']) || trim($xmlUserDetails['user']['userinfo']['id']) == '')) {

            $this->addLogEntry('user.addmedical', 'Failure', 9, 'User Id missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User Id missing.'), 'error', 400);
        }elseif ((!isset($xmlUserDetails['user']['userinfo']['auth_key']) || trim($xmlUserDetails['user']['userinfo']['auth_key']) == '')) {

            $this->addLogEntry('user.addmedical', 'Failure', 9, 'Auth key missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Auth key missing.'), 'error', 400);
        } else {            
            $user_exists = User::find()->where('id = ' . $xmlUserDetails['user']['userinfo']['id'])->one();
            $patient_exists = Patient::find()->where('user_id = ' . $xmlUserDetails['user']['userinfo']['id'])->one();
            //Authenticate access key before update
            Yii::$app->AuthoriseUser->userId = $xmlUserDetails['user']['userinfo']['id'];
            Yii::$app->AuthoriseUser->auth_key = $xmlUserDetails['user']['userinfo']['auth_key'];
            $accessAuthorised = Yii::$app->AuthoriseUser->checkAuthKey();
            if ($accessAuthorised) {
                if ($user_exists) {
                    if (is_array($xmlUserDetails['user']['alergies'])) {
                        $model = new PatientAllergies;
                        $xmlUserDetails['user']['alergies']['pid']        = $patient_exists->pid;
                        $model->setAttributes($xmlUserDetails['user']['alergies']);
                        //$model->load($xmlUserDetails['user']['alergies']);
                        $model->validate();
                        if ($model->getErrors()) {
                            $this->addLogEntry('user.medicals', 'Failure', 9, 'Correct the validation errors of allergies.');
                            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Correct the validation errors of allergies.', 'errors' => $model->getErrors()), 'error', 400);
                            exit;
                        }

                        $model->save();
                    }

                    if (is_array($xmlUserDetails['user']['medications'])) {
                        $model2 = new PatientMedications;
                        $xmlUserDetails['user']['medications']['pid']        = $patient_exists->pid;
                        $xmlUserDetails['user']['medications']['uid']        = $xmlUserDetails['user']['userinfo']['id'];
                        $model2->setAttributes($xmlUserDetails['user']['medications']);                        
                        //$model2->load($xmlUserDetails['user']['medications']);
                        $model2->validate();
                        if ($model2->getErrors()) {
                            $this->addLogEntry('user.medicals', 'Failure', 9, 'Correct the validation errors of medications.');
                            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Correct the validation errors of medications.', 'errors' => $model2->getErrors()), 'error', 400);
                            exit;
                        }
                        $model2->save();
                    }
                    
                    if (is_array($xmlUserDetails['user']['active_problems'])) {
                        $model3 = new PatientActiveProblems;
                        $xmlUserDetails['user']['active_problems']['pid']        = $patient_exists->pid;
                        $xmlUserDetails['user']['active_problems']['eid']        = 0;
                        $model3->setAttributes($xmlUserDetails['user']['active_problems']);                        
                        
                        $model3->validate();
                        if ($model3->getErrors()) {
                            $this->addLogEntry('user.medicals', 'Failure', 9, 'Correct the validation errors of active problems.');
                            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Correct the validation errors of active problems.', 'errors' => $model2->getErrors()), 'error', 400);
                            exit;
                        }
                        $model3->save();
                    }                    

                    $this->addLogEntry('user.medicals', 'Success', 3, 'User medicals successfully added. Username :- ' . $user_exists->username, $user_exists->id);
                    $this->generateJsonResponce(array("response_code" => 100, "description" => 'User medicals successfully added.'), 'ok', 200);
                    exit;
                } else {
                    $this->addLogEntry('user.medicals', 'Failure', 9, 'Add user medicals failed for user :' . $xmlUserDetails['user']['userinfo']['id']);
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'User Id is invalid.'), 'error', 400);
                }
            } else {
                $this->addLogEntry('user.medicals', 'Failure', 9, 'User profile update auth key authentication failed for user :' . $xmlUserDetails['user']['userinfo']['id']);
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your auth key is invalid.'), 'error', 400);
            }
        }
    }  
    
    /*
     * API Method : user.sendSMS
     * Purpose    : Send SMS to user
     * Returns    : Send SMS to User phone
     */

    public function sendSMS($xmlUserDetails) {
        
        if (!isset($xmlUserDetails['user']['phone']) || trim($xmlUserDetails['user']['phone']) == '') {
            $this->addLogEntry('user.sendSMS', 'Failure', 9, 'Phone number missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone number missing.'), 'error', 400);
        } elseif (!isset($xmlUserDetails['user']['message_text']) || trim($xmlUserDetails['user']['message_text']) == '') {
            $this->addLogEntry('user.sendSMS', 'Failure', 9, 'Message text missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Message text missing.'), 'error', 400);
        }
        if ($this->validatePhone($xmlUserDetails['user']['phone'])) {

            $twilio_message = $xmlUserDetails['user']['message_text'];
            //---------------------- TWILIO ----------------------//         
            $twillio = Yii::$app->Twillio;
            $message = $twillio->getClient()->account->sms_messages->create($this->twilio_from_phone, // From a valid Twilio number
                    $xmlUserDetails['user']['phone'], // Text this number
                    $twilio_message
            );

            if (isset($message->description) && ($message->description != NULL)) {
                $this->addLogEntry('user.sendCode', 'Failure', 3, 'Attempt to send SMS with invalid phone number :- ' . $xmlUserDetails['user']['phone']);
                $this->generateJsonResponce(array("response_code" => 113, "description" => $message->description), 'ok', 200);
            } else {
                $this->addLogEntry('user.sendCode', 'Success', 3, 'SMS send to phone :- ' . $xmlUserDetails['user']['phone']);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'SMS sent successfully.'), 'ok', 200);
            }
        }
    }    
  

   public function addLogEntry($api_method, $type, $log_description, $notes = '', $user_id = 0, $trans_id = 0) {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $remote_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $remote_ip = $_SERVER['REMOTE_ADDR'];
        }        

        $date   = date("Y-m-d H:i:s");
        
        $api_log = new ApiLog;
        $api_log->api_method                = $api_method;//$this->api_methods[$api_method];
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
       
 public function generateXMLResponse($response, $status, $http_status = 200, $apiVersion = 'apiv2') {
        //$checkMethod = new REST();

        //$dom = new XmlDomConstructCustomizedForAPI('1.0', 'utf-8');
        Yii::$app->xmlDom->parseMixed(array("response" => $response));
        $data = Yii::$app->xmlDom->saveXML();

        $data = str_replace('<response/>', '<response xmlns="http://' . $_SERVER['SERVER_NAME'] . '/pos/index.php/' . $apiVersion . '" status="' . $status . '" />', $data);
        $data = str_replace('<response>', '<response xmlns="http://' . $_SERVER['SERVER_NAME'] . '/pos/index.php/' . $apiVersion . '" status="' . $status . '" >', $data);
        Yii::$app->REST->response($data, $http_status);
        
        exit;
    }
    
 
public function generateJsonResponce($response){
    
    \yii::$app->response->format = \yii\web\Response::FORMAT_JSON; 
    print_r(json_encode($response));
    exit;
}

   /*
     * API Method : N.A
     * Purpose    : Filter out unwanted characters
     * Returns    : Sanitized data
     */

    public function sanitizeXML($clear = '', $excludeSpecialChars = false, $skipURLDecode = false) {
       
        if(trim($clear) <> ''){
            // Strip HTML Tags
            $clear = strip_tags($clear);
            // Clean up things like &amp;
            $clear = html_entity_decode($clear);
            // Strip out any url-encoded stuff
            if(!$skipURLDecode) {
                $clear = urldecode($clear);
            }
            // Replace non-AlNum characters with space
            if (!$excludeSpecialChars) {
                $clear = preg_replace('/[^A-Za-z0-9]/', ' ', $clear);
            }
            // Replace Multiple spaces with single space
            $clear = preg_replace('/ +/', ' ', $clear);
            // Trim the string of leading/trailing space
            $clear = trim($clear);
        }         
        return $clear;
    }

    public function checkIfUserExists($uname) {
                
        $user = User::find()
            ->where('username = "'.$uname.'"')
            ->one();
        return $user ? true : false; 
    }   
    
    public function checkIfPhoneExists($userPhone){
        
        $phone_length = strlen($userPhone);
        
        switch ($phone_length) {
            case 10:
                $phone_exist = Patient::find()->where('mobile_phone LIKE "' . $userPhone . '" OR mobile_phone LIKE "0' . $userPhone . '" OR mobile_phone LIKE "234' . $userPhone . '" OR mobile_phone LIKE "2340' . $userPhone . '" OR mobile_phone LIKE "+234' . $userPhone . '" OR mobile_phone LIKE "+2340' . $userPhone . '"')->one();                    
                break;
            case 11:
                $zero_stripped_phone=substr($userPhone,1,10);                
                $phone_exist = Patient::find()->where('mobile_phone LIKE "' . $userPhone . '" OR mobile_phone LIKE "234' . $zero_stripped_phone . '" OR mobile_phone LIKE "234' . $userPhone . '" OR mobile_phone LIKE "+234' . $userPhone . '" OR mobile_phone LIKE "+234' . $zero_stripped_phone . '" OR mobile_phone LIKE "' . $zero_stripped_phone . '"')->one();                    
                break;            
            case 13: 
                $code = substr($userPhone,0,3);                
                $code_stripped_phone=substr($userPhone,3,10);
                $phone_exist = Patient::find()->where('mobile_phone LIKE "' . $userPhone . '" OR mobile_phone LIKE "0' . $code_stripped_phone . '" OR mobile_phone LIKE "' . $code_stripped_phone . '" OR mobile_phone LIKE "'. $code .'0' . $code_stripped_phone . '" OR mobile_phone LIKE "'. $code . $code_stripped_phone . '" OR mobile_phone LIKE "+'. $code . $code_stripped_phone . '"')->one();                    
                break; 
            case 14:
                $code = substr($userPhone,0,3);  
                $plus_stripped_code = $plus_code = substr($userPhone,1,3); 
                $plus_code = substr($userPhone,0,4); 
                $code_stripped_phone=substr($userPhone,4,10);  
                
                $phone_exist = Patient::find()->where('mobile_phone LIKE "' . $userPhone . '" OR mobile_phone LIKE "0' . $code_stripped_phone . 
                                '" OR mobile_phone LIKE "'. $code . $code_stripped_phone . '" OR mobile_phone LIKE "+'. $code . $code_stripped_phone . '" OR mobile_phone LIKE "' . $code_stripped_phone . 
                                '" OR mobile_phone LIKE "'. $plus_stripped_code.$code_stripped_phone . '" OR mobile_phone LIKE "'. $plus_code .'0'. $code_stripped_phone . 
                                '" OR mobile_phone LIKE "'. $code .'0'. $code_stripped_phone . '" OR mobile_phone LIKE "'. $plus_stripped_code .'0'. $code_stripped_phone . '"')->one();                    
                break; 
            case 15:
                $plusStripped = substr($userPhone,1,14);                
                $code = substr($userPhone,1,3);  
                $code_stripped_phone=substr($userPhone,5,10);   
                $plus_code = substr($userPhone,0,4);                 
                $phone_exist = Patient::find()->where('mobile_phone LIKE "' . $userPhone . '" OR mobile_phone LIKE "0' .
                                $code_stripped_phone . '" OR mobile_phone LIKE "'. $code . $code_stripped_phone . 
                                '" OR mobile_phone LIKE "+'. $code . $code_stripped_phone . '" OR mobile_phone LIKE "'. $code .'0'. $code_stripped_phone . '" OR mobile_phone LIKE "' . 
                                $code_stripped_phone . '" OR mobile_phone LIKE " ' . $plusStripped . '" OR mobile_phone LIKE "+'. $code .'0'. $code_stripped_phone . '" OR mobile_phone LIKE "' . $plus_code.$code_stripped_phone . '"')->one();                    
                break;            
            default:
                $phone_exist = Patient::find()->where('mobile_phone LIKE "' . $userPhone . '"')->one();                    
                break;
        }        
        return ($phone_exist!=NULL)?true:false;       
    }


    public function validateEmail($email) {
        
        $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
        if (preg_match($regex, $email)) {
            return true;
        } else {
            return false;
        }
        
    }
    
    public function sendSystemEmail($message, $subject, $to, $cc = array()) {
        error_reporting(0);
        
        Yii::$app->mailer->compose()
        ->setFrom($this->administrator_email)
        ->setTo($to)
        ->setSubject($subject)
        ->setTextBody('Plain text content')
        ->setHtmlBody($message)
        ->send();
    }  
    
   public function validatePhone($phone){
     if (preg_match('/([0-9]{9})/', $phone)) {
            return true;
        } else {
            $this->addLogEntry('user.validatephone', 'Failure', 9, 'Illegal characters in mobile number.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Illegal characters in moblie number. Only numbers are allowed.'), 'error', 400);
        }
   } 
       
}
