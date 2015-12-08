<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\widgets\ActiveForm;
use kartik\widgets\FileInput;
$this->title = 'Update Account Details';
?>
<!----------------MAIN CONTAINER--------------------------->
<div class="container">
    <div class="row row-offcanvas row-offcanvas-left ">
	<!--toggle sidebar button-->
	<p class="visible-xs">
		<button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
	</p>
	
        <div class="col-sm-8 main-container mk-donation edit-info">          
          <!--toggle sidebar button-->
          <p class="visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
          </p> 
          
          <h2>edit your information</h2> 
            
            <div class="row">
            <div class="col-lg-12">
                <!--<div class="panel-group" id="accordion">-->
                <div class="panel-group2">	                    
                	<!-- /.panel -->
                    <div class="panel panel-default">                        
                       
                            <div class="panel-body">                              
                               <form class="form-inline">
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label for="exampleInputName2">First Name</label>
                                    <input type="text" class="form-control"  placeholder="First Name" value="<?php echo $data->description->fname ?>">
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Last Name</label>
                                    <input type="text" class="form-control" placeholder="Last Name" value="<?php echo $data->description->lname ?>">
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Sex</label>
                                    <div class="radio">
                                      <label>
                                        <input type="radio" name="optionsRadios" id="optionsRadios1" value="Male" <?php if($data->description->sex=='Male'){ ?> checked <?php } ?>>
                                        Male
                                      </label>
                                    </div>
                                    <div class="radio">
                                      <label>
                                        <input type="radio" name="optionsRadios" id="optionsRadios2" value="Female" <?php if($data->description->sex=='Female'){ ?>checked <?php } ?>>
                                        Female
                                      </label>
                                    </div>
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Date of Birth</label>
                                    <div class="input-group date dob" id="datepicker_dob">
                                        <input id="txt_datepicker_dob" type="text" name="DOB" class="form-control" value="<?php echo date('d-m-Y', strtotime($data->description->DOB)); ?>" placeholder="Date of Birth">
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Marital Status</label>
                                    <select class="form-control" placeholder="Marital Status"> 
                                        <option <?php if($data->description->marital_status =='Single' ){ ?>selected <?php }?>>Single</option>
                                        <option <?php if($data->description->marital_status =='Married' ){ ?>selected <?php }?>>Married</option>
                                        <option <?php if($data->description->marital_status =='Divorced' ){ ?>selected <?php }?>>Divorced</option>
                                        <option <?php if($data->description->marital_status =='Separated' ){ ?>selected <?php }?>>Separated</option>
                                        <option <?php if($data->description->marital_status =='Widow' ){ ?>selected <?php }?>>Widow</option>
                                    </select>
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Address</label>
                                    <input type="text" class="form-control" placeholder="Address" value="<?php echo $data->description->address ?>">
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >City</label>
                                    <input type="text" class="form-control" placeholder="City" value="<?php echo $data->description->city ?>">
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >State</label>
                                    <input type="text" class="form-control" placeholder="State" value="<?php echo $data->description->state ?>">
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Country</label>
                                    <input type="text" class="form-control" placeholder="Country" value="<?php echo $data->description->country ?>">
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Zipcode</label>
                                    <input type="text" class="form-control" placeholder="Zipcode" value="<?php echo $data->description->zipcode ?>">
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Email</label>
                                    <input type="text" class="form-control" placeholder="Email" value="<?php echo $data->description->email ?>">
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Guardians Name</label>
                                    <input type="text" class="form-control" placeholder="Guardians Name" value="<?php echo $data->description->guardians_name ?>">
                                  </div>
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Skypeid</label>
                                    <input type="text" class="form-control" placeholder="Skypeid" value="<?php echo $data->description->skypeid ?>">
                                  </div>
                                  
                                  <div class="form-group col-sm-6 col-xs-12">
                                    <label >Photo</label>
                                    <input type="file" class="upld-fld">
                                    <i class="clearfix"></i>
                                  </div>
                                  <div class="clearfix"></div>
                                  <div class="btn-grp">
                                    <div class="col-xs-6">
                                    	<a href="#" class="btn btn-default sbt-btn" data-toggle="modal" data-target="#popup2">Submit</a>
                                     </div>   
                                    <div class="col-xs-6">
                                      <a href="#" class="btn btn-default upd-btn" data-toggle="modal" data-target="#popup" >Update</a>
                                    </div>
                                    <i class="clearfix"></i>
                                  </div>                                 
                                </form>
                            </div>                       
                    </div>

                </div>
                <!-- /.panel-group -->
            </div>
            <!-- /.col-lg-12 -->
        </div>
        </div>
</div>
</div>

<script src="<?php echo \Yii::getAlias('@web') ?>/js/jquery.min.js"></script>
<script src="<?php echo \Yii::getAlias('@web') ?>/js/moment-with-locales.js"></script>
<script src="<?php echo \Yii::getAlias('@web') ?>/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript">
    var $j = jQuery.noConflict();  
    
    $j(function () {
        $j('#datepicker_dob').datetimepicker({
            format: 'DD-MM-YYYY'
        });
    });
</script>