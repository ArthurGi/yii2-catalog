<?php
use app\components\SiteHelper;
use app\modules\catalog\components\CatalogHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\catalog\components\ProductHelper;

/* $var $category \app\modules\catalog\models\Category */
/* $var $product \app\modules\catalog\models\Product */
/* $var $offers \app\modules\catalog\models\Offer[] */
$seoText = \app\modules\seo\components\SeoGenerator::getOnlySeoText(\Yii::$app->request->getPathInfo());
$metaData = SiteHelper::metaData($this, [$special, $page], [
    'title' => $special->name,
    'meta_title' => $special->name,
    'meta_description' => '',
    'meta_keywords' => '',
    ]);
if(!isset($this->params['breadcrumbs'])){
    $this->params['breadcrumbs'] = [];
}
$this->params['breadcrumbs'] = array_merge(
        $this->params['breadcrumbs'], SiteHelper::getCurrentPageBreadcrumbs(false));
$this->params['breadcrumbs'] = array_merge(
    $this->params['breadcrumbs'], [$special->name]);

?>
<div>
  <h1><?=$metaData['title']?></h1>
  <?//= Html::img(ProductHelper::getImage($special))?>
  
</div>
<?php
$url = Url::to([
'/catalog/default/view/',
'page_id' => $page->id,
'categoryAlias' => $category->alias,
'productAlias' => $product->alias
]);?>
<?php foreach ($offers as $offer){?>
<h3><?php echo  $offer->getReprName();?></h3>
    <?=Html::a(Html::img(ProductHelper::getImgPreview($offer->product)), $url);?>
    <b><?=$offer->price?> rub</b>
<?php };?>
<?=$seoText ? $seoText->end_text : '';?>





