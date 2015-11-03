<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reg-pg1-first">
            
            
            <div class="reg-cnt">
                <h2>Sign Up</h2>
                <h4>Personal Details</h4>
                <?php echo $phone ?><br />
                <?php echo $response ?>
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
            
                
                    <div class="form-group">
                        <div class="input-group">
                          <div class="form-group">
                            <input type="text" class="form-control full-name bx" name="firstname" placeholder="First Name">
                        </div>

                        <div class="form-group">
                            <input type="text" class="form-control full-name bx" name="lastname" placeholder="Last Name">
                        </div>
                        
                        
                        <div class="form-group">
                            <input type="text" class="form-control email-adr bx" name="email" placeholder="Email Address">
                        </div>
                        
                        <div class="form-group">
                            <select class="form-control sel-ques" name="question">
                                <option value="">Select a security question</option>
                            </select>
                        </div>
                        <p> *  Please provide security question if you don’t have a registered email id</p>
                        <div class="clearfix"></div>
                        <div class="form-group">
                            <input type="text" class="form-control ans bx" name="answer" placeholder="Type your answer here">
                        </div>
                        
                        <div class="form-group">
                            <div class="col-xs-6 sm-no-pad-lt">
                                <input type="text" class="form-control age bx" name="age" placeholder="Age">
                            </div>
                            
                             <div class="col-xs-6 sm-no-pad-rt">
                                <input type="text" class="form-control gndr bx" name="sex" placeholder="Gender">
                            </div>    
                        </div>
                        <div class="clearfix"></div>
                        <span class="or">Or</span>
                        <h4>Enter Personal Details Using</h4>
                        <a href="#" class="fb-btn"><img src="images/facebook-icon.png" alt="facebook"></a>
                        <a href="#" class="twitter-btn"><img src="images/twitter-icon.png" alt="twitter"></a>
                        
                         
                       
                    <?= Html::submitButton('Next', ['class' => 'btn btn-default', 'name' => 'signup-button']) ?>
                   
                <?php ActiveForm::end(); ?>
            </div>  
        </div>

