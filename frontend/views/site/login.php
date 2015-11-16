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
            <div class="form-group">
                <?php echo $response ?>
            </div>
            <?php $form = ActiveForm::begin(['id' => 'form-login','options' => [
            'class' => 'lgn-frm'
            ]]); ?>
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
            
            <?= Html::submitButton('Login', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
            <p>Can't remember your pin? <a href="<?php echo Url::toRoute('/site/recover') ?>">Click here</a></p>
            <p>Don't have an account? <a href="<?php echo Url::toRoute('/site/signup') ?>">Sign up here</a></p>
            <?php ActiveForm::end(); ?>
            
        </div>
    </div>
</div>