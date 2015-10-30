<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="http://localhost/phoneadoc/Login_files/bootstrap.min.css" rel="stylesheet">
    <link href="http://localhost/phoneadoc/Login_files/font-awesome.min.css" rel="stylesheet">
    
    <link href="http://localhost/phoneadoc/Login_files/style.css" rel="stylesheet">
    <?php $this->head() ?>
</head>
<body>
    <div class="container">
    <?php $this->beginBody() ?>
        <div class="row">
        <?= $content ?>
            
        </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="Login_files/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="Login_files/bootstrap.min.js"></script>
  <?php $this->endBody() ?>
</body>



<?php $this->endPage() ?>
