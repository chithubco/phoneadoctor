<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container recover-pin">
        <div class="row">
            <div class="reg-pg2">
                <h2>Recover pin</h2>
                <div class="reg2-cnt">
                    <h4>Use Email as Recovery</h4>
                    <?php $form = ActiveForm::begin(['id' => 'form-login','options' => [
            'class' => 'reg2-frm'
            ]]); ?>
                    
                        <div class="form-group">
                            <input type="text" name="email" class="form-control email-adr bx" placeholder="Email Address">
                        </div>
                        <p> * A recovery link will be sent to your Email Adress. Navigate to the  link 
and clik it to recover your pin. <a href="#">Resend Verification</a></p>
                    
                    <?= Html::submitButton('Send Verification', ['class' => 'btn btn-default', 'name' => 'email-button']) ?>
            
                    <?php ActiveForm::end(); ?>    
                        <div class="clearfix"></div>
                        <span class="or">Or</span>
                        
                        <h4>Use Phone Number & Security Question as a Recovery </h4>
                    <?php $form = ActiveForm::begin(['id' => 'form-login','options' => [
            'class' => 'reg2-frm'
            ]]); ?>    
                        <div class="form-group enter-pin">
                            <label>Enter Phone Number and Security Question</label>
                             <input type="text" name="phone" class="form-control">
                        </div>
                        
                        <div class="form-group">
                            <select name="question" class="form-control sel-ques">
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
                        
                        <div class="form-group">
                            <input type="text" name="answer" class="form-control ans bx" placeholder="Type your answer here">
                        </div>
                        
                       <p> *Reset Pin will be sent to your registered phone number. You  can login
 with your phone number and reset pin <a href="#">.Resend Pin</a></p>
                        <div class="clearfix"></div>
                        
                        
                        <?= Html::submitButton('Send Verification', ['class' => 'btn btn-default', 'name' => 'phone-button']) ?>
            
                    <?php ActiveForm::end(); ?> 
                    <div class="clearfix"></div>
                </div>
            </div>
        </div>
    </div>