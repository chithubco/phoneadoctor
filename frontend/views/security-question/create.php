<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SecurityQuestion */

$this->title = 'Create Security Question';
$this->params['breadcrumbs'][] = ['label' => 'Security Questions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="security-question-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
