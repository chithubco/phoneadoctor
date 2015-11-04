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
                <h4>Enter phone Number and Pin Code</h4>
                <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>
            <?php echo $response ?>
                
                    <div class="form-group">
                        <div class="input-group">
                          <div class="input-group-addon">
                           <select name="code" class="sel-bx">
                            <option>+234</option>
                           </select>
                        </div>
                         <input type="text" class="form-control" name="phone">      
                        </div>                      
                    </div>
                   <p>A six digit code will be sent to you mobile phone</p>
                   <p> please enter the the code for verification</p> 
                    <?= Html::submitButton('Next', ['class' => 'btn btn-default', 'name' => 'signup-button']) ?>
                   
                <?php ActiveForm::end(); ?>
            </div>  
        </div>

