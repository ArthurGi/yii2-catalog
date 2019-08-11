<?php
/* @var $this \yii\web\View */
/* @var $products app\modules\catalog\models\Product[] */
?>
<div class="malina-catalog row stock-products">
  <?php
  foreach($products as $product) {
    echo \app\modules\catalog\widgets\ProductListItem::widget(['product' => $product]);
  }
  ?>
</div>

