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
                <h4>Enter the verification code</h4>
                <?php echo $phone ?><br />
                <?php echo $response ?>
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
            
                
                    <div class="form-group">
                        <div class="input-group">
                          <div class="form-group">
                            <input class="form-control verfied" type="password" name="verify" placeholder="verfied">
                        </div>
                        <p> *  please enter the 6 digit verification code received on your mobile phone. <a href="#">Resend Verification</a></p>
                    <?= Html::submitButton('Next', ['class' => 'btn btn-default', 'name' => 'signup-button']) ?>
                   
                <?php ActiveForm::end(); ?>
            </div>  
        </div>

