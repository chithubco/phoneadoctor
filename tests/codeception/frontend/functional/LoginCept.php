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


  public function login($loginPage,$values){
        $loginPage->submit($values);

        // $loginPage->submit([
        //     'phone_no' => $phone_no,
        //     'pin_code' => $pin_code
        //     ]);

        return true;

    }
  public function changePin($loginPage,$values){
        $loginPage->submit($values);

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



    //Test #8
    $I->amGoingTo ('Change Pin (case 8)');
    //invalid confirm pin
    $old_pin = '1234';
    $new_pin = '8584';
    $confirm_pin = '7787';
    $this->changePin($loginPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see validation errors');
    $I->see('Confirm Pin must match new pin', $this->error_class);



    //blank old pin
    $old_pin = '';
    $new_pin = '8584';
    $confirm_pin = '8584';
    $this->changePin($loginPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see validation errors');
    $I->see('old pin cannot be blank', $this->error_class);

    //blank new pin
    $old_pin = '1234';
    $new_pin = '';
    $confirm_pin = '8584';
    $this->changePin($loginPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see validation errors');
    $I->see('new pin cannot be blank', $this->error_class);



    //valid everything
    $old_pin = '1234';
    $new_pin = '8584';
    $confirm_pin = '8584';
    $this->changePin($loginPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see success');
    $I->see('pin changed', $this->message_class);


    $I->expectTo('see validation errors');
    $I->see('Confirm Pin must match new pin', $this->error_class);
    $I->expectTo('see that user old pin code matches');

    $I->expectTo('see validation errors');
    $I->see('Invalid credentials', $this->error_class);



   $I->amGoingTo('try to login with correct credentials');
   $loginPage->login('erau', 'password_0');
   $I->expectTo('see that user is logged');
   $I->seeLink('Logout (erau)');
   $I->dontSeeLink('Login');
   $I->dontSeeLink('Signup');

   

