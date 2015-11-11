<?php
/* @var $this yii\web\View */
?>
<!----------------MAIN CONTAINER--------------------------->
        
        <div class="col-sm-9 main-container dsh-brd">          
          <!--toggle sidebar button-->
          <p class="visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
          </p> 
          
          <h3>Welcome John Doe to Phone A Doctor</h3>
          
         <div class="col-md-6 col-xs-12 sm-no-pad-lt sm-pad-rt">
         	<div class="dsh-blk">
            	<h4>Detail Patient Informtion</h4>
                <div class="blk-inr">
                    <p>Patient info goes here <span><img src="<?php echo \Yii::getAlias('@web') ?>/images/jhon.png" alt=""></span></p>
                    <i class="clearfix"></i>
                    
                    <ul class="icn-list">
                        <li><a href="#" title="Manage Medical History"><img src="<?php echo \Yii::getAlias('@web') ?>/images/img1.png" alt=""></a></li>
                        <li><a href="#" title="Consultation History"><img src="<?php echo \Yii::getAlias('@web') ?>/images/img2.png" alt=""></a></li>
                        <li><a href="#" title="Make Donation"><img src="<?php echo \Yii::getAlias('@web') ?>/images/img3.png" alt=""></a></li>
                        <li><a href="#" title="Patient"><img src="<?php echo \Yii::getAlias('@web') ?>/images/patient-icon.png" alt=""></a></li>
                    </ul>
                </div>
            </div>
         </div>
         
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
         </div>
         
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