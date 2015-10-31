<?php
use tests\codeception\frontend\FunctionalTester;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);

$I->wantTo('make a payment by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/payment/api', '
<request method="payment.create">
  <payment>
  <id>1</id>
  <amount>2000</amount>
  </payment>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Payment successfully created."));