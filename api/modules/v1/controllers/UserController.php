<?php

namespace api\modules\v1\controllers;

use Yii;
use common\models\Users;
use app\models\User;
use app\models\UserSearch;
//use app\models\Patient;
use app\models\VerifyPhone;
use app\models\Settings;
use app\models\Cms;
use app\models\ApiLog;
use app\models\UserSecurityQueValues;
use common\models\Patient;
use common\models\PatientAllergies;
use common\models\PatientMedications;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\XmlDomConstruct;



include_once("../../common/components/xmlToArray.php");
include_once("../../common/components/XmlDomConstruct.php");
//include_once("common/components/darkunz/yii2sms/RecipientInterface.php");
//include_once("common/components/twiliosms.php");

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
                case 'user.login':
                    $this->userLogin($xmlArray['request']);
                    break;                 
                case 'user.logout':
                    $this->userLogout($xmlArray['request']);
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
        if (($model = Users::findOne($id)) !== null) {
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
     * Returns    : Send code to users phone
     */  
    public function sendVerificationCode($xmlUserDetails) {
        
         if (!isset($xmlUserDetails['user']['phone']) || trim($xmlUserDetails['user']['phone']) == '') {            
            $this->addLogEntry('user.sendCode', 'Failure', 9, 'Phone number missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone number missing.'), 'error', 400);            
        }
        $code = rand(111111,999999);        
        $twilio_message = "Phone a doctor\nPlease use verification Code: " . $code . " to sign up." ;
        //---------------------- TWILIO ----------------------//         
        $twillio = Yii::$app->Twillio;        
        $message = $twillio->getClient()->account->messages->sendMessage($this->twilio_from_phone, // From a valid Twilio number
            $xmlUserDetails['user']['phone'], // Text this number
            $twilio_message
        );
        
        $model                      = new VerifyPhone();
        $model->phone_no            = $xmlUserDetails['user']['phone'];
        $model->verification_code   = $code;
        $model->verified            = 'NO';      
        $model->save();
        $this->addLogEntry('user.sendCode', 'Success', 3, 'User signup verification code send to phone :- ' . $xmlUserDetails['user']['phone']);
        $this->generateJsonResponce(array("response_code" => 100, "description" => 'Verification code sent.'), 'ok', 200);               
        
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
     /*
     * API Method : user.create
     * Purpose    : Create a user  - user signup step 3
     * Returns    : Result of insert operation
     */
       public function createUser($xmlUserDetails) {
     
            $userFullName   = $this->sanitizeXML($xmlUserDetails['user']['userinfo']['fname']).$this->sanitizeXML($xmlUserDetails['user']['userinfo']['lname']);
            $userPhone      = $this->sanitizeXML($xmlUserDetails['user']['patients']['mobile_phone']);
            $userFirstName  = $this->sanitizeXML($xmlUserDetails['user']['userinfo']['fname']);
            $userLastName   = $this->sanitizeXML($xmlUserDetails['user']['userinfo']['lname']);

            if(!isset($xmlUserDetails['user']['userinfo']['username']) || trim($xmlUserDetails['user']['userinfo']['username']) == '') {
                $userName = preg_replace('/\s+/', '', $userFullName); //remove any whitespaces
            } else {
                $userName = $xmlUserDetails['user']['userinfo']['username'];
            }            

            //check if username is available
            $userFlag = true;
            if($this->checkIfUserExists($userName)){
                while($userFlag){
                    $userName .= rand(100, 999);
                    $userFlag = $this->checkIfUserExists($userName);
                }
            }
            
            $security_flag = $email_exists = $email = 0 ;
             
            if($xmlUserDetails['user']['patients']['email']!=NULL){
                //check if email is already in use.
                $email = $this->sanitizeXML($xmlUserDetails['user']['patients']['email'], true);
                $email_exists = Patient::find()->where('email LIKE "'.$email.'"')->one();    
            }else{
                if (!isset($xmlUserDetails['user']['patients']['security_que_value']) || trim($xmlUserDetails['user']['patients']['security_que_value']) == '') {            
                    $this->addLogEntry('user.create', 'Failure', 9, 'Security question and email missing.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Please enter email or answer security question.'), 'error', 400);            
                }
                $security_flag = 1;
            }
            
            //check if phone no. is already in use
            $phone_exists = Patient::find()->where('mobile_phone LIKE "'.$userPhone.'"')->one();
            
            if(preg_match('/[^A-Za-z0-9]/', $userName)) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Illegal characters in username.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Illegal characters in username. Only alphanuerics allowed.'), 'error', 400);
                
            } else if(!$this->validateEmail($email) && $security_flag==0) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Invalid email.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Invalid email.'), 'error', 400);
                
            } else if($email_exists  && $security_flag==0) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Email [' . $email . '] already in use.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Email already in use.'), 'error', 400);
                
            } else if($phone_exists) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Phone [' . $userPhone . '] already in use.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone already in use.'), 'error', 400);
                
            } else {
            
                //create a new user account                      
                
               if(is_array($xmlUserDetails['user']['userinfo'])){
                        $model = new Users;
                        $xmlUserDetails['user']['userinfo']['username'] = $userName; 
                        $xmlUserDetails['user']['userinfo']['create_date'] = (int) time();
                        $xmlUserDetails['user']['userinfo']['auth_key']   = rand(1000, 9999);
                        //echo "<pre>";print_r($xmlUserDetails['user']['userinfo']);exit;
                        $model->load($xmlUserDetails['user']['userinfo']);
                        $model->validate();
                        if($model->getErrors()){
                            $this->addLogEntry('user.creaete', 'Failure', 9, 'Correct the validation errors.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Correct the validation errors.','errors'=>$model->getErrors()), 'error', 400);
                        exit;
                        }                        
                        $model->save();
                    }
             
                if(is_array($xmlUserDetails['user']['userinfo']) && $model->id != NULL){
                    //create a new patient
                    $userId                     = $model->id;
                    $model_patients             = new Patient;
                    $xmlUserDetails['user']['patients']['create_uid']   = $userId; 
                    $xmlUserDetails['user']['patients']['create_date']  = (int) time();                                                 
                    $xmlUserDetails['user']['patients']['fname']        = $userFirstName;
                    $xmlUserDetails['user']['patients']['lname']        = $userLastName;
                    $model_patients->load($xmlUserDetails['user']['patients']);
                    $model_patients->validate();
                    if($model_patients->getErrors()){
                        $this->addLogEntry('user.create', 'Failure', 9, 'Correct the validation errors.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Correct the validation errors.','errors'=>$model->getErrors()), 'error', 400);
                    exit;
                    }                        
                    $model_patients->save();                                
                }
                if($security_flag==1){
                    // Record security que value
                    $model_security_que = new UserSecurityQueValues();
                    $model_security_que->user_id = $userId;
                    $model_security_que->que_id = 1;
                    $model_security_que->user_value = $this->sanitizeXML($xmlUserDetails['user']['patients']['security_que_value'], true); 
                    $model_security_que->save();   
                }
                
                $this->addLogEntry('user.create', 'Success', 3, 'User successfully created. Username :- ' .$userName.',User Id:-'.$userId, $model->id);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'User successfully created',"authkey"=>$xmlUserDetails['user']['userinfo']['auth_key']), 'ok', 200);               
                exit;
            }
         //}
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
            $user_exists = Users::find()->where('id = ' . $xmlUserDetails['user']['userinfo']['id'] . ' AND password LIKE "' . md5($xmlUserDetails['user']['userinfo']['password']) . '"')->one();
            //Authenticate access key before update
            $access_code_exists = Users::find()->where('id = ' . $xmlUserDetails['user']['userinfo']['id'] . ' AND auth_key LIKE "' . $xmlUserDetails['user']['userinfo']['auth_key'] . '"')->one();
            if ($access_code_exists) {
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
                    $user = Users::find()
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
                    $phone_exists = Patient::find()->where('mobile_phone LIKE "' . $userPhone . '"')->all();
                    $phone_check_flag = count($email_exists) >= 2 ? true : false;                    
                    

                    if (preg_match('/[^A-Za-z0-9]/', $userName)) {

                        $this->addLogEntry('user.update', 'Failure', 9, 'Illegal characters in username.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Illegal characters in username. Only alphanuerics allowed.'), 'error', 400);
                    } else if ($xmlUserDetails['user']['patients']['email']!=NULL && !$this->validateEmail($this->sanitizeXML($xmlUserDetails['user']['patients']['email'], true))) {

                        $this->addLogEntry('user.update', 'Failure', 9, 'Invalid email.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Invalid email.'), 'error', 400);
                    } else if ($email_check_flag) {

                        $this->addLogEntry('user.update', 'Failure', 9, 'Email [' . $this->sanitizeXML($xmlUserDetails['user']['userinfo']['email']) . '] already in use.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Email already in use.'), 'error', 400);
                    } else if ($phone_check_flag) {

                        $this->addLogEntry('user.update', 'Failure', 9, 'Phone [' . $userPhone . '] already in use.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone already in use.'), 'error', 400);
                    } else {
                        
               if(is_array($xmlUserDetails['user']['userinfo'])){                        
                        $xmlUserDetails['user']['userinfo']['username'] = $userName;                         
                        $model->load($xmlUserDetails['user']['userinfo']);
                        $model->validate();
                        if($model->getErrors()){
                            $this->addLogEntry('user.update', 'Failure', 9, 'Correct the validation errors.');
                        $this->generateJsonResponce(array("response_code" => 113, "description" => 'Correct the validation errors.','errors'=>$model->getErrors()), 'error', 400);
                        exit;
                        }                        
                        $model->save();
                    }                        
                        
                        $patient_exists = Patient::find()->where('update_uid = ' . $xmlUserDetails['user']['userinfo']['id'])->one();
                        if($patient_exists){
                        $model_patients = Patient::findOne($patient_exists->pid);
                        $xmlUserDetails['user']['patients']['fname']        = $userFirstName;
                        $xmlUserDetails['user']['patients']['lname']        = $userLastName;                        
                    
                        $model_patients->save();
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
     * API Method : user.createPassword
     * Purpose    : user user signup step 4
     * Returns    : Result success or failed
     */      
    public function createPassword($xmlUserDetails){
       if (!isset($xmlUserDetails['user']['id']) || trim($xmlUserDetails['user']['id']) == '') {            
            $this->addLogEntry('user.create password', 'Failure', 9, 'User ID missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User ID missing.'), 'error', 400);            
        }else if (!isset($xmlUserDetails['user']['pin']) || trim($xmlUserDetails['user']['pin']) == '') {            
            $this->addLogEntry('user.create password', 'Failure', 9, 'Pin missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Pin missing.'), 'error', 400);            
        }      
        $user_exists = Users::find()->where('id = ' . $xmlUserDetails['user']['id'])->one();
        if($user_exists!=NULL){
            $model = $this->findModel($this->sanitizeXML($xmlUserDetails['user']['id']));
            $model->password  = md5($this->sanitizeXML($xmlUserDetails['user']['pin']));                         
            $model->save(); 
            //Send mail to registered user
            $patient_exists = Patient::find()->where('update_uid = ' . $xmlUserDetails['user']['id'])->one();
            $email = $patient_exists->email;
           
            if($email != NULL){
                $mail_content   = Cms::find()->where('name LIKE "patient_signup"')->one();
                $mail_title     = $mail_content->title;
                $mail_tmp_body  = $mail_content->content;
                $mail_body      = str_replace(array('{DATE}', '{PHONE}', '{PASSWORD}', '{USER}'),
                                              array(date('m-d-Y'), $user_exists->phone, $this->sanitizeXML($xmlUserDetails['user']['pin']), $patient_exists->firstname." ".$patient_exists->lastname),
                                              $mail_tmp_body);											 
                
                $this->sendSystemEmail($mail_body, $mail_title, $email);                    
                }
            $this->addLogEntry('user.create password', 'Success', 3, 'User sign up completed for ' . $model->username, $model->id);
            $this->generateJsonResponce(array("response_code" => 100, "description" => 'User sign up completed for ' . $model->username), 'ok', 200);        
        }else{
            $this->addLogEntry('user.create password', 'Failure', 9, 'User does not exist.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User creation failed'), 'error', 400); 
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
        
        $user_phone         = $this->sanitizeXML($xmlUserDetails['user']['phone']);
        $user_pass          = $this->sanitizeXML($xmlUserDetails['user']['pin'], true);
        $user_exists        = $patient_exists =0;
        //check if user exist 
        $patient_exists = Patient::find()->where('mobile_phone = '.$user_phone)->one(); 
        //print_r($patient_exists);exit;
        if(($patient_exists) && ($patient_exists->update_uid !=NULL))
        $user_exists = Users::find()->where('id = '.$patient_exists->update_uid.' AND password LIKE "'.md5($user_pass).'"')->one();        
        if($user_exists){
            $id         = $user_exists->id;            
            $model      = Users::findOne($id);         
            $model->auth_key = rand(1000,9999);
            $model->save();     
            $userDetails = array('id'=>$id,'username'=>$user_exists->username,'auth_key'=>$model->auth_key);
            $this->addLogEntry('user.changePin', 'Success', 9, 'User logged in sucesssfully, User Id : '.$id );
            $this->generateJsonResponce(array("response_code" => 100, "description" => $userDetails), 'ok', 200);               
        }
        else{
            $this->addLogEntry('user.changePin', 'Success', 9, 'Failed login attempt with phone:'.$user_phone);
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Login failed, please check your credentials.'), 'error', 400);               
        }     
    }
    
     /*
     * API Method : user.changePin
     * Purpose    : Change user pin
     * Returns    : Result success or failed
     */    
    public function changePin($xmlUserDetails) {
        
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
        $access_code_exists = Users::find()->where('id = ' . $xmlUserDetails['user']['user_id'] . ' AND auth_key LIKE "' . $xmlUserDetails['user']['auth_key'] . '"')->one();
        
        if ($access_code_exists) {
            if ($xmlUserDetails['user']['new_pin'] != $xmlUserDetails['user']['confirm_pin']) {
                $this->addLogEntry('user.changePin', 'Failure', 9, 'New pin and confirmed pin does not match.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'New pin and confirmed pin does not match.'), 'error', 400);
            }

            //check if user exist 
            $user_exists = Users::find()->where('id = ' . $xmlUserDetails['user']['user_id'] . ' AND password LIKE "' . md5($xmlUserDetails['user']['old_pin']) . '"')->one();
            if ($user_exists) {
                $id = $user_exists->id;
                $model = Users::findOne($id);
                $model->password = md5($xmlUserDetails['user']['new_pin']);
                $model->save();
                $this->addLogEntry('user.changePin', 'Success', 9, 'User pin changed sucesssfully for user:' . $xmlUserDetails['user']['user_id']);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'User pin changed sucesssfully.'), 'ok', 200);
            } else {
                $this->addLogEntry('user.changePin', 'Success', 9, 'User pin change failed for user:' . $xmlUserDetails['user']['user_id']);
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your old pin is incorrect.'), 'error', 400);
            }
        } else {
            $this->addLogEntry('user.changePin', 'Success', 9, 'Change user pin access key authentication failed ');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Your access key is invalid.'), 'error', 400);
        }
    }
    
     /*
     * API Method : user.recoverPin
     * Purpose    : recover user pin
     * Returns    : Result success or failed
     */    
    public function recoverPin($xmlUserDetails) {
        
        if (isset($xmlUserDetails['user']['recovery_option'])) {
            $recovery_option = $xmlUserDetails['user']['recovery_option'];
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
                }else if (!isset($xmlUserDetails['user']['security_que_value']) || trim($xmlUserDetails['user']['security_que_value']) == '') {
                    $this->addLogEntry('user.changePin', 'Failure', 9, 'security question value missing.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Answer for security question missing.'), 'error', 400);
                }else if (!isset($xmlUserDetails['user']['security_que_id']) || trim($xmlUserDetails['user']['security_que_id']) == '') {
                    $this->addLogEntry('user.changePin', 'Failure', 9, 'security question Id missing.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Security question ID missing.'), 'error', 400);
                }
                break;

            default:
                break;
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
          
            $model = $this->findModel($this->sanitizeXML($xmlUserDetails['user']['id']));
            $model->auth_key  = NULL;
            $model->save();     
            
            $this->addLogEntry('user.logout', 'Success', 9, 'User logged out sucesssfully, User Id : '.$xmlUserDetails['user']['id'] );
            $this->generateJsonResponce(array("response_code" => 100, "description" => 'Logged out'), 'ok', 200);  
        
    }    

   public function addLogEntry($api_method, $type, $log_description, $notes = '', $user_id = 0, $trans_id = 0) {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $remote_ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $remote_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $remote_ip = $_SERVER['REMOTE_ADDR'];
        }        
       
        /*$t      = microtime(true);
        $micro  = sprintf("%06d",($t - floor($t)) * 1000000);         
        $d      = new \DateTime( date('Y-m-d H:i:s.' . $micro, $t) );       
        $date   = $d->format("Y-m-d H:i:s.u");*/
        $date   = date("Y-m-d H:i:s");
        
        $api_log = new ApiLog;
        $api_log->api_method_id             = 1;//$this->api_methods[$api_method];
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
        
        //$user = Users::findByAttributes(array('username' => $uname));       
        $user = Users::find()
            ->where('username = "'.$uname.'"')
            ->one();
        return $user ? true : false; 
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

        $email = Yii::app()->email;
        $email->to  = $to;
        $email->bcc = (!empty($cc)) ? $cc : null;
        $email->subject = $subject;
        $email->message = $message;
        $email->send();
    }    
       
}