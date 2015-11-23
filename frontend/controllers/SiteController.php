<?php
namespace frontend\controllers;
use app\models\LoginForm;

use Yii;
//use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Url;

require_once("common/components/Send.php");
require_once("common/components/checkLogin.php");
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public $session;

    

    

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->layout = "index";
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        
        if (!\Yii::$app->user->isGuest) {
            //return $this->goHome();
        }
        $resp = '';
        if($_POST){
            if($this->login($_POST['code'].$_POST['phone'],$_POST['pin']))
                return $this->redirect(Url::toRoute('/consultation/index'));
        
        //$resp = $response->body->description;
        }
            //var_dump($response->body);
        $session = Yii::$app->session;
        $error = $session['error'];
        $session['error'] = NULL;
        return $this->render('login', [
         //   'model' => $model,
            'response'=>$error
        ]);
    
    }

    public function login($phone,$pin){
        $response = pull('user/api','
            <request method="user.login">
          <user>
          <phone>'.$phone.'</phone>    
          <pin>'.$pin.'</pin>    
          </user>
        </request>');
        
        //var_dump($response->body);
        
        $session = Yii::$app->session;
        if($response->body->response_code==100){
            
            // close a session
            $session->close();

            // destroys all data registered to a session.
            $session->destroy();

            $session->open();
            $session['id'] = $response->body->description->id;
            $session['authkey'] = $response->body->description->auth_key;
            $session['username'] = $response->body->description->username;
            return true;
        }else{
            $session['error'] =  $response->body->description;
            return false;
        }
    }



    

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        $session = Yii::$app->session;
        $response = pull('user/api','
          <request method="user.logout">
            <user>
            <id>'.$session['id'].'</id>    
            <auth_key>'.$session['authkey'].'</auth_key>    
            </user>
          </request>
          ');
       
            $session = Yii::$app->session;
            // close a session
            $session->close();

            // destroys all data registered to a session.
            $session->destroy();
            $session['id'] = NULL;
            $session['authkey'] = NULL;
            $session['username'] = NULL;

           
           

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        $this->layout = "index";
        return $this->render('about');
    }

     public function actionServices()
    {
        $this->layout = "index";
        return $this->render('services');
    }

    public function actionRecover()
    {
        $q = pull('security-question/api','
               <request method="question.getAllSecurityQues">
                ');
        $question = NULL;
        //var_dump($_POST);
        
        if($_POST['email-button']=='clicked'){
      
          $response = pull('user/api','
            <request method="user.recoverPin">
              <user>
              <recovery_option>email</recovery_option>    
              <email>'.$_POST['email'].'</email>    
              </user>
            </request>
            ');
         $resp = $response->body->description;
            if($response->body->response_code==100){
            
            return $this->redirect('login');
            }
        }elseif($_POST['phone-button']=='clicked'){
          
          $response = pull('user/api','
            <request method="user.recoverPin">
              <user>
              <recovery_option>phone</recovery_option>  
              <phone>'.$_POST['code'].$_POST['phone'].'</phone> 
              <security_que_id>'.$_POST['question'].'</security_que_id> 
              <security_que_value>'.$_POST['answer'].'</security_que_value>    
              </user>
            </request>
            ');
          $resp = $response->body->description;
            if($response->body->response_code==100){
            
            return $this->redirect('login');
            }
        }
        if($q->body->response_code==100){
          $question = $q->body->description;
        }
        return $this->render('recover', [
            'question'=>$question,
            'error'=>$resp,
            
        ]);
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        
        if($session['username'])
            return $this->redirect(Url::toRoute('/consultation/index'));
        $model = new SignupForm();
        
        
         $resp='';
           
        if($_POST['phone']){
            //if (!$this->session->isActive)
            // open a session
            //$this->session->open();
            $response = pull('user/api','<request method="user.sendCode">
              <user>
              <phone>'.$_POST['code'].$_POST['phone'].'</phone>   
              </user>
            </request>');
            $session = Yii::$app->session;
            $session->set('phone',$_POST['code'].$_POST['phone']);
            //$_SESSION['phone'] = $_POST['phone'];
            if($response->body->response_code==100){
            
            return $this->redirect('signup2');
            }
            $resp = $response->body->description;

        }

        return $this->render('signup', [
            'model' => $model,
            'response'=>$resp
        ]);
    }

    public function actionSignup2()
    {
        if($session['username'])
            return $this->redirect(Url::toRoute('/consultation/index'));
        $model = new SignupForm();
        $session = Yii::$app->session;
        $resp='';
        if($_POST['verify']){
            
            $response = pull('user/api','
                <request method="user.verifyPhone">
                  <user>
                  <phone>'.$session['phone'].'</phone>    
                  <verification_code>'.$_POST['verify'].'</verification_code>    
                  </user>
                </request>
                ');

            if($response->body->response_code==100){
            

            // check if a session is already open
            
            return $this->redirect('signup3');
            }
            $resp = $response->body->description;
            //$session['phone'] = $_POST['phone'];
            
            //return $this->redirect('signup3');

        }
        //var_dump($session['phone']);
        return $this->render('signup2', [
            'model' => $model,
            'phone'=>$session['phone'],
            'response'=>$resp
        ]);
    }

    public function actionSignup3()
    {
        if($session['username'])
            return $this->redirect(Url::toRoute('/consultation/index'));
        $model = new SignupForm();
        $session = Yii::$app->session;
        $resp='';
        $q = pull('security-question/api','
               <request method="question.getAllSecurityQues">
                ');
        $question = NULL;
        //var_dump($q->body);
        if($q->body->response_code==100){
          $question = $q->body->description;
        }
        if($_POST){
            
            $response = pull('user/api','
                <request method="user.create">
                  <user>
                  <userinfo>
                  <fname>'.$_POST['firstname'].'</fname>    
                  <lname>'.$_POST['lastname'].'</lname>
                  <password>123456</password>
                  </userinfo>
                  <patients>
                  <mobile_phone>'.$session['phone'].'</mobile_phone>    
                  <email>'.$_POST['email'].'</email>
                  <security_que_id>'.$_POST['question'].'</security_que_id>
                  <security_que_value>'.$_POST['answer'].'</security_que_value>
                  <DOB>'.$_POST['age'].'</DOB>
                  <address></address>    
                  <sex>'.$_POST['sex'].'</sex>
                  </patients>
                  </user>
                </request>
               
                ');
            //var_dump($response->body);
            if($response->body->response_code==100){
            $session->set('id',$response->body->user_id);
            $session->set('authkey',$response->body->authkey);
            
            return $this->redirect('signup4');
            }
            $resp = $response->body->description;
            //$session['phone'] = $_POST['phone'];
            
            //return $this->redirect('signup3');

        }
        //var_dump($session['phone']);
        return $this->render('signup3', [
            'model' => $model,
            'phone'=>$session['phone'],
            'question'=>$question,
            'response'=>$resp
        ]);
    }

    public function actionSignup4()
    {
        if($session['username'])
            return $this->redirect(Url::toRoute('/consultation/index'));
        $model = new SignupForm();
        $session = Yii::$app->session;
        $resp='';
        if($_POST['pin']){
            
            $response = pull('user/api','
                <request method="user.createPassword">
                  <user>
                  <id>'.$session['id'].'</id>    
                  <pin>'.$_POST['pin'].'</pin> 
                   <confirm_pin>'.$_POST['confirm_pin'].'</confirm_pin>   
                  </user>
                </request>
                ');
            if($response->body->response_code==100){
            

            // check if a session is already open
            
            if($this->login($session['phone'],$_POST['pin']))
                return $this->redirect(Url::toRoute('/consultation/index'));

            }
            $resp = $response->body->description;
            //$session['phone'] = $_POST['phone'];
            
            //return $this->redirect('signup3');

        }
        //var_dump($session['phone']);
        return $this->render('signup4', [
            'model' => $model,
            'phone'=>$session['phone'],
            'response'=>$resp
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
