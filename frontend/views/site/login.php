<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
    
    <div class="row">
        
        <div class="login-pg">
            <div class="center-logo">
                <img src="<?php echo \Yii::getAlias('@web') ?>/images/logo-center.png" alt="logo" class="img-responsive">
            </div>
            <h2>Login</h2>
            
            <?php if($success_flag == 1){?>
          <div class="alert alert-success">            
            <strong><?php echo $resp;?></strong>
            <a href="#" class="close close1" data-dismiss="" aria-label="close"><i class="glyphicon glyphicon-alert"></i></a>
          </div>
         <?php }else if($success_flag == 2) {?>
           <div class="alert alert-danger">            
            <strong><?php echo $resp;?></strong>
            <a href="#" class="close close1" data-dismiss="" aria-label="close"><i class="glyphicon glyphicon-alert"></i></a>
          </div>         
         <?php } ?>
            <?php $form = ActiveForm::begin(['id' => 'form-login','options' => [
            'class' => 'lgn-frm'
            ]]); ?>
            <?= $form->errorSummary($model); ?>

                
            <div class="form-group">
                <div class="drp-bx">
                    <select name="code" class="sel-fld">
                        <option>+234</option>
                        
                    </select>
                </div>
                <input type="text" class="form-control phn-no" name="phone" placeholder="Phone number">
            </div>
            
            
            
            <div class="form-group">
                <input type="password" maxlength="4" name="pin" class="form-control pin-code" placeholder="Pin/secret code.">
            </div>
            <?= $form->field($model, 'terms')->checkbox()->label('I agree to the <a href="http://phoneadoctor.com.ng/terms.html" target="_blank">Terms and conditions</a>') ?>
            <?= Html::submitButton('Login', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
            <p>Can't remember your pin? <a href="<?php echo Url::toRoute('/site/recover') ?>">Click here</a></p>
            <p>Don't have an account? <a href="<?php echo Url::toRoute('/site/signup') ?>">Sign up here</a></p>
            <?php ActiveForm::end(); ?>
            
        </div>
    </div>
</div>