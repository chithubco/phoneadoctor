<?php
use tests\codeception\frontend\FunctionalTester;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);

$I->wantTo('make a consultation by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/consultation/api', '<request method="consultation.create">
  <consultation>
  <note>Vidhya</note>    
  <user_id>1</user_id>  
  </consultation>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Consultation successfully created."));