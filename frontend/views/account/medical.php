<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\jui\AutoComplete;
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
            $("#ddl2").remove();
            var allergy_drop_down ='<select class="form-control" id="ddl2" name="allergy"><option value="-1" selected="selected">Allergy</option>';            
            for (i = 0; i < Food.length; i++) {            
               allergy_drop_down +='<option value="'+Environmental[i]+'">'+Environmental[i]+'</option>';  //createOption(ddl2, Food[i], Food[i]);
            } 
             allergy_drop_down +='<select>';
            $("#input_allergy").append(allergy_drop_down);            
            /*ddl2.options.length = 0;
            for (i = 0; i < Environmental.length; i++) {
                createOption(ddl2, Environmental[i], Environmental[i]);
            }*/
            break;
        case 'Food':
            $("#ddl2").remove();
            var allergy_drop_down ='<select class="form-control" id="ddl2" name="allergy"><option value="-1" selected="selected">Allergy</option>';            
            for (i = 0; i < Food.length; i++) {            
               allergy_drop_down +='<option value="'+Food[i]+'">'+Food[i]+'</option>';  //createOption(ddl2, Food[i], Food[i]);
            } 
             allergy_drop_down +='<select>';
            $("#input_allergy").append(allergy_drop_down);   
            /* for (i = 0; i < Food.length; i++) {
            createOption(ddl2, Food[i], Food[i]);
            }*/
            break;
        case 'Names':
            ddl2.options.length = 0;
            for (i = 0; i < names.length; i++) {
                createOption(ddl2, names[i], names[i]);
            }
            break;
        case 'Drug':
            $("#ddl2").remove();   
            $("#input_allergy").append('<input id="ddl2" class="form-control" name = "allergy"  type="text" />');
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
              	 <button class="btn-default add" id="add_allergies" data-toggle="modal" data-target="#myModal1">Add</button>
              	<ul>
                	<li>
                    	<div class="table-responsive">
                        	<table class="table table-hover table-striped" id="tbl_allergies">
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
                                <tr id="row_<?php echo $value->id; ?>">
                                    <td><?=$i;?></td>
                                    <td><?=$value->allergy_type;?></td>
                                    <td><?=$value->location;?></td>
                                    <td><?=$value->allergy;?></td>
                                    <td><?=$value->reaction;?></td>
                                    <td><?=date('d-m-Y', strtotime($value->begin_date));?></td>
                                    <td><?=date('d-m-Y', strtotime($value->end_date));?></td>
                                    <td><a href="#" class="edit" id="edit_allergy" data-toggle="modal" data-target="#myModal1" data-id="<?=$value->id;?>" data-reaction="<?=$value->reaction;?>" data-allergytype="<?=$value->allergy_type;?>"
                           data-allergy="<?=$value->allergy;?>" data-location="<?=$value->location;?>" data-severity="<?=$value->severity;?>" data-begindate="<?=date('d-m-Y', strtotime($value->begin_date));?>" data-enddate="<?=date('d-m-Y', strtotime($value->end_date));?>" >
                                        <i class="fa fa-edit"></i> </a> 
                                        <a href="#" data-id="<?=$value->id;?>" class="delete del_allergy">
                                        <i class="fa fa-trash"></i> </a>
                                    </td>
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
              	<button class="btn-default add" id="add_medication" data-toggle="modal" data-target="#myModal2">Add</button>	
             	 <ul>
                	<li>
                            <div class="table-responsive">
                        	<table class="table table-hover table-striped" id="tbl_medication">
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
                                <tr id="row_<?php echo $value->id;?>">
                                    <td><?=$i;?></td>
                                    <td><?=$value->STR;?></td>
                                    <td><?=$value->dose;?></td>
                                    <td><?=$value->route;?></td>
                                    <td><?=$value->form;?></td>
                                    <td><a href="#" class="edit" id="edit_medications" data-toggle="modal" data-target="#myModal2" data-id="<?=$value->id;?>" data-str="<?=$value->STR;?>" data-dose="<?=$value->dose;?>"
                           data-route="<?=$value->route;?>" data-form="<?=$value->form;?>" data-begindate="<?=date('d-m-Y', strtotime($value->begin_date));?>" data-enddate="<?=date('d-m-Y', strtotime($value->end_date));?>"><i class="fa fa-edit"></i> </a>  
                                        
                                        <a href="#" data-id="<?=$value->id;?>" class="delete del_medication"><i class="fa fa-trash"></i> </a></td>
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
              	<button class="btn-default add"  id="add_activeproblems" data-toggle="modal" data-target="#myModal3">Add</button>             
                 <ul>
                	<li>
                    	<div class="table-responsive">
                        	<table class="table table-hover table-striped" id="tbl_active_problems">
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
                                <tr id="row_<?php echo $value->id;?>">
                                    <td><?=$i;?></td>
                                    <td><?=$value->code;?></td>
                                    <td><?=$value->code_text;?></td>
                                    <td><?=$value->occurrence;?></td>
                                    <td><?=$value->outcome;?></td>
                                    <td><?=$value->referred_by;?></td>
                                    <td><?=date('d-m-Y', strtotime($value->begin_date));?></td>
                                    <td><?=date('d-m-Y', strtotime($value->end_date));?></td>
                                    <td><a href="#" class="edit" id="edit_activeproblems" data-toggle="modal" data-target="#myModal3" data-id="<?=$value->id;?>" data-code="<?=$value->code;?>" data-code_text="<?=$value->code_text;?>"
                           data-occurrence="<?=$value->occurrence;?>" data-outcome="<?=$value->outcome;?>" data-referred_by="<?=$value->referred_by;?>" data-begindate="<?=date('d-m-Y', strtotime($value->begin_date));?>" data-enddate="<?=date('d-m-Y', strtotime($value->end_date));?>">
                                        <i class="fa fa-edit"></i> </a>  
                                        <a href="#" data-id="<?=$value->id;?>" class="delete del_active_problem"><i class="fa fa-trash"></i> </a></td>
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
                <?php $form = ActiveForm::begin(['id' => 'form-upload', 'options' => ['enctype' => 'multipart/form-data','class'=>'upld-frm']]); ?>	                                       	
                 <ul id="patient_docs">
                    <?php  if(is_array($patient_docs)){
                      foreach ($patient_docs as $value) {                          
                        $type = explode("/", $value->docType);
                        $doc_type =  end($type);
                        switch ($doc_type) {
                            case 'jpeg':
                            case 'png':    
                                $icon_image = \Yii::getAlias('@web')."/images/jpeg-icon.png";
                                break;
                            case 'pdf':    
                                $icon_image = \Yii::getAlias('@web')."/images/pdf-icon.png";
                                break; 
                            default:
                                $icon_image = \Yii::getAlias('@web')."/images/doc-icon.png";
                                break;                                 
                            }?>
                        <li id="li_<?=$value->id;?>">
                            <img src="<?php echo $icon_image; ?>"><br>
                            <?php echo $value->title;?>
                            <i class="close del_patient_docs" data-id="<?=$value->id;?>" >x</i>
                        </li>
                         <?php } } ?>  
                                      
                        
                       <!-- <li>
                            <img src="<?php //echo \Yii::getAlias('@web') ?>/images/pdf-icon.png">
                        <i class="close">x</i>
                        </li>
                        <li>
                            <img src="<?php //echo \Yii::getAlias('@web') ?>/images/doc-icon.png" />
                             
                        </li>
                        <li>
                           <img src="<?php //echo \Yii::getAlias('@web') ?>/images/jpeg-icon.png" />
                          
                        </li>-->
                     </ul>   
                 	<input type="file" name="doc" class="upd-btn">
                        <?= Html::submitButton('Upload', ['class' => 'btn btn-primary','id' =>'patient_doc', 'name' => 'patient_doc', 'value' => 'submit']) ?>
                   
                 <?php ActiveForm::end(); ?>  
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
                                <option value="Drug" >Drug</option>   
                </select>
          	</div>
          	<div class="col-xs-6">
                <label>Allergy</label> 
                <div id="input_allergy">
                <select class="form-control" id="ddl2" name="allergy">
                    <option value="-1" selected="selected">Allergy</option>
                </select>
                </div>
            </div>
            <i class="clearfix"></i>
          </div>          
          
          <div class="form-group">
            <div class="col-xs-6">
                <label>Location</label>      
                <select id="ddl_location" class="form-control" name="location">
                    <option>select</option>
                    <option value="Skin">Skin</option>
                    <option value="Local">Local</option>
                    <option value="Abdominal">Abdominal</option>
                    <option value="Systemic/Anaphylactic">Systemic/Anaphylactic</option>
                </select>                                 
            </div>
          	<div class="col-xs-6">
                <label>Reaction</label>           
                <input type="text" id="al_reaction" class="form-control" name="reaction" value=""> 
            </div>
            <i class="clearfix"></i>
          </div>
          <div class="form-group col-xs-12">
            <label>Severity</label>           
            <select id="ddl_severity" class="form-control" name="severity">
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
                            <input id="txt_al_datepicker_begin" type="text" value="" name="begin_date" class="form-control">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
          	 </div>	
             <div class="col-xs-6">
                <label>End Date</label>           
                <div class="input-group date" id="allergy_datepicker_end">
                    <input id="txt_al_datepicker_end" type="text" value="" name="end_date" class="form-control">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div> 
             </div>
             <i class="clearfix"></i>
             <input type="hidden" name="allergyid" id="al_id">
          </div>
          
           <div class="modal-footer">
            <?= Html::submitButton('Add Allergies', ['class' => 'btn btn-primary','id' =>'submit_allergies', 'name' => 'allergies', 'value' => 'submit']) ?>
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
                <?php//  echo \yii\jui\AutoComplete::widget(['name' => 'country','class'=>'form-control','clientOptions' => ['source' => ['USA', 'RUS'], ], ]);?>                 
                <input type="text" name='STR' id="txt_str" class="form-control" placeholder="Search">
          	</div>
          	<div class="col-xs-6">
                <label>Dose</label>           
                <input type="text" name="dose" id="txt_dose" class="form-control" placeholder="Dosage">
            </div>
            <i class="clearfix"></i>
          </div>          
          
          <div class="form-group">
            <div class="col-xs-6">
                <label>Route</label>           
                <input type="text" name="route" id="txt_route" class="form-control" placeholder="Mouth/left/eye..">
            </div>
          	<div class="col-xs-6">
                <label>Form</label>           
                <input type="text" name="form" id="txt_form" class="form-control" placeholder="Capsules/pills..">
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
                            <input type="text" id="txt_md_datepicker_begin" class="form-control" name="begin_date">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div>
          	 </div>	
             <div class="col-xs-6">
                <label>End Date</label>           
                <div class="input-group date" id="medication_datepicker_end">
                    <input type="text" id="txt_md_datepicker_end" class="form-control" name="end_date">
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                </div> 
                <input type="hidden" name="medicationid" id="md_id">
             </div>
             <i class="clearfix"></i>
          </div>
           <div class="modal-footer">
            <?= Html::submitButton('Add Medications', ['class' => 'btn btn-primary','id'=>'submit_medications','name' => 'medications', 'value' => 'submit']) ?>
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
                          <input type="text" id="txt_code_type" name="code_type" class="form-control">
                      </div>
                      <div class="col-xs-6">
                          <label>Problem</label>           
                          <input type="text" id="txt_code_text" name="code_text" class="form-control">
                      </div>              
                
                      <i class="clearfix"></i>
                  </div>
                  <div class="form-group">
                      <div class="col-xs-6">
                          <label>Outcome</label>           
                          <select id="ddl_outcome" class="form-control" name="outcome">
                              <option value="Resolved">Resolved</option>
                              <option value="Improved">Improved</option>
                              <option value="Worse">Worse</option>
                              <option value="Follow Up">Follow Up</option>                              
                          </select> 
                      </div>
                      <div class="col-xs-6">
                          <label>Occurrence</label>           
                          <input type="text" id="txt_occurrence" name="occurrence" class="form-control">
                      </div>
                      <i class="clearfix"></i>
                  </div>
              
                  <div class="form-group">
                      <div class="col-xs-6">
                          <label>Begin Date</label>	
                          <div class="input-group date" id="activeprobs_datepicker_begin">
                              <input id="txt_ap_datepicker_begin" type="text" class="form-control" name="begin_date" >
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                          </div>
                      </div>	
                      <div class="col-xs-6">
                          <label>End Date</label>           
                          <div class="input-group date" id="activeprobs_datepicker_end">
                              <input id="txt_ap_datepicker_end" type="text" class="form-control" name="end_date" >
                              <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                              </span>
                          </div> 
                          <input type="hidden" name="problemid" id="ap_id">
                      </div>
                      <i class="clearfix"></i>
                  </div>
                  <div class="modal-footer">
                      <?= Html::submitButton('Add', ['class' => 'btn btn-primary', 'name' => 'activeproblems', 'id'=>'submit_activeproblems', 'value' => 'submit']) ?>
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
    
    //Add Allergies
    $j(document).on("click", "#add_allergies", function () { 

        $j("#myModal1 #al_id").val('');
        $j("#myModal1 #ddl1").val('-1');        
        $("#ddl2").remove();
        var allergy_drop_down ='<select class="form-control" id="ddl2" name="allergy"><option value="-1" selected="selected">Allergy</option><select>';   
        $("#input_allergy").append(allergy_drop_down);           
        //$j("#myModal1 #ddl2").val('Allergy'); 
        $j("#myModal1 #ddl_location").val('select'); 
        $j("#myModal1 #al_reaction").val(''); 
        $j("#myModal1 #ddl_severity").val('select'); 
        $j("#myModal1 #txt_al_datepicker_begin").val('');
        $j("#myModal1 #txt_al_datepicker_end").val('');        
        $j('#submit_allergies').text('Add Allergies');
        
    });
    
    //Edit Allergies
    $j(document).on("click", "#edit_allergy", function () {        
        $j("#myModal1 #al_id").val($j(this).data('id'));
        $j("#myModal1 #ddl1").val($j(this).data('allergytype'));
        $('#ddl2').empty();
        if($j(this).data('allergytype')=='Drug'){
            $("#ddl2").remove();   
            $("#input_allergy").append('<input id="ddl2" class="form-control" name = "allergy" value="'+$j(this).data('allergy')+'"  type="text" />'); 
        }else{
            $("#ddl2").remove();   
            var allergy_drop_down ='<select class="form-control" id="ddl2" name="allergy"><option value="-1" selected="selected">Allergy</option> <option selected="selected" value="'+$j(this).data('allergy')+'">'+$j(this).data('allergy')+'</option></select>';            
            $("#input_allergy").append(allergy_drop_down);                              
        }
        
        //configureDropDownLists($j(this).data('allergytype'),$j(this).data('allergy'))
        
        //$j("#myModal1 #input_allergy #ddl2").val($j(this).data('allergy'));
        $j("#myModal1 #ddl_location").val($j(this).data('location'));
        $j("#myModal1 #al_reaction").val($j(this).data('reaction')); 
        $j("#myModal1 #ddl_severity").val($j(this).data('severity'));
        $j("#myModal1 #txt_al_datepicker_begin").val($j(this).data('begindate'));
        $j("#myModal1 #txt_al_datepicker_end").val($j(this).data('enddate'));        
        $j('#submit_allergies').text('Update Allergies');
        
    });
    
    //Add Medications
    $j(document).on("click", "#add_medication", function () { 

        $j("#myModal2 #md_id").val('');
        $j("#myModal2 #txt_str").val('');        
        $j("#myModal2 #txt_dose").val(''); 
        $j("#myModal2 #txt_route").val(''); 
        $j("#myModal2 #txt_md_datepicker_begin").val('');
        $j("#myModal2 #txt_md_datepicker_end").val('');        
        $j('#submit_medications').text('Add Medications');
        
    });
    
    //Edit Medications    
    $j(document).on("click", "#edit_medications", function () {        

        $j("#myModal2 #md_id").val($j(this).data('id'));        
        $j("#myModal2 #txt_str").val($j(this).data('str'));
        $j("#myModal2 #txt_dose").val($j(this).data('dose')); 
        $j("#myModal2 #txt_route").val($j(this).data('route'));
        $j("#myModal2 #txt_form").val($j(this).data('form'));
        $j("#myModal2 #txt_md_datepicker_begin").val($j(this).data('begindate'));
        $j("#myModal2 #txt_md_datepicker_end").val($j(this).data('enddate'));        
        $j('#submit_medications').text('Update Medications');
        
    });    
    
    //Add Active Problems
    $j(document).on("click", "#add_activeproblems", function () { 

        $j("#myModal3 #ap_id").val('');
        $j("#myModal3 #txt_code_type").val('');        
        $j("#myModal3 #txt_code_text").val(''); 
        $j("#myModal3 #ddl_outcome").val(''); 
        $j("#myModal3 #txt_occurrence").val(''); 
        $j("#myModal3 #txt_ap_datepicker_begin").val('');
        $j("#myModal3 #txt_ap_datepicker_end").val('');        
        $j('#submit_activeproblems').text('Add Active Problems');
        
    });
    
    //Edit Active Problems    
    $j(document).on("click", "#edit_activeproblems", function () {        

        $j("#myModal3 #ap_id").val($j(this).data('id'));        
        $j("#myModal3 #txt_code_type").val($j(this).data('code'));
        $j("#myModal3 #txt_code_text").val($j(this).data('code_text')); 
        $j("#myModal3 #ddl_outcome").val($j(this).data('outcome'));
        $j("#myModal3 #txt_occurrence").val($j(this).data('occurrence'));
        $j("#myModal3 #txt_ap_datepicker_begin").val($j(this).data('begindate'));
        $j("#myModal3 #txt_ap_datepicker_end").val($j(this).data('enddate'));        
        $j('#submit_activeproblems').text('Update Active Problems');
        
    });     
    
    $j(document).on("click", ".del_allergy", function () {    
        if (window.confirm('Are you sure you want to delete this item?')){
          //alert($j(this).data('id'));
          var Id = $j(this).data('id');     
            $j.ajax({
                    type: "POST", 		
                    url: '<?php echo Url::toRoute('/account/delete_allergy') ?>', 
                    async: false,
                    data: {id : $j(this).data('id')},                 
                    success: function (response) {
                       if(response==1){
                           $j('table#tbl_allergies tr#row_'+Id).remove();
                       }else{
                           alert(response);
                       }                       
                    }                  
                });         
     }
     else{     
        return false;
     }
     });
     
     
    $j(document).on("click", ".del_medication", function () {    
        if (window.confirm('Are you sure you want to delete this item?')){
          //alert($j(this).data('id'));
          var Id = $j(this).data('id');     
            $j.ajax({
                    type: "POST", 		
                    url: '<?php echo Url::toRoute('/account/delete_medication') ?>', 
                    async: false,
                    data: {id : $j(this).data('id')},                 
                    success: function (response) {
                       if(response==1){
                           $j('table#tbl_medication tr#row_'+Id).remove();
                       }else{
                           alert(response);
                       }                       
                    }                  
                });         
     }
     else{     
        return false;
     }     
     
    });
    
    $j(document).on("click", ".del_active_problem", function () {    
        if (window.confirm('Are you sure you want to delete this item?')){
          //alert($j(this).data('id'));
          var Id = $j(this).data('id');     
            $j.ajax({
                    type: "POST", 		
                    url: '<?php echo Url::toRoute('/account/delete_active_problem') ?>', 
                    async: false,
                    data: {id : $j(this).data('id')},                 
                    success: function (response) {
                       if(response==1){
                           $j('table#tbl_active_problems tr#row_'+Id).remove();
                       }else{
                           alert(response);
                       }                       
                    }                  
                });         
     }
     else{     
        return false;
     }     
     
    });    
    
    $j(document).on("click", ".del_patient_docs", function () {    
        if (window.confirm('Are you sure you want to delete this file?')){
          //alert($j(this).data('id'));
          var Id = $j(this).data('id');     
            $j.ajax({
                    type: "POST", 		
                    url: '<?php echo Url::toRoute('/account/delete_patient_doc') ?>', 
                    async: false,
                    data: {id : $j(this).data('id')},                 
                    success: function (response) {
                       if(response==1){
                           alert('File deleted successfully.');
                           location.reload(true);
                           $j('div#step4 ui#patient_docs li#li_'+Id).remove();
                       }else{
                           alert(response);
                       }                       
                    }                  
                });         
     }
     else{     
        return false;
     }     
     
    });        
    
    
    
</script>                
        
    </div>
</div>