<?php

namespace tests\codeception\frontend\functional;

use tests\codeception\frontend\_pages\changePinPage;
use common\models\User;


class ChangePinCest
{

   
   public $error_class = ".help-block";
   public $message_class = ".help-block";
   public $changePin_text = "Change Pin";
   

   public $changepin_url = Yii::$app->homeUrl."/chagepin";

 /* @var $scenario Codeception\Scenario */


  public function changepin($changePinPage,$values){
        $changePinPage->submit($values);

        return true;

    }

  public function testUserChangePin($I, $scenario)

    
    $I->wantTo('ensure Change pin page works');

    $changePinPage = ChangePinPage::openBy($I);


    $I->amGoingTo ('Change Pin with invalid confirm pin (case 1)');
    $old_pin => '1234';
    $new_pin => '4321';
    $confirm_pin => '4213';
    $this->changePin($changePinPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see validation errors');
    $I->see('Pin change not successful', $this->error_class);


    $I->amGoingTo ('Change Pin with blank old pin (case 2)');
    $old_pin => '';
    $new_pin => '8584';
    $confirm_pin => '8584';
    $this->changePin($changePinPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see validation errors');
    $I->see('Pin change not successful', $this->error_class);



    $I->amGoingTo ('Change Pin with invalid old pin (case 3)');
    $old_pin => '8584';
    $new_pin => '4321';
    $confirm_pin => '4321';
    $this->changePin($changePinPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see validation errors');
    $I->see('Pin change not successful', $this->error_class);


   
    $I->amGoingTo ('Change Pin with blank new pin (case 4)');
    $old_pin => '1234';
    $new_pin => '';
    $confirm_pin => '8584';
    $this->changePin($changePinPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see validation errors');
    $I->see('Pin change not successful', $this->error_class);



    $I->amGoingTo ('Change Pin with invalid new pin (case 5)');
    $old_pin => '1234';
    $new_pin => '4t2!';
    $confirm_pin => '4t2!';
    $this->changePin($changePinPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see validation errors');
    $I->see('Pin change not successful', $this->error_class);



    $I->amGoingTo ('Change Pin with same details as old pin and new pin (case 6)');
    $old_pin => '1234';
    $new_pin => '1234';
    $confirm_pin => '1234';
    $this->changePin($changePinPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see validation errors');
    $I->see('Pin change not successful', $this->error_class);


    //valid everything
    $I->amGoingTo ('Change Pin with valid details (case 7)');
    $old_pin => '1234';
    $new_pin => '4321';
    $confirm_pin => '4321';
    $this->changePin($changePinPage,['old_pin'=>$old_pin,
                             'new_pin'=>$new_pin,
                             'confirm_pin'=>$confirm_pin]);
    $I->expectTo('see success');
    $I->see('pin changed successfully', $this->message_class);

