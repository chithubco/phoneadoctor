<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
$this->title = 'Consultation Details';
?>

 <!-- --------------MAIN CONTAINER------------------------- -->
       <div class="col-sm-8 main-container consultation-history">
      <!--toggle sidebar button-->
      <p class="visible-xs">
        <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
      </p>
      <h2>Consultation History</h2>
      <div class="updt-del">
        <div class="tab-content">
          <div id="step1" class="tab-pane fade in active">
            <!--<button class="btn-default add" data-toggle="modal" data-target="#myModal1">Add</button>-->
            <ul>
              <li class="contbox2">
                <div class="table-responsive">
                  <table class="table table-hover table-striped">
                    <thead>
                      <tr class=" first-tr">
                        <td>S.No</td>
                        <td>Date/Time</td>                       
                        <td>My Complain </td>
                        <td>Recommendation</td>
                        <td>Prescriptions?</td>
                        <td>Action</td>
                      </tr>
                    </thead>
                     <?php $i=1;
                      if(is_array($data)){
                            foreach ($data as $value) {                        
                          ?>  
                    <tr id="row_<?php echo $value->id; ?>">
                      <td><?=$i;?></td>   
                      <td><?=date('d-m-Y H:i:s', strtotime($value->date));?></td>                      
                      <td><?=$value->brief_description;?></td>
                      <td><?=$value->sms_text;?></td>
                      <td><?=($value->prescription!=NULL)?'Yes':'No';?></td>
                      <td><a href="#" class="edit view_more" data-toggle="modal" data-target="#myModal2" data-id="<?=$value->id;?>" data-doctor="<?=$value->title.$value->fname." ".$value->lname;?>" data-complaint="<?=$value->brief_description;?>" data-c_date="<?=date('d-m-Y H:i:s', strtotime($value->date));?>">View More </a>
                        <!-- <a href="#" class="edit" data-toggle="modal" data-target="#myModal2"><i class="fa fa-edit"></i> </a> <a href="#" class="delete"><i class="fa fa-trash"></i> </a>--></td>
                    </tr>
                     <?php $i++; } } else { ?>   
                                <tr><td colspan="9" style="text-align: center"><?=$data;?></td></tr>
                         <?php } ?>                    
                  </table>
                </div>
              </li>
            </ul>
          </div>
        <i class="clearfix"></i> </div>
      </div>
    </div>
   <!----------------MAIN CONTAINER--------------------------->
   
<!---------------- Modal2------------------->
<div class="modal fade" id="myModal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">View More</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="contBox6">
            <ul class="cont1">
              <li>Date/Time</li>
              <li id="date">14-12-2015 10:45AM</li>
            </ul>
           <div class="clearfix"></div>
            <ul class="cont1">
              <li> Consultation ID </li>
              <li id="consultation_id"></li>
            </ul>
			<div class="clearfix"></div>
            <ul class="cont1">
              <li>Consultation Request</li>
              <li id="complaint"></li>
            </ul>
			<div class="clearfix"></div>
            <ul class="cont1">
              <li>Doctor Name </li>
              <li id="doctor_name"></li>
            </ul>
			<div class="clearfix"></div>
            <ul class="cont1">
                <li> Recommendation</li>
            </ul>
          </div>
          <div class="clearfix"></div>
          <div class=""> <i class="clearfix"></i>
            <div class="table-responsive">
              <table class="table table-hover table-striped">
                <tr class="inr-head">
                  <td><a href="#">Description</a></td>
                  <td><a href="#">Prescription</a></td>
                  <!--<td><a href="#">Consultation</a></td>
                  <td><a href="#">Any hospital</a></td>-->
                </tr>
                <tr>
                  <td><p class="cont8"><div id="des"></div></p></td>                                       
                    
                  <td><ul id="prescription" class="prescription">
                    </ul>
                  </td>
                  <!--<td><p class="cont8"> Simply dummy text. </p>  </td>
                  <td><p class="cont8"> Simply dummy text.</p></td>-->
                </tr>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn1 btn-primary" data-dismiss="modal">Close</button>
          </div>
          <i class="clearfix"></i>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="<?php echo \Yii::getAlias('@web') ?>/js/jquery.min.js"></script>

<script type="text/javascript">
    var $j = jQuery.noConflict();  
    //View More
    $j(document).on("click", ".view_more", function () { 

          //var Id = $j(this).data('id');     
            $j.ajax({
                    type: "POST", 		
                    url: '<?php echo Url::toRoute('/consultation/getprescription') ?>', 
                    async: false,
                    data: {id : $j(this).data('id')},                 
                    success: function (response) {
                           $j('#prescription').html(response) 
                    }                  
                }); 
                
            $j.ajax({
                    type: "POST", 		
                    url: '<?php echo Url::toRoute('/consultation/getpatientnotes') ?>', 
                    async: false,
                    data: {id : $j(this).data('id')},                 
                    success: function (response) {
                           $j('#des').html(response) 
                    }                  
                });                 
        
        $('#date').html($j(this).data('c_date'));
        $('#consultation_id').html($j(this).data('id'));
        $('#doctor_name').html($j(this).data('doctor'));        
        $('#complaint').html($j(this).data('complaint'));
    
        
    });
</script>
