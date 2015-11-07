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
        <link href="<?php echo \Yii::getAlias('@web') ?>/css/bootstrap.min.css" rel="stylesheet">
        <link href="<?php echo \Yii::getAlias('@web') ?>/css/style.css" rel="stylesheet">
        <link href="<?php echo \Yii::getAlias('@web') ?>/css/font-awesome.min.css" rel="stylesheet">
        
        
        <?php $this->head() ?>
    </head>
    <body>
    <?php $this->beginBody() ?>
        
                <?= $content ?>
                
            
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- Include all compiled plugins (below), or include individual files as needed -->
        <script src="<?php echo \Yii::getAlias('@web') ?>/js/bootstrap.min.js"></script>
        <?php $this->endBody() ?>
    </body>
</html>
    <?php $this->endPage() ?>