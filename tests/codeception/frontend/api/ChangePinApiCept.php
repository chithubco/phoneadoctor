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

$id = $I->grabDataFromResponseByJsonPath('$..description[0].id');
$auth_key = $I->grabDataFromResponseByJsonPath('$..description[0].auth_key');

$I->wantTo('change user pin with valid details by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.changePin">
  <user>
  <old_pin>123456</old_pin>    
  <new_pin>654321</new_pin>    
  <confirm_pin>654321</confirm_pin>   
  <auth_key>'.$auth_key.'</auth_key>
  <user_id>'.$id.'</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100'));


$I->wantTo('change user pin with invalid confirm pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.changePin">
  <user>
  <old_pin>654321</old_pin>    
  <new_pin>654321</new_pin>    
  <confirm_pin>1234</confirm_pin>   
  <auth_key>'.$auth_key.'</auth_key>
  <user_id>'.$id.'</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113'));


$I->wantTo('change user pin with blank old pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.changePin">
  <user>
  <old_pin></old_pin>    
  <new_pin>654321</new_pin>    
  <confirm_pin>1234</confirm_pin>   
  <auth_key>'.$auth_key.'</auth_key>
  <user_id>'.$id.'</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113'));


$I->wantTo('change user pin with invalid old pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.changePin">
  <user>
  <old_pin>111111</old_pin>    
  <new_pin>654321</new_pin>    
  <confirm_pin>654321</confirm_pin>   
  <auth_key>'.$auth_key.'</auth_key>
  <user_id>'.$id.'</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113'));


$I->wantTo('change user pin with same details as old pin and new pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.changePin">
  <user>
  <old_pin>654321</old_pin>    
  <new_pin>654321</new_pin>    
  <confirm_pin>654321</confirm_pin>   
  <auth_key>'.$auth_key.'</auth_key>
  <user_id>'.$id.'</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113'));


$I->wantTo('change user pin with invalid new pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('user/api', '<request method="user.changePin">
 <user>
  <old_pin>654321</old_pin>    
  <new_pin>wweyt</new_pin>    
  <confirm_pin>wweyt</confirm_pin>   
  <auth_key>'.$auth_key.'</auth_key>
  <user_id>'.$id.'</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113'));