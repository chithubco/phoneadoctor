<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
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
          <div class="cnt-blk">
          
          <!--
          <h6 class="title">Refine search</h6>
          <form class="con-hist-frm">
              <div class="col-sm-4 col-xs-12">                
                <div class="input-group date" id="datetimepicker2">                    
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                    </span>
                    <input type="text" class="form-control">
                </div>              
              </div>
              <div class="col-sm-4 col-xs-12">
        <div class="input-group">                    
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-user"></span>
                    </span>
                    <input type="text" class="form-control" placeholder="Name of doctor">
                </div> 
              </div>
              <div class="col-sm-4 col-xs-12">
                <div class="input-group">                    
                    <span class="input-group-addon">
                       <i class="fa fa-medkit"></i>

                    </span>
                    <input type="text" class="form-control" placeholder="pescription">
                </div> 
              </div>
              <div class="clearfix"></div>
          </form>
          -->
          <div class="clearfix"></div>
          
          <!-- -------Date of consultation------------- -->
          <?php
foreach ($data as $val) {
  # code...
          ?>
          <div class="doc">
            <ul>
              <li><i class="fa fa-calendar"></i> Date of consultation <span><?php echo $val->start ?></span></li>
                <li><i class="fa fa-tag"></i> Consultation Identification Number <span><?php echo $val->consult_code ?></span></li>
                <li><i class="fa fa-comment-o"></i> Summary of what was asked (request)
                  <p><?php echo $val->notes ?></p>
                </li>
                <!--
                <li><i class="fa fa-comments-o"></i> Summary of recommendation
                  <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the </p>
                </li>
                -->
                <li><i class=" fa fa-user-md"></i>Doctor that responded <span><?php echo $val->title." ".$val->fname." ".$val->lname ?></span></li>
                <!--<li><i class="fa fa-money"></i> Session Charge <span>500</span></li>
                <li><i class="fa fa-medkit"></i> Prescription provided (Yes/No) <span>yes</span>
                  <a href="#">View full conversation</a>  
                </li> -->
            </ul>
          
          </div>         
        
       <?php
        }
       ?> 
        </div>
   <!----------------MAIN CONTAINER--------------------------->
