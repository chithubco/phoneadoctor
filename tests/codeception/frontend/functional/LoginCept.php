<?php

namespace tests\codeception\frontend\functional;

use tests\codeception\frontend\_pages\LoginPage;
use common\models\User;


class LoginCest
{

   
   public $error_class = ".help-block";
   public $message_class = ".help-block";
   public $login_text = "Login";
   

   public $login_url = Yii::$app->homeUrl."/login";

 /* @var $scenario Codeception\Scenario */


  public function login($loginPage,$phone_no,$pin_code){
        $loginPage->submit([
            'phone_no' => $phone_no,
            'pin_code' => $pin_code
            ]);

        return true;

    }

  public function testUserLogin($I, $scenario)

    
    $I->wantTo('ensure login page works');

    $loginPage = LoginPage::openBy($I);

    //Test #1
    $I->amGoingTo ('Submit login with no data (case 1)');
    $this->login($loginPage,['phone_no'=>'',
                             'pin_code'=>'']);
                
    $I->expectTo('see validation errors');
    $I->see('Phone cannot be blank.', $this->error_class);


    //Test #2
    $I->amGoingTo ('Submit login with registered phone_no and blank Pin (case 2)');
    $this->login($loginPage,['phone_no'=>'+2348098305580',
                             'pin_code'=>'']);
                
    $I->expectTo('see validation errors');
    $I->see('Pin cannot be blank.', $this->error_class);


    //Test #3
    $I->amGoingTo ('Submit login with registered phone_no and invalid Pin (case 3)');
    $this->login($loginPage,['phone_no'=>'+2348098305580',
                             'pin_code'=>'4321']);
                
    $I->expectTo('see validation errors');
    $I->see('Invalid credentials', $this->error_class);


    //Test #4
    $I->amGoingTo ('Submit login with registered phone_no and invalid Pin (case 4)');
    $this->login($loginPage,['phone_no'=>'+2348098305580',
                             'pin_code'=>'!@#1']);
                
    $I->expectTo('see validation errors');
    $I->see('Invalid credentials', $this->error_class);


    //Test #5
    $I->amGoingTo ('Submit login with unregistered phone_no and invalid Pin (case 5)');
    $this->login($loginPage,['phone_no'=>'+2348098305580',
                             'pin_code'=>'abc1']);
                
    $I->expectTo('see validation errors');
    $I->see('Invalid credentials', $this->error_class);



    //Test #6
    $I->amGoingTo ('Submit login with unregistered phone_no and blank Pin (case 6)');
    $this->login($loginPage,['phone_no'=>'+2348058305580',
                             'pin_code'=>'']);
                
    $I->expectTo('see validation errors');
    $I->see('Pin cannot be blank.', $this->error_class);


    //Test #7
    $I->amGoingTo ('Submit login with unregistered phone_no and invalid Pin (case 7)');
    $this->login($loginPage,['phone_no'=>'+2348058305580',
                             'pin_code'=>'4321']);
                
    $I->expectTo('see validation errors');
    $I->see('Invalid credentials', $this->error_class);


    //Test #7
    $I->amGoingTo ('Submit login with unregistered phone_no and invalid Pin (case 7)');
    $this->login($loginPage,['phone_no'=>'+2348058305580',
                             'pin_code'=>'!@#1']);
                
    $I->expectTo('see validation errors');
    $I->see('Invalid credentials', $this->error_class);


    //Test #8
    $I->amGoingTo ('Submit login with unregistered phone_no and invalid Pin (case 8)');
    $this->login($loginPage,['phone_no'=>'+2348058305580',
                             'pin_code'=>'abc1']);
                
    $I->expectTo('see validation errors');
    $I->see('Invalid credentials', $this->error_class);


    //Test #9
    $I->amGoingTo ('Submit login with registered phone_no and valid Pin (case 9)');
    $this->login($loginPage,['phone_no'=>'+2348098305580',
                             'pin_code'=>'1234']);
                
    $I->expectTo('see that user is logged in');
    $I->see('Dashboard', $this->message_class);






   $I->amGoingTo('try to login with correct credentials');
   $loginPage->login('erau', 'password_0');
   $I->expectTo('see that user is logged');
   $I->seeLink('Logout (erau)');
   $I->dontSeeLink('Login');
   $I->dontSeeLink('Signup');

   

