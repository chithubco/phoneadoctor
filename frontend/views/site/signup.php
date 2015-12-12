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
    <h2>Sign Up</h2>
    
    <div class="sigup-step2-bx">
      <div class="center-logo">
        <img src="<?php echo \Yii::getAlias('@web') ?>/images/logo-center.png" alt="logo" class="img-responsive">
      </div>
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
      <h4>Enter phone Number</h4>
      <?php $form = ActiveForm::begin(['id' => 'form-signup','options' => [
      'class' => 'reg2-frm'
      ]]); ?>
      <?php //echo $response ?>
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
      
      <p>Already have an account? <a href="<?php echo Url::toRoute('/site/login') ?>">Click here</a></p>
      <p>Can't remember your pin? <a href="<?php echo Url::toRoute('/site/recover') ?>">Click here</a></p>
      <?= Html::submitButton('Next', ['class' => 'btn btn-default', 'name' => 'signup-button']) ?>
      
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>