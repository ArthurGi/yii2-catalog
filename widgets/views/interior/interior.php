<?php
/* @var $products app\modules\catalog\models\Product[] */
foreach($interiors as $interior) {
  ?>
  <?=$interior->getName();?>
    <?php
}
