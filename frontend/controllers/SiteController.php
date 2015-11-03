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
/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public $session;

    

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

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
            if($this->login($_POST['phone'],$_POST['pin']))
                return $this->redirect(Url::toRoute('/consultation/index'));
        
        $resp = $response->body->description;
        }
            //var_dump($response->body);

        
        return $this->render('login', [
            'model' => $model,
            'response'=>$resp
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
        if($response->body->response_code==100){
            $session = Yii::$app->session;
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
        Yii::$app->user->logout();

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
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        
        $model = new SignupForm();
        
        
        
           
        if($_POST['phone']){
            //if (!$this->session->isActive)
            // open a session
            //$this->session->open();
            /*$response = pull('user/api','<request method="user.sendCode">
              <user>
              <phone>'.$_POST['phone'].'</phone>   
              </user>
            </request>');*/
            $session = Yii::$app->session;
            $session->set('phone',$_POST['phone']);
            //$_SESSION['phone'] = $_POST['phone'];
            
            return $this->redirect('signup2');

        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionSignup2()
    {
        
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
        
        $model = new SignupForm();
        $session = Yii::$app->session;
        $resp='';
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
                  <security_que_value>'.$_POST['question'].'</security_que_value>
                  <DOB>'.$_POST['age'].'</DOB>
                  <address></address>    
                  <sex>'.$_POST['sex'].'</sex>
                  </patients>
                  </user>
                </request>
               
                ');
            
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
            'response'=>$resp
        ]);
    }

    public function actionSignup4()
    {
        
        $model = new SignupForm();
        $session = Yii::$app->session;
        $resp='';
        if($_POST['pin']){
            
            $response = pull('user/api','
                <request method="user.createPassword">
                  <user>
                  <id>'.$session['id'].'</id>    
                  <pin>'.$_POST['pin'].'</pin>    
                  </user>
                </request>
                ');
            if($response->body->response_code==100){
            

            // check if a session is already open
            
            if($this->login($session['id'],$_POST['pin']))
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
