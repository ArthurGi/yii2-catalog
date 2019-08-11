<?php
use app\components\SiteHelper;
use app\modules\catalog\components\CatalogHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\catalog\components\ProductHelper;
use app\modules\seo\models\SeoText;
use app\modules\seo\components\SeoGenerator;

/* @var $category \app\modules\catalog\models\Category */
/* @var $productItem \app\modules\catalog\components\ProductItem */
/* @var $offers \app\modules\catalog\models\Offer[] */
$product = $productItem->getProduct();
$seoTextModel = SeoText::find()->where(['url' => SeoText::prepareUrl(\Yii::$app->request->getPathInfo())])->one();
$metaData = SiteHelper::metaData($this, [$product, $page], [
    'title' => $product->name,
    'meta_title' => $product->name,
    'meta_description' => '',
    'meta_keywords' => '',
    ]);
if(!isset($this->params['breadcrumbs'])){
    $this->params['breadcrumbs'] = [];
}
\Yii::$app->opengraph->registerTags([
    'title' => $metaData['title'],
    'description' => $metaData['title'],
    'image' => \yii\helpers\Url::to('/web/img/logo.svg', true)
]);
if(isset($seoTextModel) && !empty($seoTextModel->canonical)) {
    SeoGenerator::addCanonical($seoTextModel->canonical);
} else {
    SeoGenerator::addCanonical(explode('?', \Yii::$app->request->getPathInfo())[0]);
}
$this->params['breadcrumbs'] = array_merge(
        $this->params['breadcrumbs'], 
        ProductHelper::getProductBreadcrumbs($category, $product, $page));

$reviewHtml = \app\modules\catalog\widgets\ProductReview::widget(['product' => $product]);
$deliveryInformation = app\components\ConstantHelper::getValue('delivery-information', '');
?>

<?=$this->render('_view-good', [
    'productItem' => $productItem,
    'metaData' => $metaData
]);?>

<div class="container">
  <div class="row">
    <div class="col-lg-7 no-gutters-right">
      <div class="cards-tabs__wrapper">
        <div class="cards-tabs">
          <span class="cards-tab__tab" data-tab=".dsc-tab"><?=\yii::t('modules/catalog/app', 'Description')?></span>
          <?php
          if($reviewHtml) {
            ?>
            <span class="cards-tab__tab" data-tab=".review-tab"><?=\yii::t('modules/catalog/app', 'Reviews')?> (<span class="counter"></span>)</span>
            <?php
          }
          ?>
          <?php
          if($deliveryInformation) { ?>
          <span class="cards-tab__tab" data-tab=".added-info-tab"><?=\yii::t('modules/catalog/app', 'Delivery information')?></span>        
          <?php
          }
          ?>
        </div>
        <div class="cards-tab__content">
          <div class="cards-tab__item dsc-tab">
            <table>
              <?php
              foreach($productItem->getProductParams() as $param) {
              ?>
                <tr>
                  <td><?=$param['name']?>:</td>
                  <td><?=implode(', ', $param['values'])?></td>
                </tr>
                <?php
            }
            ?>
            </table>
            <div class="cards-tab__item__content">
              <?=$product->getDsc();?>
            </div>
          </div>
          <div class="cards-tab__item review-tab">
              <?=$reviewHtml;?>
            </div>
          <div class="cards-tab__item added-info-tab">
            <?= $deliveryInformation;?>
          </div>
        </div>
      </div>
    </div>
    <?=\app\modules\catalog\widgets\ReviewForm::widget(['productId' => $product->id]);?>
  </div>
</div>

<?=\app\modules\catalog\widgets\SimilarProducts::widget(['product' => $product, 'isLoadInJS' => true]);?>

<div class="container">
  <div class="row">
	<?= app\modules\catalog\widgets\WatchedProducts::widget();?>
    <section class="sect-seo_conent sect-seo_conent-m">
        <div class="container">
            <div class="row">
                <div class="col-12">
                  <?=$seoTextModel ? $seoTextModel->end_text : ''?>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>






