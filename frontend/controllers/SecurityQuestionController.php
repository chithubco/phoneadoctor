<?php
namespace app\controllers;
namespace frontend\controllers;

use Yii;
use app\models\SecurityQuestion;
use app\models\SecurityQuestionSearch;
use app\models\Settings;
use app\models\Cms;
use app\models\ApiLog;
use app\models\UserSecurityQueValues;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

include_once("common/components/xmlToArray.php");
include_once("common/components/XmlDomConstruct.php");

/**
 * SecurityQuestionController implements the CRUD actions for SecurityQuestion model.
 */
class SecurityQuestionController extends Controller
{
    public $administrator_email;
    public $base_currency_code;
    public $configURL;
    public $homeURL;
    public $salt                = 'phoneDoctor';
    public $currencyLabel       = 'N';  
    
    
   public function beforeAction($action) { 
        

        $settingValues = Settings::find()->all();                       

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
                
                case 'question.addSecurityQue':
                    $this->addSecurityQue($xmlArray['request']);
                    break;  
                case 'question.getAllSecurityQues':
                    $this->getAllSecurityQue($xmlArray['request']);
                    break;                 
                
                
                default:
                   $this->generateJsonResponce(array("response_code" => 999, "description" => 'Unknown method.'), 'error', 400);
                    break;
            }
        }
    }    

    /**
     * Lists all SecurityQuestion models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SecurityQuestionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SecurityQuestion model.
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
     * Creates a new SecurityQuestion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SecurityQuestion();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SecurityQuestion model.
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
     * Deletes an existing SecurityQuestion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

        /*
     * API Method : security.addSecurityQue
     * Purpose    : add security question
     * Returns    : Added or failed
     */    
    public function addSecurityQue($xmlUserDetails) {
        
       if (!isset($xmlUserDetails['security']['user']['id']) || trim($xmlUserDetails['security']['user']['id']) == '') {
                    $this->addLogEntry('user.addSecurityQue', 'Failure', 9, 'User Id missing.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'User Id missing.'), 'error', 400);
          } elseif (!isset($xmlUserDetails['security']['question_text']['question']) || trim($xmlUserDetails['security']['question_text']['question']) == '') {
                    $this->addLogEntry('user.addSecurityQue', 'Failure', 9, 'Question text missing.');
                    $this->generateJsonResponce(array("response_code" => 113, "description" => 'Question text missing.'), 'error', 400);
          }
        
          $model = new SecurityQuestion;
          $model->question  = $xmlUserDetails['security']['question_text']['question'];
          $model->save();
          $this->addLogEntry('security.addSecurityQue', 'Success', 3, 'Security question added');
          $this->generateJsonResponce(array("response_code" => 100, "description" => 'Security question added'), 'ok', 200);                         
    }
    
        /*
     * API Method : security.getAllSecurityQue
     * Purpose    : add security question
     * Returns    : Added or failed
     */    
    public function getAllSecurityQue($xmlUserDetails) {
          
          $securityQues = SecurityQuestion::find()
                         ->where('1=1')
                         ->all();
          
            foreach ($securityQues as $key => $value) {
              $securityQuestions[$value->id] = $value->question;   
            }
          $this->addLogEntry('security.addSecurityQue', 'Success', 3, 'Security question added');
          $this->generateJsonResponce(array("response_code" => 100, "description" => $securityQuestions), 'ok', 200);                         
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
    
    /**
     * Finds the SecurityQuestion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SecurityQuestion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SecurityQuestion::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    
}
