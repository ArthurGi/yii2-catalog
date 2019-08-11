<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
/* @var $info array */
$id = uniqid('f');
?>
<div class="filter-item">
  <?=Html::label($info['name'], $id)?>
  <?=Html::textInput($info['alias']."[from]", \yii\helpers\ArrayHelper::getValue($info['value'], 'from', $info['min']), ['class' => 'form-control'])?>
  <?=Html::textInput($info['alias']."[to]", \yii\helpers\ArrayHelper::getValue($info['value'], 'to', $info['max']), ['class' => 'form-control'])?>
</div>

