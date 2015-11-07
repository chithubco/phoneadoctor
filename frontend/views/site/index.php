<?php
use yii\helpers\Url;
/* @var $this yii\web\View */
$this->title = 'PhoneADoctor';
?>
<!-- MAIN IMAGE SECTION -->
<div id="headerwrap">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="col-lg-8">
                    <h1><img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/phone-a-doctor-banner-icon.png" class="img-responsive"></h1>
                    <h2 style="color: #e8151b; font-size:43px; font-weight:normal" align="center">All You Have To Do Is</h2>
                    <a href="<?php echo Url::toRoute('/site/signup') ?>" class="free">Sign Up For Free</a>
                    <div class="contact-detail">
                        <h4>Call Us! 0700DOCTOR </h4>
                        <a href="callto://phoneadoctor"><i class="fa fa-skype"></i></a>
                        <a href="mailto: help@phoneadoctor"><i class="fa fa-envelope"></i></a>
                        <a href="https://twitter.com/@phoneadoctor"><i class="fa fa-twitter"></i></a>
                        <a href="https://www.facebook.com/Phoneadoctor-796603310409830/timeline/"><i class="fa fa-facebook"></i></a>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <i style="color:#C00" class="fa fa-angle-down"></i>
        </div><!-- row -->
    </div><!-- /container -->
</div><!-- /headerwrap -->


    <!-- WELCOME SECTION -->

    <div id="welcome">

    <div class="container">

      <div class="row mt">

        <div class="col-lg-8">

            <h1>We Provide affordable, accessible and available health care delivery to everyone</h1>

            <p>No matter where you are or your financial status, we provide inclusive health care delivery. Anyone from any where can use our service to have first class medical consultation using the channels you are already familiar with. Try if for free today!</p>

        </div>

        <div class="col-lg-4">

            <p class="pull-right"><br><a href="<?php echo Url::toRoute('/site/about') ?>"><button type="button" class="btn btn-danger">Read More</button></a></p>

        </div>

      </div><!-- /row -->

    </div><!-- /.container -->

    </div>

    

    <!-- PORTFOLIO SECTION -->

    <div id="portfolio">

        <div class="container">

            <div class="row mt">

                <ul class="grid effect-2" id="grid">

                    <li><a href="<?php echo Url::toRoute('/site/services') ?>"><img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/portfolio/sickperson.jpg"></a></li>

                    <li><a href="<?php echo Url::toRoute('/site/services') ?>"><img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/portfolio/cards.jpg"></li>

                    <li><a href="<?php echo Url::toRoute('/site/services') ?>"><img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/portfolio/callingscreen.jpg"></a></li>

                    <li><a href="<?php echo Url::toRoute('/site/services') ?>"><img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/portfolio/diagnose.jpg"></a></li>

                    <li><a href="<?php echo Url::toRoute('/site/services') ?>"><img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/portfolio/doctor.jpg"></a></li>

                    <li><a href="<?php echo Url::toRoute('/site/services') ?>"><img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/portfolio/prescribe.jpg"></a></li>

                    <li><a href="<?php echo Url::toRoute('/site/services') ?>"><img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/portfolio/patient.jpg"></a></li>

                </ul>

            </div><!-- row -->

        </div><!-- container -->

    </div><!-- portfolio -->





    <!-- SERVICES SECTION -->

    <div id="services">

        <div class="container">

            <div class="row mt">

                <div class="col-lg-1 centered">

                    <i class="fa fa-certificate"></i>

                </div>

                <div class="col-lg-3">

                    <h3>Who Is It For?</h3>

                    <p>Phone A Doctor is a medical service which has been made available for you and your family. So wherever you may be we are just a touch of a button away.</p>

                </div>



                <div class="col-lg-1 centered">

                    <i class="fa fa-question-circle"></i>

                </div>

                <div class="col-lg-3">

                    <h3>Staff Support</h3>

                    <p>We provide you with Nigerian highly trained, accredited doctors, clinicians and consultants, committed to improving the healthcare and well-being of all our clients. </p>

                </div>

            

            

                <div class="col-lg-1 centered">

                    <i class="fa fa-globe"></i>

                </div>

                <div class="col-lg-3">

                    <h3>Exciting Services</h3>

                    <p>We offer specialist medical services to our clients from non-emergency health issues to those who seek medical consult. With Phone A Doctor, many of your medical issues can be diagnosed instead of visiting time-consuming emergency rooms.</p>

                </div>

            

            </div><!-- row -->

        </div><!-- container -->

    </div><!-- services section -->

    

    

    <!-- BLOG POSTS -->

    <div class="container">

        <div class="row mt">

            <div class="col-lg-12">

                <h1>Phone A Doctor Process</h1>

            </div><!-- col-lg-12 -->

            <div class="col-lg-8">

                <p>Our service process have been simplified for your convenience.</p>

            </div><!-- col-lg-8-->

            <div class="col-lg-4 goright">

                <p><a href="<?php echo Url::toRoute('/site/services') ?>"><i class="fa fa-angle-right"></i> See All Steps to Phone A Doctor</a></p>

            </div>

        </div><!-- row -->

        

        <div class="row mt">

            <div class="col-lg-4">

                <img class="img-responsive" src="<?php echo \Yii::getAlias('@web') ?>/assets/img/doc2.jpg" alt="">

                <h3><a href="#">Gaining access to the service</a></h3>

                <p>In other to get access to the Phone A Doctor service, you need to purchase the airtime voucher from our vendors.  </p>

                <p><a href="<?php echo Url::toRoute('/site/services') ?>"><i class="fa fa-link"></i> Read More</a></p>

            </div>

            <div class="col-lg-4">

                <img class="img-responsive" src="<?php echo \Yii::getAlias('@web') ?>/assets/img/doc3.jpg" alt="">

                <h3><a href="#">Call Phone A Doctor</a></h3>

                <p>After purchasing the voucher, you can then make the call (0800 000 0000) to the doctor to lodge your medical issues.</p>

                <p><a href="<?php echo Url::toRoute('/site/services') ?>"><i class="fa fa-link"></i> Read More</a></p>

            </div>

            <div class="col-lg-4">

                <img class="img-responsive" src="<?php echo \Yii::getAlias('@web') ?>/assets/img/doc4.png" alt="">

                <h3><a href="#">It's that simple</a></h3>

                <p>Once you have clearly described the ailment, you would be suggested a course of treatment or prescription. </p>

                <p><a href="<?php echo Url::toRoute('/site/services') ?>"><i class="fa fa-link"></i> Read More</a></p>

            </div>      

        </div><!-- row -->

    </div><!-- container -->

    

    

    <!-- CLIENTS LOGOS 

    <div id="lg">

        <div class="container">

            <div class="row centered">

                <div class="col-lg-2 col-lg-offset-1">

                    <img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/clients/c01.gif" alt="">

                </div>

                <div class="col-lg-2">

                    <img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/clients/c02.gif" alt="">

                </div>

                <div class="col-lg-2">

                    <img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/clients/c03.gif" alt="">

                </div>

                <div class="col-lg-2">

                    <img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/clients/c04.gif" alt="">

                </div>

                <div class="col-lg-2">

                    <img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/clients/c05.gif" alt="">

                </div>-->

            <!--</div><!-- row -->

        <!--</div><!-- container -->

    <!--</div><!-- dg -->

    

    

    <!-- CALL TO ACTION -->

    <div id="call">

        <div class="container">

            <div class="row">

                <h3>WE WANT TO MAKE A DIFFERENCE</h3>

                <div class="col-lg-8 col-lg-offset-2">

                    <p>Call us today for more services and detailed description of our services. Don't hesitate to make that call as you may just be saving a life.</p>

                    <p><button type="button" class="btn btn-danger btn-lg">Call To 0700DOCTOR</button></p>

                </div>

            </div><!-- row -->

        </div><!-- container -->

    </div><!-- Call to action -->

    
