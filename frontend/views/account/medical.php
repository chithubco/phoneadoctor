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
        
           
              <div class="col-sm-8 main-container medical-history">          
          <!--toggle sidebar button-->
          <p class="visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
          </p> 
          
          <h2>Update , Edit or Delete your Medical history</h2>           
          <div class="updt-del">
             <ul class="nav nav-tabs">
              <li class="active"><a data-toggle="tab" href="#step1">Allergies</a></li>
              <li class=""><a data-toggle="tab" href="#step2">Medications</a></li>
              <li class=""><a data-toggle="tab" href="#step3">Active problems</a></li>
              <li class=""><a data-toggle="tab" href="#step4">Upload Files</a></li>
            </ul>
            
            <div class="tab-content">
              <div id="step1" class="tab-pane fade in active"> 
              	 <button class="btn-default add" data-toggle="modal" data-target="#myModal1">Add</button>
              	<ul>
                	<li>
                    	<div class="table-responsive">
                        	<table class="table table-hover table-striped">
                            	<thead>
                                	<tr>
                                    	<td>S.No</td>
                                    	<td>Type</td>
                                        <td>Location</td>
                                        <td>Allergy</td>
                                        <td>Reaction</td>
                                        <td>Begin Date</td>
                                        <td>End Date</td>
                                        <td>Action</td>
                                    </tr>
                                </thead>
                      <?php $i=1;
                      if(is_array($patient_allergies)){
                            foreach ($patient_allergies as $value) {                        
                          ?>  
                                <tr>
                                    <td><?=$i;?></td>
                                    <td><?=$value->allergy_type;?></td>
                                    <td><?=$value->location;?></td>
                                    <td><?=$value->allergy;?></td>
                                    <td><?=$value->reaction;?></td>
                                    <td><?=date('d-m-Y', strtotime($value->begin_date));?></td>
                                    <td><?=date('d-m-Y', strtotime($value->end_date));?></td>
                                    <td><a href="#" class="edit" data-toggle="modal" data-target="#myModal1"><i class="fa fa-edit"></i> </a>  <a href="#" class="delete"><i class="fa fa-trash"></i> </a></td>
                                </tr>
                        <?php $i++; } } else { ?>   
                                <tr><td colspan="9" style="text-align: center"><?=$patient_allergies;?></td></tr>
                         <?php } ?>
                            </table>
                        </div>
						
                    </li>                    
                </ul>               
              </div>
              
              <div id="step2" class="tab-pane fade">
              	<button class="btn-default add" data-toggle="modal" data-target="#myModal2">Add</button>	
             	 <ul>
                	<li>
                    	<div class="table-responsive">
                        	<table class="table table-hover table-striped">
                            	<thead>
                                	<tr>
                                    	<td>S.No</td>
                                        <td>Medication</td>
                                        <td>Dose</td>
                                        <td>Route</td>
                                        <td>Form</td>
                                       <!-- <td>Instructions</td>-->
                                        <td>Action</td>
                                    </tr>
                                </thead>  
                          <?php $i=1;
                          if(is_array($patient_medications)){
                            foreach ($patient_medications as $value) {                        
                          ?>  
                                <tr>
                                    <td><?=$i;?></td>
                                    <td><?=$value->STR;?></td>
                                    <td><?=$value->dose;?></td>
                                    <td><?=$value->route;?></td>
                                    <td><?=$value->form;?></td>
                                    <td><a href="#" class="edit" data-toggle="modal" data-target="#myModal1"><i class="fa fa-edit"></i> </a>  <a href="#" class="delete"><i class="fa fa-trash"></i> </a></td>
                                </tr>
                         <?php $i++; } } else { ?>   
                                <tr><td colspan="9" style="text-align: center"><?=$patient_medications;?></td></tr>
                         <?php } ?> 
                                  
                            </table>
                        </div> 
                      </li>                   	
                </ul>
              </div>
              
              <div id="step3" class="tab-pane fade"> 
              	<button class="btn-default add" data-toggle="modal" data-target="#myModal3">Add</button>             
                 <ul>
                	<li>
                    	<div class="table-responsive">
                        	<table class="table table-hover table-striped">
                            	<thead>
                                	<tr>
                                    	<td>S.No</td>
                                        <td>Search</td>
                                        <td>Problem</td>
                                        <td>Occurance</td>
                                        <td>Outcome</td>
                                        <td>Referred by</td>
                                        <td>Date Diagnosed</td>
                                        <td>End Date</td>
                                        <td>Action</td>
                                    </tr>
                                </thead>  
                         <?php $i=1;
                         if(is_array($active_problems)){
                            foreach ($active_problems as $value) {                        
                          ?>  
                                <tr>
                                    <td><?=$i;?></td>
                                    <td><?=$value->code;?></td>
                                    <td><?=$value->code_text;?></td>
                                    <td><?=$value->occurrence;?></td>
                                    <td><?=$value->outcome;?></td>
                                    <td><?=$value->referred_by;?></td>
                                    <td><?=date('d-m-Y', strtotime($value->begin_date));?></td>
                                    <td><?=date('d-m-Y', strtotime($value->end_date));?></td>
                                    <td><a href="#" class="edit" data-toggle="modal" data-target="#myModal1"><i class="fa fa-edit"></i> </a>  <a href="#" class="delete"><i class="fa fa-trash"></i> </a></td>
                                </tr>
                        <?php $i++; } } else { ?>   
                                <tr><td colspan="9" style="text-align: center"><?=$active_problems;?></td></tr>
                         <?php } ?>
                            </table>
                        </div>                    	
                    </li>
                </ul>
              </div>
              
              
             <div id="step4" class="tab-pane fade">               	           
                 <form class="upld-frm">                 	
                    <ul>
                        <li>
                            <img src="<?php echo \Yii::getAlias('@web') ?>/images/pdf-icon.png">                        	
                            <i class="close">x</i>
                        </li>
                        <li>
                        	<img src="<?php echo \Yii::getAlias('@web') ?>/images/doc-icon.png" />
                        	<i class="close">x</i>    
                        </li>
                        <li>
                           <img src="<?php echo \Yii::getAlias('@web') ?>/images/jpeg-icon.png" />
                           <i class="close">x</i>
                        </li>
                     </ul>   
                 	<input type="file" class="upd-btn">
                    <input type="submit" value="submit" class="btn btn-default">
                 </form>
              </div>
              <i class="clearfix"></i>
             
            </div>
        </div>	
        </div>
	 <!----------------MAIN CONTAINER--------------------------->
      </div><!--/row-->
	</div>
    
 

<!---------------- Modal------------------->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Allergies</h4>
      </div>
      <div class="modal-body">
        <?php $form = ActiveForm::begin(['id' => 'form-allergy','options' => ['enctype' => 'multipart/form-data']]); ?>	
          <div class="form-group">
          	<div class="col-xs-6">
                <label>Type</label>           
                <select id="ddl1" name="allergy_type" class="form-control" onchange="configureDropDownLists(this,document.getElementById('ddl2'))">
				<option value="-1" selected="selected">Type</option>
				<option value="Environmental">Environmental</option>
				<option value="Food" >Food</option>                  
                </select>
          	</div>
          	<div class="col-xs-6">
                <label>Allergy</label>           
                <select class="form-control" id="ddl2" name="allergy">
                    <option value="-1" selected="selected">Alergy</option>
                </select>
            </div>
            <i class="clearfix"></i>
          </div>          
          
          <div class="form-group">
            <div class="col-xs-6">
                <label>Location</label>      
                <select class="form-control" name="location">
                    <option>select</option>
                    <option value="Skin">Skin</option>
                    <option value="Local">Local</option>
                    <option value="Abdominal">Abdominal</option>
                    <option value="Systemic/Anaphylactic">Systemic/Anaphylactic</option>
                </select>                                 
            </div>
          	<div class="col-xs-6">
                <label>Reaction</label>           
                <input type="text" class="form-control" name="reaction" value=""> 
            </div>
            <i class="clearfix"></i>
          </div>
          <div class="form-group col-xs-12">
            <label>Severity</label>           
            <select class="form-control" name="severity">
            	<option>select</option>
                <option value="Very Mild">Very Mild</option>
                <option value="Mild">Mild</option>
                <option value="Moderate">Moderate</option>
                <option value="Severe">Severe</option>
            </select> 
          </div>
          
          <div class="form-group">
          	<div class="col-xs-6">
                <label>Begin Date</label>	
          		<div class="input-group date" id="allergy_datepicker_begin">
                            <input type="text" name="begin_date" class="form-control">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
          	 </div>	
             <div class="col-xs-6">
                <label>End Date</label>           
                <div class="input-group date" id="allergy_datepicker_end">
                    <input type="text" name="end_date" class="form-control">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div> 
             </div>
             <i class="clearfix"></i>
          </div>
          
           <div class="modal-footer">
            <?= Html::submitButton('Add Allergies', ['class' => 'btn btn-primary', 'name' => 'allergies', 'value' => 'submit']) ?>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>            
           </div>
          <i class="clearfix"></i>
         <?php ActiveForm::end(); ?>          
      </div>     
    </div>
  </div>
</div>
<!----------- /Modal-----------> 


<!---------------- Modal2------------------->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Add Medications</h4>
      </div>
      <div class="modal-body">
          <?php $form = ActiveForm::begin(['id' => 'form-medication','options' => ['enctype' => 'multipart/form-data']]); ?>	
          <div class="form-group">
          	<div class="col-xs-6">
                <label>Medication</label>           
                <input type="text" name='STR' class="form-control" placeholder="Search">
          	</div>
          	<div class="col-xs-6">
                <label>Dose</label>           
                <input type="text" name="dose" class="form-control" placeholder="Dosage">
            </div>
            <i class="clearfix"></i>
          </div>          
          
          <div class="form-group">
            <div class="col-xs-6">
                <label>Route</label>           
                <input type="text" name="route" class="form-control" placeholder="Mouth/left/eye..">
            </div>
          	<div class="col-xs-6">
                <label>Form</label>           
                <input type="text" name="form" class="form-control" placeholder="Capsules/pills..">
            </div>
            <i class="clearfix"></i>
          </div>
          <!--<div class="form-group col-xs-12">
            <label>Instructions</label>           
            <input type="text" name="dose" class="form-control"> 
          </div>-->
          
          <div class="form-group">
          	<div class="col-xs-6">
                <label>Begin Date</label>	
          		<div class="input-group date" id="medication_datepicker_begin">
                            <input type="text" class="form-control" name="begin_date">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
          	 </div>	
             <div class="col-xs-6">
                <label>End Date</label>           
                <div class="input-group date" id="medication_datepicker_end">
                    <input type="text" class="form-control" name="end_date">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div> 
             </div>
             <i class="clearfix"></i>
          </div>
           <div class="modal-footer">
            <?= Html::submitButton('Add Medications', ['class' => 'btn btn-primary', 'name' => 'medications', 'value' => 'submit']) ?>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>            
           </div>
          <i class="clearfix"></i>
        <?php ActiveForm::end(); ?>  
      </div>     
    </div>
  </div>
</div>

<!----------- /Modal2----------->  


<!---------------- Modal3------------------->
<div class="modal fade" id="myModal3" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title" id="myModalLabel">Add Active Problems</h4>
          </div>
          <div class="modal-body">
              <?php $form = ActiveForm::begin(['id' => 'form-consult','options' => ['enctype' => 'multipart/form-data']]); ?>	
                  <!--  <div class="form-group">
                          <div class="col-xs-6">
                          <label>Search</label>           
                          <input type="text" class="form-control" placeholder="Search">
                          </div>
                          <div class="col-xs-6">
                          <label>Problem</label>           
                          <input type="text" class="form-control">
                      </div>
                      <i class="clearfix"></i>
                    </div>  -->        
              
                  <div class="form-group">
                      <div class="col-xs-6">
                          <label>Code Type</label>           
                          <input type="text" name="code_type" class="form-control">
                      </div>
                      <div class="col-xs-6">
                          <label>Problem</label>           
                          <input type="text" name="code_text" class="form-control">
                      </div>              
                
                      <i class="clearfix"></i>
                  </div>
                  <div class="form-group">
                      <div class="col-xs-6">
                          <label>Outcome</label>           
                          <select class="form-control" name="outcome">
                              <option value="Resolved">Resolved</option>
                              <option value="Improved">Improved</option>
                              <option value="Worse">Worse</option>
                              <option value="Follow Up">Follow Up</option>                              
                          </select> 
                      </div>
                      <div class="col-xs-6">
                          <label>Occurrence</label>           
                          <input type="text" name="occurrence" class="form-control">
                      </div>
                      <i class="clearfix"></i>
                  </div>
              
                  <div class="form-group">
                      <div class="col-xs-6">
                          <label>Begin Date</label>	
                          <div class="input-group date" id="activeprobs_datepicker_begin">
                              <input type="text" class="form-control" name="begin_date" >
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                          </div>
                      </div>	
                      <div class="col-xs-6">
                          <label>End Date</label>           
                          <div class="input-group date" id="activeprobs_datepicker_end">
                              <input type="text" class="form-control" name="end_date" >
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                          </div> 
                      </div>
                      <i class="clearfix"></i>
                  </div>
                  <div class="modal-footer">`
                      <?= Html::submitButton('Add', ['class' => 'btn btn-primary', 'name' => 'activeproblems', 'value' => 'submit']) ?>
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>            
                  </div>
                  <i class="clearfix"></i>
               <?php ActiveForm::end(); ?>
          </div>
          
      </div>
  </div>
</div>

<!----------- /Modal3----------->  
    
    
<script src="<?php echo \Yii::getAlias('@web') ?>/js/jquery.min.js"></script>
<script src="<?php echo \Yii::getAlias('@web') ?>/js/moment-with-locales.js"></script>
<script src="<?php echo \Yii::getAlias('@web') ?>/js/bootstrap-datetimepicker.js"></script>
    
<script>
    $('#myModal1').modal(options);
    $('#myModal2').modal(options);
    $('#myModal3').modal(options);
</script>
    
<script type="text/javascript">
    var $j = jQuery.noConflict();
    $j(function () {
        $j('#allergy_datepicker_begin').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        
        $j('#allergy_datepicker_end').datetimepicker({
            format: 'DD-MM-YYYY'
        }); 
        
        $j('#medication_datepicker_begin').datetimepicker({
            format: 'DD-MM-YYYY'
        });
        
        $j('#medication_datepicker_end').datetimepicker({
            format: 'DD-MM-YYYY'
        });          
        
        $j('#activeprobs_datepicker_begin').datetimepicker({
            format: 'DD-MM-YYYY'
        }); 
        
        $j('#activeprobs_datepicker_end').datetimepicker({
            format: 'DD-MM-YYYY'
        }); 
    });
</script>                
        
    </div>
</div>