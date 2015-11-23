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
                        <select id="ddl1" name="allergy_type" class="stringInfoSpacing" onchange="configureDropDownLists(this,document.getElementById('ddl2'))">
				<option value="-1" selected="selected">Type</option>
				<option value="Environmental">Environmental</option>
				<option value="Food" >Food</option>
				</select>
				
			</div>
			<div class="form-group">
			Alergy:
                        <select id="ddl2" name="allergy" class="stringInfoSpacing">
				<option value="-1" selected="selected">Alergy</option>

				</select>
			</div>
			<!--<div class="form-group">
			Begin Date:
				<input type="text" name="begin_date" class="form-control" >
			</div>
                        <div class="form-group">
			End Date:
				<input type="text" name="end_date" class="form-control" >
			</div>-->
                        <div class="form-group">
			Reaction:
				<input type="text" name="reaction" class="form-control" >
			</div>	
			<div class="form-group">
			Severity:
				<input type="text" name="severity" class="form-control" >
			</div>
                        <div class="form-group">
			Location:
				<input type="text" name="location" class="form-control" >
			</div>	
                        <div class="form-group">
			STR:
				<input type="text" name="STR" class="form-control" >
			</div>	
			<div class="form-group">
			RXCUI:
				<input type="text" name="RXCUI" class="form-control" >
			</div>
                        <div class="form-group">
			CODE:
				<input type="text" name="CODE" class="form-control" >
			</div>  
                        <div class="form-group">
			ICDS:
				<input type="text" name="ICDS" class="form-control" >
			</div>	
                        <div class="form-group">
			Ocurrence:
				<input type="text" name="ocurrence" class="form-control" >
			</div>	
			<div class="form-group">
			Form:
				<input type="text" name="form" class="form-control" >
			</div>
                        <div class="form-group">
			Route:
				<input type="text" name="route" class="form-control" >
			</div>  
                        <div class="form-group">
			Code Text:
				<input type="text" name="code_text" class="form-control" >
			</div>	
                        <div class="form-group">
			Ocurrence:
				<input type="text" name="ap_ocurrence" class="form-control" >
			</div>	
			<div class="form-group">
			Outcome:
				<input type="text" name="outcome" class="form-control" >
			</div>
                        <div class="form-group">
			Referred By:
				<input type="text" name="referred_by" class="form-control" >
			</div>                        
			<div class="form-group">		
			<?php 
   /* echo $form->field($model, 'file')->widget(FileInput::classname(), [
        'options' => ['accept' => 'image/*'],
    ]);
		*/	?>
			<!--<div class="form-group">				
			Begin Date:	
                            <div class="input-group date" id="datetimepicker2">
                                    <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    <input type="text" class="form-control">
                            </div>
                        </div>

			
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