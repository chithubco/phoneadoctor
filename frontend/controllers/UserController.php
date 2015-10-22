<?php

namespace app\controllers;
namespace frontend\controllers;


use Yii;
use app\models\User;
use app\models\UserSearch;
use app\models\Settings;
use app\models\ApiLog;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\components\XmlDomConstruct;

include_once("common/components/xmlToArray.php");
include_once("common/components/XmlDomConstruct.php");

class UserController extends Controller
{
    public $administrator_email;
    public $base_currency_code;
    public $configURL;
    public $homeURL;
    public $salt                = 'phoneDoctor';
    public $currencyLabel       = 'N';  
    
    
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
                case 'user.update':
                    $this->updateUser($xmlArray['request']);
                    break;
                case 'user.changePassword':
                    $this->updatePassword($xmlArray['request']);
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
    
       public function createUser($xmlUserDetails) {

      //check the mandatory fields
 
        if (!isset($xmlUserDetails['user']['password']) || trim($xmlUserDetails['user']['password']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Password missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Password missing.'), 'error', 400);
            
        }else if (!isset($xmlUserDetails['user']['firstname']) || trim($xmlUserDetails['user']['firstname']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Firstname missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Firstname missing.'), 'error', 400);
            
        }else if (!isset($xmlUserDetails['user']['lastname']) || trim($xmlUserDetails['user']['lastname']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Lastname missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Lastname missing.'), 'error', 400);
            
        }else if (!isset($xmlUserDetails['user']['phone']) || trim($xmlUserDetails['user']['phone']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Phone missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone missing.'), 'error', 400);
            
        } 
        else if (!isset($xmlUserDetails['user']['email']) || trim($xmlUserDetails['user']['email']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'User email missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User email missing.'), 'error', 400);
            
        } else if((!isset($xmlUserDetails['user']['birth_date']) || trim($xmlUserDetails['user']['birth_date']) == '')) {
                
            $this->addLogEntry('user.create', 'Failure', 9, 'Date of birth missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Date of birth missing.'), 'error', 400);

        } else if((!isset($xmlUserDetails['user']['location']) || trim($xmlUserDetails['user']['location']) == '')) {
                
            $this->addLogEntry('user.create', 'Failure', 9, 'Location missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Location missing.'), 'error', 400);

        } else {
            $userFullName   = $this->sanitizeXML($xmlUserDetails['user']['firstname']).$this->sanitizeXML($xmlUserDetails['user']['lastname']);
            $userPhone      = $this->sanitizeXML($xmlUserDetails['user']['phone']);
            $userFirstName  = $this->sanitizeXML($xmlUserDetails['user']['firstname']);
            $userLastName   = $this->sanitizeXML($xmlUserDetails['user']['lastname']);

            if(!isset($xmlUserDetails['user']['username']) || trim($xmlUserDetails['user']['username']) == '') {
                $userName = preg_replace('/\s+/', '', $userFullName); //remove any whitespaces
            } else {
                $userName = $xmlUserDetails['user']['username'];
            }            

            //check if username is available
            $userFlag = true;
            if($this->checkIfUserExists($userName)){
                while($userFlag){
                    $userName .= rand(100, 999);
                    $userFlag = $this->checkIfUserExists($userName);
                }
            }
            
            //check if email is already in use.
            $email = $this->sanitizeXML($xmlUserDetails['user']['email'], true);
            $email_exists = User::find()->where('email LIKE "'.$email.'"')->one();           
            
            //check if phone no. is already in use
            $phone_exists = User::find()->where('phone LIKE "'.$userPhone.'"')->one();
            
            if(preg_match('/[^A-Za-z0-9]/', $userName)) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Illegal characters in username.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Illegal characters in username. Only alphanuerics allowed.'), 'error', 400);
                
            } else if(!$this->validateEmail($this->sanitizeXML($xmlUserDetails['user']['email'], true))) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Invalid email.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Invalid email.'), 'error', 400);
                
            } else if($email_exists) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Email [' . $this->sanitizeXML($xmlUserDetails['user']['email']) . '] already in use.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Email already in use.'), 'error', 400);
                
            } else if($phone_exists) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Phone [' . $userPhone . '] already in use.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone already in use.'), 'error', 400);
                
            } else {
            
                //create a new user account
                $model                      = new User();
                
                //$activate_key               = time() . rand(1000, 9999);echo 1;
                $model->username            = $userName;
                $model->password            = md5($xmlUserDetails['user']['password']);
                $model->phone               = $userPhone;
                $model->firstname           = $userFirstName;
                $model->lastname            = $userLastName;
                $model->email               = $email;
                $model->birth_date          = '3434';//$this->sanitizeXML($xmlUserDetails['user']['birth_date'], true);
                $model->location            = $this->sanitizeXML($xmlUserDetails['user']['location'], true);
                $model->createtime          = (int) time();
                $model->status              = 1;
                $model->role                = 'USER';
   
                $model->save();
               
                $this->addLogEntry('user.create', 'Success', 3, 'User successfully created. Username :- ' . $model->username, $model->id);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'User successfully created.'), 'ok', 200);               
                exit;
            }
         }
       }
       
    /*
     * API Method : user.update
     * Purpose    : Update existing user
     * Returns    : Result of update operation
     */
    
    public function updateUser($xmlUserDetails) {
        
        if (!isset($xmlUserDetails['user']['password']) || trim($xmlUserDetails['user']['password']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Password missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Password missing.'), 'error', 400);
            
        }else if (!isset($xmlUserDetails['user']['firstname']) || trim($xmlUserDetails['user']['firstname']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Firstname missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Firstname missing.'), 'error', 400);
            
        }else if (!isset($xmlUserDetails['user']['lastname']) || trim($xmlUserDetails['user']['lastname']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Lastname missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Lastname missing.'), 'error', 400);
            
        }else if (!isset($xmlUserDetails['user']['phone']) || trim($xmlUserDetails['user']['phone']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'Phone missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone missing.'), 'error', 400);
            
        } 
        else if (!isset($xmlUserDetails['user']['email']) || trim($xmlUserDetails['user']['email']) == '') {
            
            $this->addLogEntry('user.create', 'Failure', 9, 'User email missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'User email missing.'), 'error', 400);
            
        } else if((!isset($xmlUserDetails['user']['birth_date']) || trim($xmlUserDetails['user']['birth_date']) == '')) {
                
            $this->addLogEntry('user.create', 'Failure', 9, 'Date of birth missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Date of birth missing.'), 'error', 400);

        } else if((!isset($xmlUserDetails['user']['location']) || trim($xmlUserDetails['user']['location']) == '')) {
                
            $this->addLogEntry('user.create', 'Failure', 9, 'Location missing.');
            $this->generateJsonResponce(array("response_code" => 113, "description" => 'Location missing.'), 'error', 400);

        } else {
            
            $model          = $this->findModel($this->sanitizeXML($xmlUserDetails['user']['id']));            
            $userFullName   = $this->sanitizeXML($xmlUserDetails['user']['firstname']).$this->sanitizeXML($xmlUserDetails['user']['lastname']);
            $userPhone      = $this->sanitizeXML($xmlUserDetails['user']['phone']);
            $userFirstName  = $this->sanitizeXML($xmlUserDetails['user']['firstname']);
            $userLastName   = $this->sanitizeXML($xmlUserDetails['user']['lastname']);

            if(!isset($xmlUserDetails['user']['username']) || trim($xmlUserDetails['user']['username']) == '') {
                $userName = preg_replace('/\s+/', '', $userFullName); //remove any whitespaces
            } else {
                $userName = $xmlUserDetails['user']['username'];
            }   
            
            

            //check if username is available
            $userFlag = true;
            $user = User::find()
                    ->where('username = "'.$userName.'"')
                    ->all();  
            $userNameUpdateFlag = count($user) >= 2 ? true : false;
            
            if($userNameUpdateFlag){
                while($userFlag){
                    $userName .= rand(100, 999);
                    $userFlag = $this->checkIfUserExists($userName);
                }
            }
            
            //check if email is already in use.
            $email = $this->sanitizeXML($xmlUserDetails['user']['email'], true);
            
            $email_exists = User::find()->where('email LIKE "'.$email.'"')->all();  
            $email_check_flag = count($email_exists) >= 2 ? true : false;
            
            //check if phone no. is already in use
            $phone_exists = User::find()->where('phone LIKE "'.$userPhone.'"')->all();  
            $phone_check_flag = count($email_exists) >= 2 ? true : false;
            
            if(preg_match('/[^A-Za-z0-9]/', $userName)) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Illegal characters in username.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Illegal characters in username. Only alphanuerics allowed.'), 'error', 400);
                
            } else if(!$this->validateEmail($this->sanitizeXML($xmlUserDetails['user']['email'], true))) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Invalid email.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Invalid email.'), 'error', 400);
                
            } else if($email_check_flag) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Email [' . $this->sanitizeXML($xmlUserDetails['user']['email']) . '] already in use.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Email already in use.'), 'error', 400);
                
            } else if($phone_check_flag) {
                
                $this->addLogEntry('user.create', 'Failure', 9, 'Phone [' . $userPhone . '] already in use.');
                $this->generateJsonResponce(array("response_code" => 113, "description" => 'Phone already in use.'), 'error', 400);
                
            } else {
            
                $model->username            = $userName;
                $model->password            = md5($xmlUserDetails['user']['password']);
                $model->phone               = $userPhone;
                $model->firstname           = $userFirstName;
                $model->lastname            = $userLastName;
                $model->email               = $email;
                $model->birth_date          = '3434';//$this->sanitizeXML($xmlUserDetails['user']['birth_date'], true);
                $model->location            = $this->sanitizeXML($xmlUserDetails['user']['location'], true);
                $model->createtime          = (int) time();
                $model->status              = 1;
                $model->role                = 'USER';
   
                $model->save();
               
                $this->addLogEntry('user.create', 'Success', 3, 'User successfully created. Username :- ' . $model->username, $model->id);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'User successfully created.'), 'ok', 200);               
                exit;
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
    print_r($response);
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
        
        //$user = User::findByAttributes(array('username' => $uname));       
        $user = User::find()
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
       
}
