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
<div class="container sigup-step2">
    <div class="row">
        <h2>Sign up</h2>
        <div class="sigup-step2-bx">
            <div class="center-logo">
                <img src="<?php echo \Yii::getAlias('@web') ?>/images/logo-center.png" alt="logo" class="img-responsive">
            </div>
            
            <?php $form = ActiveForm::begin(['id' => 'form-signup','options' => [
            'class' => 'reg2-frm'
            ]]); ?>
            <?php echo $response ?>
            <div class="form-group">
                <input class="form-control verfied" type="password" name="verify" placeholder="verify">
            </div>
            <p> *  please enter the 6 digit verification code received on your mobile phone. <a href="#">Resend Verification</a></p>
            <p>Already have an account? <a href="<?php echo Url::toRoute('/site/login') ?>">Click here</a></p>
      <p>Can't remember your pin? <a href="<?php echo Url::toRoute('/site/recover') ?>">Click here</a></p>
            <?= Html::submitButton('Next', ['class' => 'btn btn-default', 'name' => 'signup-button']) ?>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>