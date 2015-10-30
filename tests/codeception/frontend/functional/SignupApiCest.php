<?php
use tests\codeception\frontend\FunctionalTester;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);
$I->wantTo('create a new user by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.create">
  <user>
  <firstname>Vidhya</firstname>    
  <lastname>G</lastname>
  <password>123456</password>
  <phone>9554523226</phone>    
  <email>vighya@dfdf.dfd</email>
  <birth_date>123456</birth_date>
  <location>kochi</location>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"User successfully created."));

$I->wantTo('create a new user by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/donation/api', '<request method="donation.create">
  <donation>
  <amount>2000</amount> 
  <gateway>interswitch</gateway>
  <name>Segun</name>
  <email>xtreem88@gmail.com</email>  
  </donation>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"User successfully created."));

<request method="consultation.create">
  <consultation>
  <note>Vidhya</note>    
  <user_id>1</user_id>  
  </consultation>
</request>

http://localhost/phoneadoc/index.php/consultation/api

<request method="payment.create">
  <payment>
  <id>1</id>
  <amount>2000</amount>
  </payment>
</request>

http://localhost/phoneadoc/index.php/payment/api