<?php
use tests\codeception\frontend\FunctionalTester;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);

$I->wantTo('Submit a valid phone no for verification by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.sendCode">
  <user>
  <phone>+2348098305580</phone> 
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Verification sent to phone."));


$I->wantTo('Submit an invalid phone no for verification by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.sendCode">
  <user>
  <phone>+234doctor</phone> 
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Invalid phone no."));


$I->wantTo('Submit an existing phone no for verification by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.sendCode">
  <user>
  <phone>+2348098305580</phone> 
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '113',"description"=>"Phone no.already exists"));