<?php
use \yii\helpers\Url;
$items = $cart->getItems();
?>
<a class="icon_4 <?php echo $items ? 'active' : ''; ?>" href="<?php echo Url::to(['/cart/']); ?>"><img
            src="/web/img/icon_head_4.svg" alt="alt"></a>
<div class="m_card_hover__wrap">
    <div class="m_card_hover">
        <div class="close-cart-popap">
            <span></span>
        </div>
        <?php foreach ($items as $item): ?>
            <?php
            $product = $item->getProduct();
            $offer = $item->getOffer();
            ?>
            <div class="name d-flex">
                <div class="img_tovar">
                    <img src="<?php echo \app\components\ImageHelper::thumbnail($product->getImagePath(), 100, 100); ?>"
                         alt=" <?php echo $product->name; ?>">
                </div>
                <div class="desc_tovar">
                    <div class="title">
                        <?php echo $product->name; ?>
                    </div>
                    <div class="block_property">
                        <div>
                            <div> статика мл/кг/XхY см</div>
                            <div class="quality">
                                <?php echo $item->getQuantity() . ' x ' . $offer->price . ' ' . $offer->curency; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="m_final_price">
            <?php echo \yii::t('modules/catalog/app', 'Общая стоимость'); ?>: <span><?php echo $cart->getTotalCost(); ?>
                ₽</span>
            <a href="<?php echo Url::to(['/cart/']); ?>"
               class="my-btn"> <?php echo \yii::t('modules/catalog/app', 'Перейти в корзину'); ?></a>
        </div>
    </div>
</div>