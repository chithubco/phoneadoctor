<?php
use tests\codeception\frontend\FunctionalTester;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);

$I->wantTo('make a donation through interswitch by API');
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
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Donation successfully created."));


$I->wantTo('make a donation through paypal by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/donation/api', '<request method="donation.create">
  <donation>
  <amount>2000</amount> 
  <gateway>paypal</gateway>
  <name>Segun</name>
  <email>xtreem88@gmail.com</email>  
  </donation>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Donation successfully created."));