<?php
use tests\codeception\frontend\ApiGuy;


/* @var $scenario Codeception\Scenario */



class ConsultationApiCest
{

   
   public $error_class = ".help-block";
   public $message_class = ".help-block";
   public $login_text = "Login";


 /* @var $scenario Codeception\Scenario */


  public function consult($loginPage,$values){
        $loginPage->submit($values);

        // $loginPage->submit([
        //     'phone_no' => $phone_no,
        //     'pin_code' => $pin_code
        //     ]);

        return true;

    }

  public function testUserConsultation($I, $scenario){

    
    $I = new ApiGuy($scenario);

	$I->wantTo('make a consultation by API');
	//$I->amHttpAuthenticated('davert','123456');
	$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
	$I->sendPOST('consultation/api', '<request method="consultation.create">
	  <consultation>
	  <note>Vidhya</note>    
	  <user_id>1</user_id>  
	  </consultation>
	</request>');
	$I->seeResponseCodeIs(200);
	$I->seeResponseIsJson();
	$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Consultation successfully created."));

    }

}


    


    

   //  $I->expectTo('see validation errors');
   //  $I->see('Confirm Pin must match new pin', $this->error_class);
   //  $I->expectTo('see that user old pin code matches');

   //  $I->expectTo('see validation errors');
   //  $I->see('Invalid credentials', $this->error_class);


   // $I->amGoingTo('try to login with correct credentials');
   // $loginPage->login('erau', 'password_0');
   // $I->expectTo('see that user is logged');
   // $I->seeLink('Logout (erau)');
   // $I->dontSeeLink('Login');
   // $I->dontSeeLink('Signup');

   

