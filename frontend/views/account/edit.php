<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
$this->title = 'Update Account Details';
?>
<!----------------MAIN CONTAINER--------------------------->
<div class="col-sm-8 main-container consult-pg">
	<!--toggle sidebar button-->
	<p class="visible-xs">
		<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
	</p>
	
	<h2>Update Account</h2>
	<div class="cnt-blk">
		<?php $form = ActiveForm::begin(['id' => 'form-consult','options' => ['enctype' => 'multipart/form-data']]); ?>
			
			<div class="form-group">
			First Name:
				<input type="text" name="fname" class="form-control" value="<?php echo $data->description->fname ?>">
			</div>
			<div class="form-group">
			Last Name:
				<input type="text" name="lname" class="form-control" value="<?php echo $data->description->lname ?>">
			</div>
                        <div class="form-group">
			Skype Id:
				<input type="text" name="skypeid" class="form-control" value="<?php echo $data->description->skypeid ?>">
			</div>            
			<div class="form-group">
			Sex:
				<input type="text" name="sex" class="form-control" value="<?php echo $data->description->sex ?>">
			</div>
			<div class="form-group">
				<textarea class="form-control txt-area" name="address" placeholder="Type your address here"><?php echo $data->description->address ?></textarea>
			</div>
			<div class="form-group">
			City:
				<input type="text" name="city" class="form-control" value="<?php echo $data->description->city ?>">
			</div>
			<div class="form-group">
			State:
				<input type="text" name="state" class="form-control" value="<?php echo $data->description->state ?>">
			</div>
			<?php
			

   
    echo $form->field($model, 'file')->widget(FileInput::classname(), [
        'options' => ['accept' => 'image/*'],
    ]);
			?>
			<div class="form-group">
			Pin:
				<input type="password" name="pin" class="form-control">
			</div>
			<!--
			<div class="form-group">
				<p>Scheduled for  a consultation</p>
				<div class="col-sm-6 sm-no-pad-lt">
					<div class="input-group date" id="datetimepicker2">
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
						<input type="text" class="form-control">
					</div>
				</div>
				<div class="col-sm-6 sm-no-pad-rt">
					<div class="input-group date" id="datetimepicker3">
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-time"></span>
						</span>
						<input type="text" class="form-control">
					</div>
				</div>
			</div>
			
			<!--
			<div class="chrg-opt">
				<p>Select available charging options</p>
				<ul>
					<li class="col-xs-4 col-lt-12">
						<a href="#"><span><img src="<?php echo \Yii::getAlias('@web') ?>/images/free-minutes.png" alt="free minutes"></span> Free Minutes</a>
					</li>
					
					<li class="col-xs-4 col-lt-12">
						<a href="#"><span class="payg"><img src="<?php echo \Yii::getAlias('@web') ?>/images/payus.png" alt="payg"></span> PAYG</a>
					</li>
					
					<li class="col-xs-4 col-lt-12">
						<a href="#"><span><img src="<?php echo \Yii::getAlias('@web') ?>/images/premium.png" alt="Active subscription icon"></span>Active Subscription</a>
					</li>
					
				</ul>
			</div>
			-->
		<?= Html::submitButton('Update Account', ['class' => 'btn btn-default', 'name' => 'signup-button']) ?>
                   
                <?php ActiveForm::end(); ?>
	</div>
	<!----------------MAIN CONTAINER--------------------------->