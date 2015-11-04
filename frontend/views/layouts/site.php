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
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <meta charset="<?= Yii::$app->charset ?>">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <link href="<?php echo \Yii::getAlias('@web') ?>/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo \Yii::getAlias('@web') ?>/css/font-awesome.min.css" rel="stylesheet">
        <link href="<?php echo \Yii::getAlias('@web') ?>/css/prettify-1.0.css" rel="stylesheet">
        <link href="<?php echo \Yii::getAlias('@web') ?>/css/base.css" rel="stylesheet">
        <link href="<?php echo \Yii::getAlias('@web') ?>/css/bootstrap-datetimepicker.css" rel="stylesheet">
        <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
        <link href="<?php echo \Yii::getAlias('@web') ?>/css/style.css" rel="stylesheet">
        <?php $this->head() ?>
    </head>
    <body>
        <?php $this->beginBody() ?>
        <header id="header">
            <div class="hdr">
                <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
                    <div class="container">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="#"><img src="<?php echo \Yii::getAlias('@web') ?>/images/logo.png" alt="logo" class="img-responsive"></a>
                        </div>
                        <div class="navbar-collapse collapse">
                            <ul class="nav navbar-nav navbar-right">
                                <li><a href="<?php echo Url::toRoute('/consultation/create') ?>" title="Consult a Doctor"><img src="<?php echo \Yii::getAlias('@web') ?>/images/consult-a-doctor-top.png" alt="consult a doctor"></a></li>
                                <!--
                                <li><a href="#" title="Search a Doctor"><img src="<?php echo \Yii::getAlias('@web') ?>/images/search-a-doctor-top.png" alt="search a doctor"></a></li> -->
                                <li><a href="#" title="Manage Medical History"><img src="<?php echo \Yii::getAlias('@web') ?>/images/manage-medical-history-top.png" alt="manage medical history"></a></li>
                                <li><a href="#" title="Make Donation"><img src="<?php echo \Yii::getAlias('@web') ?>/images/make-donation-top.png" alt="make donation"></a></li>
                                <a href="#" class="logout"><img src="<?php echo \Yii::getAlias('@web') ?>/images/logout.png" alt="logout"> Logout</a>
                            </ul>
                        </div>
                    </div>
                </nav>
            </div>
        </header>
        <div class="container">
            <div class="row row-offcanvas row-offcanvas-left ">
                
                <!----------------LEFT SIDERBAR--------------------------->
                <div class="col-sm-4 col-xs-12 sidebar-offcanvas lt-col" id="sidebar" role="navigation">
                    
                    <!----- EDIT PROFILE-------->
                    <div class="edit-profile">
                        <img src="<?php echo \Yii::getAlias('@web') ?>/images/jhon.png">
                        <p>John Doe</p>
                        <h6>Edit profile</h6>
                        <div class="clearfix"></div>
                    </div>
                    <!-----/EDIT PROFILE-------->
                    
                    <!-----CURRENT/ACTIVE SUBSCRIPTION-------->
                    <div class="current-sub">
                        <img src="<?php echo \Yii::getAlias('@web') ?>/images/current-subscription-img.png">
                        <p>Current/active subscription</p>
                        <h4>free minutes</h4>
                        <h5>Subscription Expired</h5>
                        <a href="#">Renew/Change Subscription</a>
                        <div class="clearfix"></div>
                    </div>
                    <!-----/CURRENT/ACTIVE SUBSCRIPTION-------->
                    
                    
                    
                    <div class="accordion">
                        <div class="accordion-head col-xs-12 active"><span><img src="<?php echo \Yii::getAlias('@web') ?>/images/consult-a-doctor.png" alt="consult a doctor"></span><a href="<?php echo Url::toRoute('/consultation/create') ?>">Consult a doctor</a></div>
                        <div class="accordion-head col-xs-12"><span><img src="<?php echo \Yii::getAlias('@web') ?>/images/search-a-doctor.png" alt="search a doctor"></span><a href="#">Search a doctor</a></div>
                        <div class="accordion-head col-xs-12"><span><img src="<?php echo \Yii::getAlias('@web') ?>/images/manage-medical-history.png" alt="manage medical history"></span><a href="#">Manage Medical History</a></div>
                        <div class="accordion-head col-xs-12"><span><img src="<?php echo \Yii::getAlias('@web') ?>/images/make-payment.png" alt="make payment"></span><a href="#">Make Payment</a></div>
                        <div class="accordion-head col-xs-12"><span><img src="<?php echo \Yii::getAlias('@web') ?>/images/view-transictions.png" alt="view transictions"></span><a href="#">View Transactions</a></div>
                        <div class="accordion-head col-xs-12"><span><img src="<?php echo \Yii::getAlias('@web') ?>/images/consultance-history.png" alt="consultance history"></span><a href="#">Consultation History</a></div>
                        <div class="accordion-head col-xs-12">
                            <span><img src="<?php echo \Yii::getAlias('@web') ?>/images/subscription.png" alt="subscription"></span><p>Subscription</p><div class="arrow down"></div>
                        </div>
                        <div class="accordion-body col-xs-12  no-pad">
                            <ul>
                                <li><a href="#">View Subscription Plan</a></li>
                                <li><a href="#">Change/Renew Subtscription</a></li>
                                <li><a href="#">Add  subscription package.</a></li>
                            </ul>
                        </div>
                        <div class="accordion-head col-xs-12"><span><img src="<?php echo \Yii::getAlias('@web') ?>/images/make-donation.png" alt=""></span><a href="#">Make Donation</a></div>
                        <div class="clearfix"></div>
                    </div>
                    
                </div>
                <!-- --------------/LEFT SIDERBAR------------------------- -->
                <!-- --------------MAIN CONTAINER------------------------- -->
                
                
                
                <?= $content ?>
                
                
                <!-- --------------MAIN CONTAINER------------------------- -->
            </div>
        </div>
        <div class="clearfix"></div>
        <footer id="footer">
            <div class="ftr">
                <p>Copyright © 2015 All Rights Reserved By  phone a doctor</p>
            </div>
            
        </footer>
        
        <!-- script references -->
        <script type="text/javascript" src="<?php echo \Yii::getAlias('@web') ?>/js/jquery-2.1.1.min.js"></script>
        <!--<script src="<?php echo \Yii::getAlias('@web') ?>/js/jquery.min.js"></script>-->
        
        <script src="<?php echo \Yii::getAlias('@web') ?>/js/bootstrap.min.js"></script>
        <script src="<?php echo \Yii::getAlias('@web') ?>/js/scripts.js"></script>
        
        <script src="<?php echo \Yii::getAlias('@web') ?>/js/moment-with-locales.js"></script>
        <script src="<?php echo \Yii::getAlias('@web') ?>/js/bootstrap-datetimepicker.js"></script>
        <script>
        $(function(){
        $("#header").load("header.html");
        $("#footer").load("footer.html");
        });
        </script>
        
        <script type="text/javascript">
        
        $(function () {
        $('#datetimepicker2').datetimepicker({
        icons: {
        time: "fa fa-clock-o",
        date: "fa fa-calendar",
        up: "fa fa-arrow-up",
        down: "fa fa-arrow-down"
        }
        });
        });
        
        $(function () {
        $('#datetimepicker3').datetimepicker({
        format: 'LT'
        });
        });
        </script>
    </div>
</div>
<?php $this->endBody() ?>
</body>
<?php $this->endPage() ?>