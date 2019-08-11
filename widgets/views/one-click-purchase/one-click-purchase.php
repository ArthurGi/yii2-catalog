<?php
use yii\helpers\Html;
/* @var $product app\modules\catalog\models\Product */
/* @var $offer app\modules\catalog\models\Offer */
/* @var $model app\modules\catalog\models\OneClickPurchaseForm */
$model->formId = ($model->formId ? $model->formId : uniqid('form-')); 
?>
<a href="#" class="one-click-buy-btn btn btn-outline-primary d-flex align-items-center justify-content-center" data-form-id="<?=$model->formId?>" data-pjax="0">
        <?=\yii::t('modules/catalog/app', 'One-click buying')?>
</a>
<div style="display: none;">
  <div class="one-click-buy-popup popup-inner">
    <a href="#" class="form-close close-form-btn-js">╳</a>
    <div class="one-click-buy-popup-body">
      <?=Html::beginForm(yii\helpers\Url::to(['/catalog/api/one-click-purchase/']), 'POST', ['class' => 'one-click-buy-form', 'id' => $model->formId, 'data-pjax' => 0]);?>
        <div class="title_form"><?=\yii::t('modules/catalog/app', 'One-click buying')?></div>
        <?=Html::activeHiddenInput($model, 'product_id');?>
        <?=Html::activeHiddenInput($model, 'offer_id');?>
        <?=Html::activeHiddenInput($model, 'formId');?>
        <div class="form-item">
          <?=Html::activeTextInput($model, 'name', ['class' => 'form-input']);?>
          <?=Html::activeLabel($model, 'name', ['class' => 'form-label']);?>
          <div class="error"><?=$model->getFirstError('name');?></div>
        </div>
        <div class="form-item">
          <?=Html::activeTextInput($model, 'phone', ['class' => 'form-input']);?>
          <?=Html::activeLabel($model, 'phone', ['class' => 'form-label']);?>
          <div class="error"><?=$model->getFirstError('phone');?></div>
        </div>
        <div class="form-item">
          <?=Html::activeTextArea($model, 'comment', ['class' => 'form-input']);?>
          <?=Html::activeLabel($model, 'comment', ['class' => 'form-label']);?>
          <div class="error"><?=$model->getFirstError('comment');?></div>
        </div>
        <?=Html::submitButton(\yii::t('modules/catalog/app', 'Send'), ['class' => 'btn btn-primary']);?>
        <a href="#" class="btn cancel btn-outline-primary close-form-btn-js"><?=\yii::t('modules/catalog/app', 'Cancel')?></a>
      <?=Html::endForm();?>
    </div>
  </div>
</div>


