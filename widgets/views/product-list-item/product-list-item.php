<?php
use app\modules\catalog\components\ProductItem;
use yii\helpers\Html;
use app\components\ImageHelper;
/* @var $this \yii\web\View */
/* @var $product app\modules\catalog\models\Product */
/* @var $productItem ProductItem */


$mainImg = $productItem->getMainImg();
$detailPageUrl = app\modules\catalog\components\ProductHelper::getDetailPageUrl($productItem->getProduct());
?>
<div class="col-12 col-md-6 col-xl-4 product-item">
  <div class="carousel-two__item">
      <div class="carousel-two__item__wrap">
          <div class="carousel-two__item__img">
              <a href="<?=$detailPageUrl;?>">
                <?=Html::img(ImageHelper::thumbnail($mainImg['src'], 312, 312, ImageHelper::THUMBNAIL_FORCE_ASPECT_RATIO), 
                                [
                                    'alt' => $productItem->getName(),
                                    'data-full' => $mainImg['src'],
                                    'data-offer' => $mainImg['offer']
                                ]);?>
              </a>
            <?=$productItem->isSale() ? Html::tag('div', 'sale', ['class' => 'malina-catalog__sale']) : '';?>
            <?=$productItem->isNew() ? Html::tag('div', 'new', ['class' => 'malina-catalog__new']) : '';?>
          </div>
          <div class="carousel-two__item__content">
            <p title="<?= Html::encode($productItem->getName())?>">
                    <?= \app\components\StringHelper::short($productItem->getName(), 56);?>
            </p>
            <span><?=$productItem->getPriceWithCurency();?></span>
          </div>
      </div>
      <div class="carousel-two__item__wrap  active-hover">
          <div class="carousel-two__item__img">
            <a href="<?=$detailPageUrl;?>">
                <?=Html::img(ImageHelper::thumbnail($mainImg['src'], 312, 312, ImageHelper::THUMBNAIL_FORCE_ASPECT_RATIO), 
                              [
                                  'alt' => $productItem->getName(),
                                  'data-full' => $mainImg['src'],
                                  'data-offer' => $mainImg['offer'],
                                  'class' => 'js-product-item-main-img'
                              ]);?>
            </a>
            <?=$productItem->isSale() ? Html::tag('div', 'sale', ['class' => 'malina-catalog__sale']) : '';?>
            <?=$productItem->isNew() ? Html::tag('div', 'new', ['class' => 'malina-catalog__new']) : '';?>
          </div>
          <div class="carousel-two__item__content">
              <p title="<?= Html::encode($productItem->getName())?>">
                    <?=\app\components\StringHelper::short($productItem->getName(), 56);?>
              </p>
              
                <div class="carousel-two__form-group">
                  <form method="GET" action="" class="offer-params-form">
                  <?= Html::hiddenInput('product', $productItem->getProduct()->id, ['class' => 'current-product-id']); ?>
                  <?= Html::hiddenInput('offer', $productItem->getOffer()->id, ['class' => 'current-offer-id']); ?>
                  <?= Html::hiddenInput('changed-property-id', '', ['class' => 'changed-property-id']); ?>
                  <?php
                  foreach($productItem->getOfferParams() as $offerParam)
                  {
                    ?>
                      <div class="offerParamName"><?=$offerParam['name'];?>:</div>
                      <div class="carousel-two__radio-item">
                        <?php
                        foreach($offerParam['values'] as $offerParamValue)
                        {
                          $radioId = uniqid('offer');
                          echo Html::radio("param[{$offerParamValue['paramId']}]", $offerParamValue['checked'], [
                              'id' => $radioId, 
                              'value' => $offerParamValue['value'],
                              'data-param-id' => $offerParamValue['paramId'],
                              'class' => 'offer-param-value js-product-item-offer-param']);
                          echo Html::label($offerParamValue['name'], $radioId, [
                              'class' => $offerParamValue['checked'] ? 'active' : 0
                          ]);
                        }
                         ?> 
                      </div>
                    <?php
                  }
                  ?>
                  </form>
                  <div class="checkmark"><?=$productItem->getPriceWithCurency();?></div>
                </div>
              <?= Html::a(\yii::t('modules/catalog/app', 'В корзину'), ['cart/add'], [
                  'class' => 'my-btn add-to-cart-btn',
                  'data' => [
                      'method' => 'post',
                      'params' => [
                          'offer_id' => $productItem->offer->id,
                      ],
                  ],
              ]);
              ?>
          </div>
          <div class="heart">
              <?= Html::a('<img src="/web/img/icon_head_3.svg" alt="alt">',  ['cart/add-favorite'], [
                      'class' => \app\modules\catalog\models\Favorite::checkFavoriteForUser($productItem->product->id) ? 'add-to-favorite-btn': 'add-to-favorite-btn active',
                      'data' => [
                          'method' => 'post',
                          'params' => [
                              'id' => $productItem->product->id,
                          ],
                      ],
              ]);?>
          </div>
          <div class="minny-slider-wrap">
              <div class="minny-slider">
                <?php
                foreach($productItem->getAllImages() as $imgArray) 
                {
                  ?>
                  <div class="minny-slider__item">
                      <a href="#" >
                        <?= Html::img(
                                ImageHelper::thumbnail($imgArray['src'], 74, 74, ImageHelper::THUMBNAIL_FORCE_ASPECT_RATIO), 
                                [
                                    'alt' => $productItem->getName(),
                                    'data-full' => $imgArray['src'],
                                    'data-image' => ImageHelper::thumbnail($imgArray['src'], 312, 312, ImageHelper::THUMBNAIL_FORCE_ASPECT_RATIO),
                                    'data-offer' => $imgArray['offer']
                                ]);?>
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

