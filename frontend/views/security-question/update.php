<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SecurityQuestion */

$this->title = 'Update Security Question: ' . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Security Questions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="security-question-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
