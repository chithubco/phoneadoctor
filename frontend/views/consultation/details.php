<?php
/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
$this->title = 'Consultation Details';
?>


<!- ---------------MAIN CONTAINER------------------------- -->
        <?php
        	$start = explode(" ", $details->start);
        	$start_time = $start[1];
        	$start_date = $start[0];

        	$end = explode(" ", $details->end);
        	$end_time = $end[1];
        	$end_date = $end[0];

        ?>
        <div class="col-sm-8 main-container whn-phn">          
          <!--toggle sidebar button-->
          <p class="visible-xs">
            <button type="button" class="btn btn-primary btn-xs" data-toggle="offcanvas"><i class="glyphicon glyphicon-chevron-left"></i></button>
          </p> 
          
          <h2>your schedule for consultation  </h2>          
          <div class="calling-dctr">
          	<span>Date: <?php echo $start_date ?></span><span>Time: <?php echo $start_time ?></span>
            <h3>Doctor you would be speaking to</h3>
            <img src="<?php echo \Yii::getAlias('@web') ?>/doctorimages/<?php echo $details->image ?>.png" alt="" width="150px"  >
            <p><?php echo $details->doctor ?></p>
            <h3>Identification Code for the session</h3>
            <h5>#<?php echo $details->code ?></h5>            
          </div>
          
        </div>
        
        
	 <!-- --------------MAIN CONTAINER------------------------- -->