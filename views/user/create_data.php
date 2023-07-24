<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Create Data';
$this->params['breadcrumbs'][] = ['label' => 'Dashboard', 'url' => ['site/dashboard']];
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<div class="data-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'attribute1')->textInput() ?>
    <?= $form->field($model, 'attribute2')->textInput() ?>>
    <?= $form->field($model, 'attribute3')->textInput() ?>
    <?= $form->field($model, 'attribute4')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
