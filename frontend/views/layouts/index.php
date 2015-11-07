<?php
/* @var $this \yii\web\View */
/* @var $content string */
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;
use yii\helpers\Url;
AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="PhoneADoctor">
        <meta name="author" content="">
        <link rel="shortcut icon" href="assets/ico/favicon.png">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <!-- Bootstrap core CSS -->
        <link href="<?php echo \Yii::getAlias('@web') ?>/assets/css/bootstrap.css" rel="stylesheet">
        <link href="<?php echo \Yii::getAlias('@web') ?>/css/style.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="<?php echo \Yii::getAlias('@web') ?>/assets/css/main.css" rel="stylesheet">
        <link href="<?php echo \Yii::getAlias('@web') ?>/assets/css/font-awesome.min.css" rel="stylesheet">
        <link href='http://fonts.googleapis.com/css?family=Lato:300,400,700,300italic,400italic' rel='stylesheet' type='text/css'>
        <link href='http://fonts.googleapis.com/css?family=Raleway:400,300,700' rel='stylesheet' type='text/css'>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        
        <script src="<?php echo \Yii::getAlias('@web') ?>/assets/js/modernizr.custom.js"></script>
        
        <!--Start of Zopim Live Chat Script-->
        <script type="text/javascript">
        window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
        d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
        _.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
        $.src='//v2.zopim.com/?2klhV8KoUXoedvQXXWZ37dcKDK9x6ahZ';z.t=+new Date;$.
        type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
        </script>
        <!--End of Zopim Live Chat Script-->
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <!-- Menu -->
        <nav class="menu" id="theMenu">
            <div class="menu-wrap">
                <h1 class="logo"><a href="<?php echo Url::toRoute('/site/index') ?>#home"><img src="<?php echo \Yii::getAlias('@web') ?>/assets/img/logotxt.png"></a></h1>
                <!--<i class="fa fa-arrow-right menu-close"></i>-->
                <a href="<?php echo Url::toRoute('/site/index') ?>#welcome">What we do</a>
                <a href="<?php echo Url::toRoute('/site/about') ?>">Who we are</a>
                <a href="<?php echo Url::toRoute('/site/services') ?>">How to use the service</a>
                <a href="#">Added Service</a>
                <a href="#">Our Partners</a>
                <a href="#contact">Contact</a>
                <a href="https://www.facebook.com/Phoneadoctor-796603310409830/timeline/"><i class="fa fa-facebook"></i></a>
                <a href="https://twitter.com/PhoneADr"><i class="fa fa-twitter"></i></a>
                <a href="#"><i class="fa fa-dribbble"></i></a>
                <a href="mailto: help@phoneadoctor"><i class="fa fa-envelope"></i></a>
            </div>
            
            <!-- Menu button -->
            <div id="menuToggle"><i class="fa fa-bars"></i></div>
        </nav>
        
        <?= $content ?>
        <div class="container">

        <div class="row mt">

            <div class="col-lg-12">

                <h1>Stay Connected</h1>

                <p>Join us on our social networks for all the latest updates, product/service announcements and more.</p>

                <br>

            </div><!-- col-lg-12 -->

        </div><!-- row -->

    </div><!-- container -->

    

    

    <!-- SOCIAL FOOTER --->

    <section id="contact"></section>

    <div id="sf">

        <div class="container">

            <div class="row">

                <div class="col-lg-4 dg">

                    <h4 class="ml">FACEBOOK</h4>

                    <p class="centered"><a href="https://www.facebook.com/pages/Phoneadoctor/796603310409830"><i class="fa fa-facebook"></i></a></p>

                    <p class="ml">> Become A Friend</p>

                </div>

                <div class="col-lg-4 lg">

                    <h4 class="ml">TWITTER</h4>

                    <p class="centered"><a href="https://twitter.com/PhoneADr"><i class="fa fa-twitter"></i></a></p>

                    <p class="ml">> Follow Us</p>

                </div>

                <div class="col-lg-4 dg">

                    <h4 class="ml">GOOGLE +</h4>

                    <p class="centered"><a href="#"><i class="fa fa-google-plus"></i></a></p>

                    <p class="ml">> Add Us To Your Circle</p>

                </div>

            </div><!-- row -->

        </div><!-- container -->

    </div><!-- Social Footer -->

    

    <!-- CONTACT FOOTER --->

    <div id="cf">

        <div class="container">

            <div class="row">

                <div class="col-lg-8">

                    <div id="mapwrap">

                        <iframe height="400" width="100%" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d7927.045060151827!2d3.3853204!3d6.5817745!3m2!1i1024!2i768!4f13.1!5e0!3m2!1sen!2sng!4v1422375241969"></iframe>

                    </div>  

                </div><!--col-lg-8-->

                <div class="col-lg-4">

                    <h4>ADDRESS<br/>Our Head Office</h4>

                    <br>

                    <p>

                        Hills View Estate, Ogudu G.R.A<br/>

                        Lagos.

                    </p>

                    <p>

                        P: 0700DOCTOR<br/>

                        F: +234 080-4333-4345<br/>

                        E: <a href="mailto:info@phoneadoctor.com.ng">info@phoneadoctor.com.ng</a>

                    </p>

                    <p>For more information, contact us today.</p>

                </div><!--col-lg-4-->

            </div><!-- row -->

        </div><!-- container -->

    </div><!-- Contact Footer -->
        
         <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
        <script src="<?php echo \Yii::getAlias('@web') ?>/assets/js/bootstrap.min.js"></script>
        <script src="<?php echo \Yii::getAlias('@web') ?>/assets/js/main.js"></script>
        <script src="<?php echo \Yii::getAlias('@web') ?>/assets/js/masonry.pkgd.min.js"></script>
        <script src="<?php echo \Yii::getAlias('@web') ?>/assets/js/imagesloaded.js"></script>
        <script src="<?php echo \Yii::getAlias('@web') ?>/assets/js/classie.js"></script>
        <script src="<?php echo \Yii::getAlias('@web') ?>/assets/js/AnimOnScroll.js"></script>
        <script>
        new AnimOnScroll( document.getElementById( 'grid' ), {
        minDuration : 0.4,
        maxDuration : 0.7,
        viewportFactor : 0.2
        } );
        </script>
        <?php $this->endBody() ?>
       
    </body>
</html>
<?php $this->endPage() ?>