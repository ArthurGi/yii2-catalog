<?php
use app\components\SiteHelper;
use \yii\helpers\Html;

$metaData = SiteHelper::metaData($this, [$page], [
    'title' => $page->name,
    'meta_title' => $page->name,
    'meta_description' => '',
    'meta_keywords' => '',
]);
if (!isset($this->params['breadcrumbs'])) {
    $this->params['breadcrumbs'] = [];
}
$this->params['breadcrumbs'] = array_merge($this->params['breadcrumbs'], SiteHelper::getCurrentPageBreadcrumbs(false));

?>
<section class="cards">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1 class="main_h1"><?php echo \yii::t('modules/catalog/app', 'Cart'); ?></h1>
            </div>
            <div class="col-12">
                <div style="" class="row podzakaz" id="row-podzakaz">
                    <div class="col-12">
                        <div class="basket-items-list-wrapper basket-items-list-wrapper-height-fixed basket-items-list-wrapper-light"
                             id="basket-items-list-wrapper-podzakaz">
                            <div class="basket-items-list-container" id="basket-items-list-container-podzakaz">
                                <div class="basket-items-list-overlay" id="basket-items-list-overlay-podzakaz"
                                     style="display: none;"></div>

                                <div class="full_basket block_basket_header d-flex">
                                    <div class="">
                                        <?php echo \yii::t('modules/catalog/app', 'Название'); ?>
                                    </div>
                                    <div class="">
                                        <?php echo \yii::t('modules/catalog/app', 'Стоимость'); ?>
                                    </div>
                                    <div class="">
                                        <?php echo \yii::t('modules/catalog/app', 'Количество'); ?>
                                    </div>
                                    <div class="">
                                        <?php echo \yii::t('modules/catalog/app', 'Сумма'); ?>
                                    </div>
                                </div>
                                <?php foreach ($cart->getItems() as $item): ?>
                                    <?php
                                    $product = $item->getProduct();
                                    $offer = $item->getOffer();
                                    ?>
                                    <div class="full_basket block_basket basket-items-list" id="<?php echo 'basket_item_'.$offer->id;?>">
                                        <table class="basket-items-list-table">
                                            <tbody id="basket-items-podzakaz">
                                            <tr class="basket-items-list-item-container">
                                                <td class="basket-items-list-item-descriptions">
                                                    <div class="basket-items-list-item-descriptions-inner block_basket-item d-flex">
                                                        <div class="basket_photo">
                                                            <a href="<?php echo app\modules\catalog\components\ProductHelper::getDetailPageUrl($product);?>">
                                                                <img src="<?php echo \app\components\ImageHelper::thumbnail($product->getImagePath(), 71, 71); ?>"
                                                                     alt="<?php echo $product->name; ?>">
                                                            </a>
                                                        </div>
                                                        <div class="title_cart-item">
                                                            <a href="#"><?php echo $product->name; ?></a>
                                                            <?php /* вывод свойст торгового предложения
                                                            <div class="card-value-wrap">
                                                                <div class="property-wrap">
                                                                    <span class="basket-item-property-size"><?php echo \yii::t('modules/catalog/app', 'Объем'); ?>:</span>
                                                                    <div class="basket-item-property-size-value">250 мл
                                                                    </div>
                                                                </div>
                                                                <div class="property-size" style="display: none;">
                                                                    <label for="size_radio1">
                                                                        <input type="radio" id="size_radio1"
                                                                               class="size_radio" name="sizeradio"
                                                                               value="250 мл">
                                                                        250 мл
                                                                    </label>
                                                                    <label for="size_radio2">
                                                                        <input type="radio" id="size_radio2"
                                                                               class="size_radio" name="sizeradio"
                                                                               value="500 мл">
                                                                        500 мл
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            */ ?>
                                                        </div>
                                                        <div class="basket_block-price_roznica">
                                                            <div class="basket_block-price_roznica_inner">
                                                                <b><?php echo \yii::t('modules/catalog/app', 'Стоимость'); ?></b>
                                                                <strong><?php echo $offer->price . ' ' . $offer->curency; ?></strong>
                                                            </div>
                                                        </div>
                                                        <div class="basket_block-kol">
                                                            <div class="kolvo">
                                                                <div class="kol">
                                                                    <div class="number basket-item-block-amount">
                                                                        <b>  <?php echo \yii::t('modules/catalog/app', 'Количество'); ?></b>
                                                                        <span class="minus"
                                                                              data-entity="basket-item-quantity-minus"></span>
                                                                        <input type="number" size="5"
                                                                               min="1" max="999"
                                                                               class="basket-item-amount-filed"
                                                                               value="<?php echo $item->getQuantity(); ?>"
                                                                               data-offer-price="<?php echo $offer->price;?>"
                                                                               data-offer-currency = "<?php echo $offer->curency;?>"
                                                                               data-offer-id="<?php echo $offer->id; ?>" disabled>
                                                                        <span class="plus"
                                                                              data-entity="basket-item-quantity-plus"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="basket_block-price basket_total">
                                                            <strong id="<?php echo 'basket_total_'.$offer->id;?>"> <?php echo $item->getQuantity() * $offer->price . ' ' . $offer->curency; ?></strong>
                                                        </div>

                                                        <div class="basket_del" data-offer-id="<?php echo $offer->id; ?>">✖</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endforeach; ?>
                                <div class="full_basket__result">
                                    <div class="full_basket__result__promo">
                                        <span>  <?php echo \yii::t('modules/catalog/app', 'Есть промокод?'); ?></span>
                                        <form action="#" id="fullform_promo">
                                            <label>
                                                <input class="required"
                                                       placeholder="  <?php echo \yii::t('modules/catalog/app', 'Введите промокод'); ?>"
                                                       type="promo">
                                                <button>  <?php echo \yii::t('modules/catalog/app', 'Применить') ?></button>
                                            </label>
                                        </form>
                                    </div>
                                    <div class="full_basket__result__summ">
                                        <div class="full_basket__result__summ__all">
                                            <span>  <?php echo \yii::t('modules/catalog/app', 'Сумма заказа'); ?></span>
                                            <strong id="total_sum"><?php echo $cart->getTotalCost(); ?> <?php echo \app\components\CurrencyHelper::getCurrencyIcon();?></strong>
                                        </div>
                                        <div class="full_basket__result__summ__sale">
                                            <span>  <?php echo \yii::t('modules/catalog/app', 'С учетом скидки') ?>
                                                : </span>
                                            <strong id="total_sum_sale"><?php echo $cart->getTotalCost(); ?> <?php echo \app\components\CurrencyHelper::getCurrencyIcon();?></strong>
                                        </div>
                                        <div class="full_basket__result__summ__btn">
                                            <a href=""
                                               class="btn btn-outline-primary d-flex align-items-center justify-content-center">
                                                <?php echo \yii::t('modules/catalog/app', 'Купить в один клик'); ?>
                                            </a>
                                            <?php echo Html::a(\yii::t('modules/catalog/app', 'Make Order'), ['/order'], ['class' => 'my-btn check-auth']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
