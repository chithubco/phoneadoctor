<?php
use tests\codeception\frontend\FunctionalTester;


/* @var $scenario Codeception\Scenario */

$I = new ApiGuy($scenario);
$I->wantTo('Fill user details with email by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.create">
  <user>
  <firstname>Ladell</firstname>    
  <lastname>Martins</lastname>
  <password>123456</password>
  <phone>9865323238</phone>    
  <email>tizfreak2000@yahoo.com</email>
  <security_que_value></security_que_value>
  <birth_date>290400</birth_date>
  <location>kochi</location>    
  <pin_code>565656</pin_code>
  </user>
</request>');
$I->seeResponseCodeIs(200);
$I->seeResponseIsJson();
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Details successfully added."));


$I->wantTo('Fill user details no email security question selected by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.create">
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
$I->seeResponseContainsJson(array('response_code' => '100',"description"=>"Details successfully added."));



$I->wantTo('Fill user details no email no security question by API');
//$I->amHttpAuthenticated('davert','123456');
$I->haveHttpHeader('Content-Type','application/x-www-form-urlencoded');
$I->sendPOST('/index.php/user/api', '<request method="user.create">
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
$I->seeResponseContainsJson(array('response_code' => '113',"description"=>" Security question cannot be blank."));
