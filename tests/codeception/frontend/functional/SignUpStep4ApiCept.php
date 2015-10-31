<?php
use tests\codeception\frontend\FunctionalTester;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);

$I->wantTo('Create user pin with valid input by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.createPin">
  <user>
  <id>21</id>    
  <pin>1234</pin>
  <confirm_pin>1234</confirm_pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Pin created successfully."));


$I->wantTo('Create user pin with invalid input by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.createPin">
  <user>
  <id>21</id>    
  <pin>abc1</pin>
  <confirm_pin>abc1</confirm_pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Pin can only be in figures."));


$I->wantTo('Create user pin with pin & confirm pin unmatched input by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.createPin">
  <user>
  <id>21</id>    
  <pin>1234</pin>
  <confirm_pin>4321</confirm_pin>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Pin does not match."));