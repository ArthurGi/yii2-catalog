<?php
use yii\helpers\Html;
use app\components\ImageHelper;

/* @var $productItem \app\modules\catalog\components\ProductItem */
?>
<div class="container product-body">
    <div class="row">
        <div class="col-xl-6 col-lg-12 product-images">
            <div class="row">
                <div class="col-12 col-md-12 col-xl-12">
                    <div class="card-cos__wrap">
                        <div class="card-cos__nav-wrap">
                            <div class="card-cos__arrows">
                                <div class="card-cos__prev">
                                    <img src="/web/img/arrow_right.svg" alt="alt">
                                </div>
                                <div class="card-cos__counter">
                                    <span class="card-cos__first"></span>
                                    <span class="card-cos__second"></span>
                                </div>
                                <div class="card-cos__next">
                                    <img src="/web/img/arrow_right.svg" alt="alt">
                                </div>
                            </div>
                        </div>
                        <div class="card-cos__slider">
                            <?php
                            foreach ($productItem->getAllImages() as $image) {
                                $preview = ImageHelper::thumbnail($image['src'], 70, 70, ImageHelper::THUMBNAIL_RESIZE_AND_CROP);
                                ?>
                                <div data-thumb="<?= $preview ?>" class="crad-cos__item"
                                     data-offer="<?= $image['offer'] ?>">
                                    <img src="<?= $image['src']; ?>" alt="alt">
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-lg-12 product-description">
            <div class="card-script">
                <!--div class="card-script__acritcle">
                    Артикул: 00351266
                </div-->
                <h1 class="card-script__title">
                    <?= isset($metaData['title']) ? $metaData['title'] : $productItem->getProduct()->getName(); ?>
                </h1>
                <div class="card-script__body">
                    <?= $productItem->getProduct()->getShortDsc(); ?>
                </div>
                <form method="GET" action="" class="offer-params-form">
                    <?= Html::hiddenInput('product', $productItem->getProduct()->id, ['class' => 'current-product-id']); ?>
                    <?= Html::hiddenInput('offer', $productItem->getOffer()->id, ['class' => 'current-offer-id']); ?>
                    <?= Html::hiddenInput('changed-property-id', '', ['class' => 'changed-property-id']); ?>
                    <?php
                    foreach ($productItem->getOfferParams() as $param) {
                        ?>
                        <div class="card-script___radio-item">
                            <span><?= $param['name'] ?>:</span>
                            <?= implode(';&nbsp;&nbsp;', array_map(function ($data) {
                                $id = uniqid('r');
                                return Html::radio("param[{$data['paramId']}]", $data['checked'], [
                                        'value' => $data['value'],
                                        'class' => 'myradio',
                                        'id' => $id,
                                        'data-param-id' => $data['paramId']
                                    ])
                                    . Html::label($data['name'], $id, ['class' => 'first-label']);
                            }, $param['values'])) ?>
                        </div>
                        <?php
                    }
                    ?>
                </form>
                <div class="card-script___kolvo">
                    <div class="kol">
                        <div class="number basket-item-block-amount">
                            <b><?= \yii::t('modules/catalog/app', 'Count') ?></b>
                            <span class="minus" data-entity="basket-item-quantity-minus"></span>
                            <input type="text" size="5" class="basket-item-amount-filed" value="1 шт" data-value="1"
                                   data-entity="basket-item-quantity-field" id="basket-item-quantity-29359">
                            <span class="plus" data-entity="basket-item-quantity-plus"></span>
                        </div>
                    </div>
                </div>
                <div class="card-script__price">
                    <?= $productItem->getPriceWithCurency(); ?>
                </div>
                <div class="card-script__butt">
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
                    <?= \app\modules\catalog\widgets\OneClickPurchase::widget(['product' => $productItem->getProduct(), 'offer' => $productItem->getOffer()]); ?>
                </div>
                <div class="card-script__sosial">
                        <?php $favorite = \app\modules\catalog\models\Favorite::checkFavoriteForUser($productItem->product->id); ?>
                        <?= Html::a('<img src="/web/img/soc_icon_1.svg" alt="alt" class="fav-not-active"><img src="/web/img/soc_icon_1_active.svg" alt="alt" class="fav-active">', ['cart/add-favorite'], [
                            'class' => $favorite ? 'add-to-favorite-btn active' : 'add-to-favorite-btn',
                            'data' => [
                                'method' => 'post',
                                'params' => [
                                    'id' => $productItem->product->id,
                                ],
                            ],
                        ]); ?>
                  <a href="#" class="js-fb-share"><img src="/web/img/soc_icon_3.svg" alt="alt"></a>
                  <a href="#" class="js-vk-share"><img src="/web/img/soc_icon_2.svg" alt="alt"></a>
                </div>
            </div>
        </div>
    </div>
</div>
