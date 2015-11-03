<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to login:</p>
<div class="login-pg">
                <div class="center-logo">
                    <img src="<?php echo \Yii::getAlias('@web') ?>/images/logo-center.png" alt="logo" class="img-responsive">
                </div>
                <h2>Login</h2>
                <div class="form-group">
                        <?php echo $response ?>
                    </div>
                <?php $form = ActiveForm::begin(['id' => 'form-login']); ?>
                
                    <div class="form-group">
                        <input type="text" name="phone" class="form-control phn-no" placeholder="Phone number">
                    </div>
                    
                    <div class="form-group">
                        <input type="password" name="pin" class="form-control pin-code" placeholder="Pin/secret code.">
                    </div>
                   
                    <?= Html::submitButton('Login', ['class' => 'btn btn-default', 'name' => 'login-button']) ?>
                

            <?php ActiveForm::end(); ?>
            </div>
</div>
