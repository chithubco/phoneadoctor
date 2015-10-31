<?php

namespace tests\codeception\frontend\functional;

use tests\codeception\frontend\_pages\SignupPage;
use common\models\User;

class SignupCest
{

   
   public $error_class = ".help-block";
   public $message_class = ".help-block";
   public $step1_text = "step 1";
   public $step2_text = "step 2";
   public $step3_text = "step 3";
   public $step4_text = "step 4";

   public $step1_url = Yii::$app->homeUrl."/signup";
   public $step2_url = Yii::$app->homeUrl."/step2";
   public $step3_url = Yii::$app->homeUrl."/step3";
   public $step4_url = Yii::$app->homeUrl."/step4";
   

    /**
     * This method is called before each cest class test method
     * @param \Codeception\Event\TestEvent $event
     */
    public function _before($event)
    {
    }

    /**
     * This method is called after each cest class test method, even if test failed.
     * @param \Codeception\Event\TestEvent $event
     */
    public function _after($event)
    {
        User::deleteAll([
            'email' => 'tester.email@example.com',
            'username' => 'tester',
        ]);
    }

    /**
     * This method is called when test fails.
     * @param \Codeception\Event\FailEvent $event
     */
    public function _fail($event)
    {

    }

    public function register_step1($signupPage,$phone, $code='+234'){
        $signupPage->submit([
            'phone' => $phone,
            'code'=>$code
        ]);

        return true;

    }

    public function check_step1($model,$phone){
            $details = $model::findOne("phone = '"+$phone+"'");
            return $details->verify_code;

    }

    public function register_step2($signupPage,$verify_code){
        $signupPage->submit([
            'verify_code'=>$verify_code
        ]);

        return true;

    }

    public function register_step3($signupPage,$details){
        $signupPage->submit($details);

        return true;

    }

    public function register_step4($signupPage,$pin_code,$verify_pin){
        $signupPage->submit([
            'pin_code' => $pin_code,
            'verify_pin' => $verify_pin
            ]);

        return true;

    }


    /**
     *
     * @param \codeception_frontend\FunctionalTester $I
     * @param \Codeception\Scenario $scenario
     */
    public function testUserSignup($I, $scenario)
    {
        $I->wantTo('ensure that signup works');

        $signupPage = SignupPage::openBy($I);
        //$I->see('Signup', 'h1');
        //$I->see('Please fill out the following fields to signup:');
        //Test #1
        $I->amGoingTo('submit signup step 1 form with no data (case )');

        $signupPage->submit([]);

        $I->expectTo('see validation errors');
        $I->see('Phone number cannot be blank.', $this->error_class);

        //Test #2
        $I->amGoingTo('submit signup step 1 form with invalid phone number (case 3');

        $this->register_step1($signupPage,'12345678901');

        $I->expectTo('see validation errors');
        $I->see('Phone number is invalid.', $this->error_class);

        //Test #3
        $this->register_step1($signupPage,'05098305580');

        $I->expectTo('see validation errors');
        $I->see('Phone number is invalid.', $this->error_class);
       
        //Test #4
        $this->register_step1($signupPage,'0809830558');

        $I->expectTo('see validation errors');
        $I->see('Phone number is invalid.', $this->error_class);

        //Test #5
        $this->register_step1($signupPage,'0809830t580');

        $I->expectTo('see validation errors');
        $I->see('Phone number is invalid.', $this->error_class);
       
        //Test #6
        $this->register_step1($signupPage,'Anthony');

        $I->expectTo('see validation errors');
        $I->see('Phone number is invalid.', $this->error_class);

        //Test #7
        $this->register_step1($signupPage,"'~#!%@^&*()");

        $I->expectTo('see validation errors');
        $I->see('Phone number is invalid.', $this->error_class); 

        //Test #8
        $I->amGoingTo('Start signup at step 3 (case 9)');
        $I->amOnPage($this->step3_url);
        $I->expectTo('see signup step 1');
        $I->see($this->step1_text, $this->message_class); 


        //Test #9
        $I->amGoingTo('Start signup at step 4 (case 10)');
        $I->amOnPage($this->step4_url);
        $I->expectTo('see signup step 1');
        $I->see($this->step1_text, $this->message_class);

        //Test #10
        $I->amGoingTo('Sign up with valid Phone number (case 1, step 1)');
        $this->register_step1($signupPage,"08098305580");

        $I->expectTo('see signup step 2');
        $I->see($this->step2_text, $this->message_class); 
        $I->seeInDatabase('verifyPhone', ['phone' => "08098305580"]);
        $model = new verifyPhone;
        $verify_code=$this->check_step1($model,"08098305580");

		//Test #11
        $I->amGoingTo ('Submit Signup step3 with no data (case 1, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'',
                                           'lastname'=>'',
                                           'DOB'=>'']);
                
        $I->expectTo('see validation errors');
        $I->see('firstname cannot be blank.', $this->error_class);


        //Test #12
        $I->amGoingTo ('Submit Signup step3 with no firstname (case 2, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('firstname cannot be blank.', $this->error_class);


        //Test #13
        $I->amGoingTo ('Submit Signup step3 with invalid firstname (case 3, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'08097656354',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('Invalid first name.', $this->error_class);


        //Test #14
        $I->amGoingTo ('Submit Signup step3 with invalid firstname (case 4, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'Anth98y',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('Invalid first name.', $this->error_class);


        //Test #15
        $I->amGoingTo ('Submit Signup step3 with invalid firstname (case 5, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'#@$^&*&^%$#',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('Invalid first name.', $this->error_class);


        //Test #16
        $I->amGoingTo ('Submit Signup step3 with no lastname (case 6, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('lastname cannot be blank.', $this->error_class);



         //Test #17
        $I->amGoingTo ('Submit Signup step3 with invalid lastname (case 7, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'08097656354',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('Invalid last name.', $this->error_class);



         //Test #18
        $I->amGoingTo ('Submit Signup step3 with invalid lastname (case 8, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'Anth98y',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('Invalid last name.', $this->error_class);


        //Test #19
        $I->amGoingTo ('Submit Signup step3 with invalid lastname (case 9, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'#@$^&*&^%$#',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('Invalid last name.', $this->error_class);



        //Test #20
        $I->amGoingTo ('Submit Signup step3 with no DOB (case 10, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'Martins',
                                           'DOB'=>'']);
                
        $I->expectTo('see validation errors');
        $I->see('DOB cannot be blank.', $this->error_class);



        //Test #21
        $I->amGoingTo ('Submit Signup step3 with valid details with invalid email (case 11, step 3)');
        $this->register_step3($signupPage,['email'=>'aeyariegmail.com',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('Please enter a valid email', $this->error_class);


        //Test #22
        $I->amGoingTo ('Submit Signup step3 with valid details with invalid email (case 12, step 3)');
        $this->register_step3($signupPage,['email'=>'aeyarie@gmailcom',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('Please enter a valid email', $this->error_class);


        //Test #23
        $I->amGoingTo ('Submit Signup step3 with valid details with invalid email (case 13, step 3)');
        $this->register_step3($signupPage,['email'=>'aeyarie@123456',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('Please enter a valid email', $this->error_class);


        //Test #24
        $I->amGoingTo ('Submit Signup step3 with valid details with invalid email (case 14, step 3)');
        $this->register_step3($signupPage,['email'=>'aeyarieg!@#&^com',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00']);
                
        $I->expectTo('see validation errors');
        $I->see('Please enter a valid email', $this->error_class);



        //Test #25
        $I->amGoingTo ('Submit Signup step3 with valid details without email, without selecting security question (case 15, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00'
                                           'Security_question'=>''
                                           'Security_answer'=>'']);
                
        $I->expectTo('see validation errors');
        $I->see('Please select a security question', $this->error_class);


        //Test #26
        $I->amGoingTo ('Submit Signup step3 with valid details without email and select a security question without an answer (case 16, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00'
                                           'Security_question'=>'My pets name'
                                           'Security_answer'=>'']);
                
        $I->expectTo('see validation errors');
        $I->see('Please provide an answer to the question', $this->error_class);


        //Test #27
        $I->amGoingTo ('Submit Signup step3 with valid details without email and select a security question and fill an answer  (case 17, step 3)');
        $this->register_step3($signupPage,['email'=>'',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00'
                                           'Security_question'=>'My pets name'
                                           'Security_answer'=>'Bingo']);
                
        $I->expectTo('see signup step 4');
        $I->see($this->step4_text, $this->message_class);


        //Test #28
		$I->amGoingTo ('Submit Signup step3 with valid details and valid email (case 18, step 3)');
		$this->register_step3($signupPage,['email'=>'tizfreak2000@yahoo.com',
                                           'firstname'=>'Ladell',
                                           'lastname'=>'Martins',
                                           'DOB'=>'29/04/00']);
				
		$I->expectTo('see signup step 4');
		$I->see($this->step4_text, $this->message_class);
		

        //Test #29
        $I->amGoingTo ('Submit Signup step4 with no data (case 19, step 4)');
        $this->register_step4($signupPage,['securepin'=>'',
                                           'confirmpin'=>'']);
                
        $I->expectTo('see validation errors');
        $I->see('Please input your 4-digit secure pin', $this->error_class);



		//Test #30
        $I->amGoingTo ('Submit Signup step4 with pin more than 4 digits (case 20, step 4)');
        $this->register_step4($signupPage,['securepin'=>'54678',
                                           'confirmpin'=>'']);
                
        $I->expectTo('only be able to type 4 digits');
        $I->see('cursor off after 4 digits');


        //Test #31
        $I->amGoingTo ('Submit Signup step4 with pin combination of digits and alphabets (case 21, step 4)');
        $this->register_step4($signupPage,['securepin'=>'5tt8',
                                           'confirmpin'=>'']);
                
        $I->expectTo('only be able to type 4 digits');
        $I->see('nothing when i type anything not a digit');


        //Test #32
        $I->amGoingTo ('Submit Signup step4 with pin combination of characters (case 22, step 4)');
        $this->register_step4($signupPage,['securepin'=>'@#%',
                                           'confirmpin'=>'']);
                
        $I->expectTo('only be able to type 4 digits');
        $I->see('nothing when i type anything not a digit');


        //Test #33
        $I->amGoingTo ('Submit Signup step4 with pin 4 digits and without confirmpin (case 23, step 4)');
        $this->register_step4($signupPage,['securepin'=>'1234',
                                           'confirmpin'=>'']);
                
        $I->expectTo('see validation errors');
        $I->see('Confirm pin cannot be blank', $this->error_class);


        //Test #34
        $I->amGoingTo ('Submit Signup step4 with pin 4 digits and invalid confirmpin (case 24, step 4)');
        $this->register_step4($signupPage,['securepin'=>'1234',
                                           'confirmpin'=>'4321']);
                
        $I->expectTo('see validation errors');
        $I->see('Pins do not match', $this->error_class);


        //Test #35
        $I->amGoingTo ('Submit Signup step4 with pin 4 digits and valid confirmpin (case 25, step 4)');
        $this->register_step4($signupPage,['securepin'=>'1234',
                                           'confirmpin'=>'1234']);
                
        $I->expectTo('see that user is created');
        $I->see('');



        //$I->amGoingTo('Sign up with valid details and email');
        $signupPage->submit([
            'username' => 'tester',
            'email' => 'tester.email@example.com',
            'password' => 'tester_password',
        ]);
        $I->expectTo('see that user is created');
        $I->seeRecord('common\models\User', [
            'username' => 'tester',
            'email' => 'tester.email@example.com',
        ]);
		
        $I->expectTo('see that user logged in');
        $I->seeLink('Logout (tester)');
    }
}
