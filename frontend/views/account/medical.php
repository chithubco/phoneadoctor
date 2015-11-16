<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
$this->title = 'Add Medical Details';
?>
<script type="text/javascript">
     function configureDropDownLists(ddl1,ddl2) {
     
    var Environmental = new Array('Dust', 'Pollen', 'Dust Mites','Animal Dander','Insect Sting','Nikel','Mould','Latex');
    var Food = new Array('Dairy', 'Egg', 'Peanut','Other Nuts','Seafood','Shellfish','Soy','Wheat','Melons, bananas, cucumbers (ragweed pollen)','Baker\'s Yeast');
    //var names = new Array('John', 'David', 'Sarah');

    switch (ddl1.value) {
        case 'Environmental':
            ddl2.options.length = 0;
            for (i = 0; i < Environmental.length; i++) {
                createOption(ddl2, Environmental[i], Environmental[i]);
            }
            break;
        case 'Food':
            ddl2.options.length = 0; 
        for (i = 0; i < Food.length; i++) {
            createOption(ddl2, Food[i], Food[i]);
            }
            break;
        case 'Names':
            ddl2.options.length = 0;
            for (i = 0; i < names.length; i++) {
                createOption(ddl2, names[i], names[i]);
            }
            break;
            default:
                ddl2.options.length = 0;
            break;
    }

}

    function createOption(ddl, text, value) {
        var opt = document.createElement('option');
        opt.value = value;
        opt.text = text;
        ddl.options.add(opt);
    }
</script>
<!----------------MAIN CONTAINER--------------------------->
<div class="col-sm-8 main-container consult-pg">
	<!--toggle sidebar button-->
	<p class="visible-xs">
		<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
	</p>
	
	<h2>Add Medicals</h2>
	<div class="cnt-blk">
		<?php $form = ActiveForm::begin(['id' => 'form-consult','options' => ['enctype' => 'multipart/form-data']]); ?>
			Alergies
			<div class="form-group">
			
			Type:
				<select id="ddl1" class="stringInfoSpacing" onchange="configureDropDownLists(this,document.getElementById('ddl2'))">
				<option value="-1" selected="selected">Type</option>
				<option value="Environmental">Environmental</option>
				<option value="Food" >Food</option>
				</select>
				
			</div>
			<div class="form-group">
			Alergy:
			<select id="ddl2" class="stringInfoSpacing">
				<option value="-1" selected="selected">Alergy</option>

				</select>
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