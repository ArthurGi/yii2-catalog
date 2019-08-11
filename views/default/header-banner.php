<?php

/* @var $this \yii\web\View */
/* @var $category app\modules\catalog\models\Category */
if(!$category->getImagePath()) {
  return '';
}
?>
<div class="header-banner">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <div class="body_banner">
            <div class="header-banner__img">
                <?= \yii\helpers\Html::img($category->getImagePath(), ['alt' => $category->getName()])?>
            </div>
            <div class="header-banner__title"><?=$category->getName();?></div>
        </div>
      </div>
    </div>
  </div>
</div>