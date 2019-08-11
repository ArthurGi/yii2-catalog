<?php
use app\components\ImageHelper;


foreach($products as $product) {
  $productItem = new \app\modules\catalog\components\ProductItem(['product' => $product]);
  $imgUrl = ImageHelper::thumbnail($productItem->getMainImg()['src'], 230, 230, ImageHelper::THUMBNAIL_FORCE_ASPECT_RATIO);
  ?>
    <a class="submenu_item_link" href="<?=\app\modules\catalog\components\ProductHelper::getDetailPageUrl($productItem->getProduct())?>">
        <div class="top-menu__submenu__item" style="background: url('<?=$imgUrl;?>') no-repeat center; background-size: cover">
            <?php
            if($productItem->isNew()) { ?>
              <div class="top-menu__submenu__item__anons a_new">New</div>
              <?php
            }?>
            <?php
            if($productItem->isSale()) { ?>
              <div class="top-menu__submenu__item__anons a_sale">Sale</div>
              <?php
            }?>
            <div class="top-menu__submenu__item__content">
              <p><?=$productItem->getName();?></p>
            </div>

        </div>
    </a>
  <?php
}
