<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
 <div class="container">
        <div class="row">
            <div class="reg-pg2 signup-step3">
                <h2>Sign up</h2>
                <div class="reg2-cnt step3-cnt">
                    <span class="logo-center"><img src="<?php echo \Yii::getAlias('@web') ?>/images/logo-center.png" alt="logo"></span>
                    <?php $form = ActiveForm::begin(['id' => 'form-signup','options' => [
            'class' => 'reg2-frm'
            ]]); ?>                   
                        <h4>Personal Details</h4> 
                        <div class="form-group">
                            <input type="text" class="form-control phone bx" readonly="true" value="<?php echo $phone ?>">
                        </div> 
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
                                <?php
                                    if($question){
                                        foreach($question as $key=>$val){
                                ?>
                                <option value="<?php echo $key ?>"><?php echo $val ?></option>
                                <?php
                                        }
                                    }
                                ?>
                            </select>
                        </div>
                        <p> *  Please provide security question if you don’t have a registered email id</p>
                        <div class="clearfix"></div>
                        <div class="form-group">
                            <input type="text" class="form-control ans bx" name="answer" placeholder="Type your answer here">
                        </div>
                        
                        <div class="form-group">
                            <div class="col-xs-6 sm-no-pad-lt">
                                <div class="input-group date" id="datetimepicker2">                    
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    <input type="text" name="age" class="form-control">
                                </div>
                            </div>
                            
                             <div class="col-xs-6 sm-no-pad-rt">
                                <select class="form-control gndr bx" name="sex">
                                    <option>Gender</option>
                                    <option>Male</option>
                                    <option>Female</option>
                                </select>                                
                            </div>    
                        </div>
                        <div class="clearfix"></div>
                        <span class="or">Or</span>
                        <h4>Enter Personal Details Using</h4>
                        <a href="#" class="fb-btn"><img src="<?php echo \Yii::getAlias('@web') ?>/images/facebook-icon.png" alt="facebook"></a>
                        <a href="#" class="twitter-btn"><img src="<?php echo \Yii::getAlias('@web') ?>/images/twitter-icon.png" alt="twitter"></a> 
                        <p>Already have an account? <a href="<?php echo Url::toRoute('/site/login') ?>">Click here</a></p>
      <p>Can't remember your pin? <a href="<?php echo Url::toRoute('/site/recover') ?>">Click here</a></p>
                         <?= Html::submitButton('Next', ['class' => 'btn btn-default', 'name' => 'signup-button']) ?>
            
            <?php ActiveForm::end(); ?>
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>
<script src="<?php echo \Yii::getAlias('@web') ?>/js/jquery.min.js"></script>
<script src="<?php echo \Yii::getAlias('@web') ?>/js/moment-with-locales.js"></script>
<script src="<?php echo \Yii::getAlias('@web') ?>/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript">
var $j = jQuery.noConflict();
$j(function () {
$j('#datetimepicker2').datetimepicker({
format: 'YYYY-MM-DD'
});
});
</script>