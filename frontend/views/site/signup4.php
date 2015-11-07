<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container signup-step4">
    <div class="row">
        <h2>Sign up</h2>
        <div class="login-pg">
            <div class="center-logo">
                <img src="<?php echo \Yii::getAlias('@web') ?>/images/logo-center.png" alt="logo" class="img-responsive">
            </div>
            
            <?php $form = ActiveForm::begin(['id' => 'form-signup','options' => [
            'class' => 'lgn-frm'
            ]]); ?>
            <h4>Enter Pin Code</h4>
            <div class="form-group">
                <input type="password" class="form-control pin-code" placeholder="Enter your desired pin code" name="pin">
                
            </div>
            
            <div class="form-group">
                <input type="password" class="form-control pin-code" placeholder="Confirm Pin" name="confirm_pin">
            </div>
            <?= Html::submitButton('Next', ['class' => 'btn btn-default', 'name' => 'signup-button']) ?>
            
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>