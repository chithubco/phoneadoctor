<?php
use tests\codeception\frontend\FunctionalTester;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);

$I->wantTo('Verify a phone with a valid verification code by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.verifyPhone">
  <user>
  <phone>+2348098305580</phone>    
  <verification_code>946533</verification_code>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Phone verification successful."));


$I->wantTo('Verify a phone with a invalid verification code by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.verifyPhone">
  <user>
  <phone>+2348098305580</phone>    
  <verification_code>533456</verification_code>    
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Phone verification failed."));