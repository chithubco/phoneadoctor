<?php
namespace app\components;
namespace frontend\components;

use Yii;
use common\models\Donations;
use common\models\Users;
use common\models\Transactions;
use common\models\PaymentAttempts;
use yii\filters\VerbFilter;


class Donation
{
    public $administrator_email;
    public $base_currency_code;
    public $configURL;
    public $homeURL;
    public $salt                = 'phoneDoctor';
    public $currencyLabel       = 'N';  

    
  
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Donations::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
   
     /*
     * API Method : user.create
     * Purpose    : Create a user
     * Returns    : Result of insert operation
     */
       public function run($id) {

                
                $this->addLogEntry('donation.create', 'Success', 3, 'Donation successfully created. Username :- ' . $name, $model->id);
                $this->generateJsonResponce(array("response_code" => 100, "description" => 'Donation successfully created.',"response"=>array("payment_id"=>$paym->id)), 'ok', 200);               
                exit;
            
         
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

    
       
}
