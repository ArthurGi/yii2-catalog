<?php
use yii\helpers\Html;
use app\modules\catalog\components\ProductHelper;
use app\modules\catalog\components\ProductItem;
use app\components\ImageHelper;

$pageId = \app\components\SiteHelper::getPageIdByHandler('catalog/default/index');
?>
<section class="sect-new good-width similar-products-block">
  <div class="container">
    <div class="row">
      <div class="col-12 col-md-7 col-xl-7">
        <div class="h2 h2-card">
          <h2><?=\yii::t('modules/catalog/app', 'Similar products');?></h2>
        </div>
      </div>
      <div class="col-12 d-md-block col-md-5 col-xl-5 to_down_fixed">
        <div class="carousel-two__nav-wrap">
          <div class="carousel-two__arrows">
            <div class="my-car-prev">
                <img src="/web/img/arrow_right.svg" alt="alt">
            </div>
            <div class="sect-new__counter">
              <span class="counter__first"></span>
              <span class="counter__second"></span>
            </div>
            <div class="my-car-next">
              <img src="/web/img/arrow_right.svg" alt="alt">
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="carousel_block">
          <div class="bx-wrapper">
            <div class="bx-viewport">
              <div class="carousel similar-products-carousel">
                <?php foreach($similarProducts as $similarProduct) {
                  $productItem = new ProductItem(['product' => $similarProduct]);
                  $mainImg = $productItem->getMainImg();
                  ?>
                
                      <div class="carousel-two__item">
                          <a href="<?=ProductHelper::getDetailPageUrl($similarProduct)?>">
                              <div class="carousel-two__item__img">
                                  <?=Html::img(ImageHelper::thumbnail($mainImg['src'], 312, 312, ImageHelper::THUMBNAIL_FORCE_ASPECT_RATIO), 
                              [
                                  'alt' => $productItem->getName(),
                                  'data-full' => $mainImg['src'],
                                  'data-offer' => $mainImg['offer']
                              ]);?>
                              </div>
                              <div class="carousel-two__item__content">
                                  <p><?=$productItem->getName();?></p>
                                              <br/>
                                  <span><?=$productItem->getPriceWithCurency();?></span>
                              </div>
                          </a>
                      </div>
                <?php
                }
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

