<?php
use tests\codeception\frontend\ApiGuy;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);

$I->wantTo('Submit a valid phone no for verification by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.sendCode">
  <user>
  <phone>+2348098305580</phone> 
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array("response_code" => 100, "description" => 'Verification code sent.'));


$I->wantTo('Submit an invalid phone no for verification by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.sendCode">
  <user>
  <phone>+234doctor</phone> 
  </user>
</request>');
$I->seeResponseCodeIs(500);
//$I->seeResponseIsJson();
//$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Invalid phone no."));


$I->wantTo('Submit an existing phone no for verification by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.sendCode">
  <user>
  <phone>+2348098305580</phone> 
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
//$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Phone no.already exists"));

//step 2



$I->wantTo('Verify a phone with a invalid verification code by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.verifyPhone">
  <user>
  <phone>+2348098305580</phone>    
  <verification_code>533456</verification_code>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array("response_code" => 113, "description" => 'Your code do not match.'));

$I->wantTo('Verify a phone with a valid verification code by API');
//$I->amHttpAuthenticated('davert','123456');
$verify_code=$I->grabFromDatabase('verify_phone','verification_code', array('phone_no' => '+2348098305580','verified'=>'NO'));
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.verifyPhone">
  <user>
  <phone>+2348098305580</phone>    
  <verification_code>'.$verify_code.'</verification_code>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array("response_code" => 100, "description" => 'Phone number verified.'));

//step 3
$I = new ApiGuy($scenario);
$I->wantTo('Fill user details with email by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.create">
  <user>
  <firstname>Ladell</firstname>    
  <lastname>Martins</lastname>
  <password>123456</password>
  <phone>+2348098305580</phone>    
  <email>tizfreak2000@yahoo.com</email>
  <security_que_value></security_que_value>
  <birth_date>290400</birth_date>
  <location>kochi</location>    
  <pin_code>565656</pin_code>
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100'));
$id = $I->grabDataFromResponseByJsonPath('$..description[0].id');
//$auth_key = $I->grabDataFromResponseByJsonPath('$..description[0].auth_key');

$I->wantTo('Fill user details no email security question selected by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.create">
  <user>
  <firstname>Ladell</firstname>    
  <lastname>Martins</lastname>
  <password>123456</password>
  <phone>9865323238</phone>    
  <email></email>
  <security_que_value>test</security_que_value>
  <birth_date>290400</birth_date>
  <location>kochi</location>    
  <pin_code>565656</pin_code>
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100'));



$I->wantTo('Fill user details no email no security question by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.create">
  <user>
  <firstname>Ladell</firstname>    
  <lastname>Martins</lastname>
  <password>123456</password>
  <phone>9865323238</phone>    
  <email></email>
  <security_que_value></security_que_value>
  <birth_date>290400</birth_date>
  <location>kochi</location>    
  <pin_code>565656</pin_code>
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113'));


//step 4
$I->wantTo('Create user pin with valid input by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.createPin">
  <user>
  <id>21</id>    
  <pin>1234</pin>
  <confirm_pin>1234</confirm_pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100'));


$I->wantTo('Create user pin with invalid input by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.createPin">
  <user>
  <id>21</id>    
  <pin>abc1</pin>
  <confirm_pin>abc1</confirm_pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113'));


$I->wantTo('Create user pin with pin & confirm pin unmatched input by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.createPin">
  <user>
  <id>21</id>    
  <pin>1234</pin>
  <confirm_pin>4321</confirm_pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113'));