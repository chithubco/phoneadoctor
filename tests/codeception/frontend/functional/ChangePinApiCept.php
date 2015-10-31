<?php
use tests\codeception\frontend\FunctionalTester;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);

$I->wantTo('change user pin with valid details by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.changePin">
  <user>
  <old_pin>1234</old_pin>    
  <new_pin>4321</new_pin>    
  <confirm_pin>4321</confirm_pin>   
  <auth_key>6301</auth_key>
  <user_id>2</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Pin changed successfully."));


$I->wantTo('change user pin with invalid confirm pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.changePin">
  <user>
  <old_pin>1234</old_pin>    
  <new_pin>4321</new_pin>    
  <confirm_pin>4213</confirm_pin>
  <auth_key>6301</auth_key>    
  <user_id>16</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Pin change not successfull."));


$I->wantTo('change user pin with blank old pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.changePin">
  <user>
  <old_pin> </old_pin>    
  <new_pin>4321</new_pin>    
  <confirm_pin>4213</confirm_pin>
  <auth_key>6301</auth_key>    
  <user_id>16</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Pin change not successfull."));


$I->wantTo('change user pin with invalid old pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.changePin">
  <user>
  <old_pin>8544</old_pin>    
  <new_pin>4321</new_pin>    
  <confirm_pin>4321</confirm_pin>  
  <auth_key>6301</auth_key>  
  <user_id>16</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Pin change not successfull."));


$I->wantTo('change user pin with same details as old pin and new pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.changePin">
  <user>
  <old_pin>1234</old_pin>    
  <new_pin>1234</new_pin>    
  <confirm_pin>1234</confirm_pin>
  <auth_key>6301</auth_key>    
  <user_id>16</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Pin change not successfull."));


$I->wantTo('change user pin with invalid new pin by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.changePin">
  <user>
  <old_pin>1234</old_pin>    
  <new_pin>4t2!</new_pin>    
  <confirm_pin>1234</confirm_pin>
  <auth_key>6301</auth_key>    
  <user_id>16</user_id>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Pin change not successfull."));