<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Dashboard';
$session = \Yii::$app->session;
?>
<!----------------MAIN CONTAINER--------------------------->
        
        <div class="col-sm-9 main-container dsh-brd">          
          <!--toggle sidebar button-->
          <p class="visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
          </p> 
          
          <h3>Welcome <?php echo $session['fname']." ".$session['lname'] ?> to Phone A Doctor</h3>
          <?php
            
            $details=$session['consult'];
           if($details && (strtotime($details->end) > time())){

          $start = explode(" ", $details->start);
          $start_time = $start[1];
          $start_date = $start[0];

          $end = explode(" ", $details->end);
          $end_time = $end[1];
          $end_date = $end[0];
          ?>
         <div class="col-md-12 col-xs-12 sm-no-pad-lt sm-pad-rt">
          <div class="dsh-blk">
          <h4>your schedule for consultation  </h4> 
        <div class="col-sm-12 main-container whn-phn">          
          <!--toggle sidebar button-->
          <p class="visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
          </p> 
          
                   
          <div class="calling-dctr">
            <span>Date: <?php echo $start_date ?></span><span>Time: <?php echo $start_time ?></span>
            <h3>Doctor you would be speaking to</h3>
            <img src="<?php echo \Yii::getAlias('@web') ?>/doctorimages/<?php echo $details->image ?>.png" alt="" width="60px"  >
            <p><?php echo $details->doctor ?></p>
            <h3>Identification Code for the session</h3>
            <h5>#<?php echo $details->code ?></h5>            
          </div>
          </div>
          </div>
        </div>
         <?php
       }else{
        

        ?>
        <div class="col-md-12 col-xs-12 sm-no-pad-lt sm-pad-rt">
          <div class="dsh-blk">
              <h4>Make a consultation</h4>
                <?php $form = ActiveForm::begin(['id' => 'form-consult']); ?>
      
      <div class="form-group">
        <textarea class="form-control txt-area" name="question" rows="8" placeholder="Type your question here"></textarea>
      </div>
     
    <?= Html::submitButton('Submit your question', ['class' => 'btn btn-default', 'name' => 'signup-button']) ?>
                   
                <?php ActiveForm::end(); ?>
            </div>
         </div>
        
        <?php
          }
        ?>
         <!--
         <div class="col-md-6 col-xs-12 sm-no-pad-rt sm-pad-lt">
         	<div class="dsh-blk">
            	<h4>Search A Doctor</h4>
                <div class="blk-inr">
                    <form class="srch-dctr-frm">
                     <div class="form-group">                    
                        <div class="input-group">
                          <div class="input-group-addon"><i class="fa fa-search-plus"></i></div>
                          <input type="text" class="form-control" id="exampleInputAmount" placeholder="Enter Search item">                      
                        </div>
                      </div>
                      <div class="form-group">
                          <div class="col-sm-3 col-xs-12">
                            <label>Speciality</label>
                          </div>  
                            <div class=" col-sm-9 col-xs-12 sm-no-pad-rt">
                                <select class="form-control">
                                    <option>General</option>
                                </select>                        	
                            </div> 
                            <div class="clearfix"></div>
                      </div>
                      
                      <div class="form-inline spc">
                      		<div class="form-group">
                            	<label>Results per Page</label>
                                <div class="input-group">
                                <select class="sel">
                                    <option></option>
                                </select>
                                
                                <label class="radio-inline">
                                  <input type="radio" name="inlineRadioOptions" id="inlineRadio1" value="option1"> Option A
                                </label>
                                <label class="radio-inline">
                                  <input type="radio" name="inlineRadioOptions" id="inlineRadio2" value="option2"> Option B
                                </label>                               
                                </div>
                            </div>
                            
                      </div>
                      
                      <input type="submit" value="Search" class="btn btn-default pull-right srch-btn">
                      <div class="clearfix"></div>
                    </form>
                </div>
            </div>
         </div>-->
         
         <div class="col-md-6 col-xs-12 sm-no-pad-lt sm-pad-rt">
         	<div class="dsh-blk">
            	<h4>Detail Patient Informtion</h4>
                <div class="blk-inr">
                	<div class="table-responsive">
                    	<table class="table table-bordered">
 							<tr class="hdng">
                            	<td>Number</td>
                                <td>Column 2</td>
                                <td>Column 3</td>
                            </tr>
                            
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            
						</table>
                    </div>
                    
                    <a href="#" class="btn btn-default view-btn pull-right">View more</a>
                    <i class="clearfix"></i>
                </div>
            </div>
         </div>
         
         <div class="col-md-6 col-xs-12 sm-no-pad-rt sm-pad-lt">
         	<div class="dsh-blk">
            	<h4>Detail Patient Informtion</h4>
                <div class="blk-inr">
                	<div class="table-responsive">
                    	<table class="table table-bordered">
 							<tr class="hdng">
                            	<td>Number</td>
                                <td>Column 2</td>
                                <td>Column 3</td>
                            </tr>
                            
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                            	<td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            
						</table>
                    </div>
                    <a href="#" class="btn btn-default view-btn pull-right">View more</a>
                    <i class="clearfix"></i>
                </div>
            </div>
         </div>
         
        </div>
        
	 <!----------------MAIN CONTAINER--------------------------->