<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Change Pin';
?>
<!-- --------------MAIN CONTAINER------------------------- -->

<div class="col-sm-8 main-container consult-pg">
	<!--toggle sidebar button-->
	<p class="visible-xs">
		<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
	</p>
	
	<h2>Change Pin</h2>
	<div class="cnt-blk">
	<h4><?php echo $error ?></h4>
		<?php $form = ActiveForm::begin(['id' => 'form-consult']); ?>
			<div class="form-group">
                    	<label>Enter old pin</label>
                    	<input type="password" name="pin" class="form-control" placeholder="">
                    </div>
                    
                    <div class="form-group">
                    	<label>Enter new pin</label>
                    	<input type="password" name="newpin" class="form-control" placeholder="">
                    </div>
                    
                    <div class="form-group">
                    	<label>Confirm new pin</label>
                    	<input type="password" name="newpin2" class="form-control" placeholder="">
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
		<?= Html::submitButton('Submit your question', ['class' => 'btn btn-default', 'name' => 'signup-button']) ?>
                   
                <?php ActiveForm::end(); ?>
	</div>
	<!----------------MAIN CONTAINER--------------------------->