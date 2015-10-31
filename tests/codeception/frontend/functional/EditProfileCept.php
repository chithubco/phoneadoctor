<?php

namespace tests\codeception\frontend\functional;

use tests\codeception\frontend\_pages\editProfilePage;
use common\models\User;


class editProfileCest
{

   
   public $error_class = ".help-block";
   public $message_class = ".help-block";
   public $editprofile_text = "Edit Profile";
   

   public $editprofile_url = Yii::$app->homeUrl."/editprofile";

 /* @var $scenario Codeception\Scenario */


  public function editprofile($editProfilePage, $pin_code, $values){
        $editProfilePage->submit($pin_code, $values);

        return true;

    }

  public function testUserEditProfile($I, $scenario)

    
    $I->wantTo('ensure edit profile page works');

    $editProfilePage = EditProfilePage::openBy($I);


    $I->amGoingTo ('Edit profile with valid pin (case 1)');
    $pin_code => '1234';
    $this->editProfile($editProfilePage, $pin_code, [
                             'first_name'=>'Ladel',
                             'last_name'=>'Martin',
                             'email'=>'tizfreak2000@yahoo.com',
                             'DOB'=> '29/04/00']);
    $I->expectTo('see updated user info displayed');
    $I->see('Profile update successful', $this->message_class);
    

    $I->amGoingTo ('Edit profile with invalid pin (case 1)');
    $pin_code => '4321';
    $this->editProfile($editProfilePage, $pin_code, [
                             'first_name'=>'Ladel',
                             'last_name'=>'Martin',
                             'email'=>'tizfreak2000@yahoo.com',
                             'DOB'=> '29/04/00']);
    $I->expectTo('see validation errors');
    $I->see('Incorrect Pin', $this->message_class);