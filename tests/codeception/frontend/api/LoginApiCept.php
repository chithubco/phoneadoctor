<?php
use tests\codeception\frontend\ApiGuy;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);



$I->wantTo('login with valid details by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.login">
  <user>
  <phone>+23408026459452</phone>    
  <pin>123456</pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => 100));


$I->wantTo('login with no data by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.login">
  <user>
  <phone></phone>    
  <pin></pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array("response_code" => 113));


$I->wantTo('login with registered phone_no and blank Pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.login">
  <user>
  <phone>+23408026459452</phone>    
  <pin></pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array("response_code" => 113));


$I->wantTo('login with registered phone_no and invalid Pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.login">
  <user>
  <phone>+23408026459452</phone>    
  <pin>4321</pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array("response_code" => 113));


$I->wantTo('login with registered phone_no and invalid Pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.login">
  <user>
  <phone>+23408026459452</phone>    
  <pin>@!#1</pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array("response_code" => 113));


$I->wantTo('login with registered phone_no and invalid Pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.login">
  <user>
  <phone>+23408026459452</phone>    
  <pin>abc1</pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array("response_code" => 113));


$I->wantTo('login with unregistered phone_no and invalid Pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.login">
  <user>
  <phone>+2348058305580</phone>    
  <pin>@!#1</pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array("response_code" => 113));