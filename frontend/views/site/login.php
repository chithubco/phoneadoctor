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
                    <img src="http://localhost/phoneadoc/Login_files/logo-center.png" alt="logo" class="img-responsive">
                </div>
                <h2>Login</h2>
                
                <form class="lgn-frm">
                    <div class="form-group">
                        <input type="text" class="form-control phn-no" placeholder="Phone number">
                    </div>
                    
                    <div class="form-group">
                        <input type="password" class="form-control pin-code" placeholder="Pin/secret code.">
                    </div>
                    <input type="submit" class="btn btn-default" value="Sign in">
                </form>
            </div>
</div>
