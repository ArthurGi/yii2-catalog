<?php
/* @var $products app\modules\catalog\models\Product[] */
foreach($products as $product) {
  ?>
  <?=$product->getName();?>
    <?php
}

