<?php
use app\components\SiteHelper;
use \yii\helpers\Html;
use app\modules\seo\models\SeoText;
use app\modules\seo\components\SeoGenerator;
use app\modules\catalog\models\CatalogFilter;
use app\modules\catalog\components\CatalogHelper;
use yii\helpers\Url;

/* @var $seoTextModel SeoText */
/* @var $this \yii\web\View */
/* @var $filter app\modules\catalog\models\CatalogFilter */

$seoTextModel = SeoText::find()->where(['url' => SeoText::prepareUrl(\Yii::$app->request->getPathInfo())])->one();

$metaData = SiteHelper::metaData($this, [$category ? $category : $page], [
    'title' => $seoTextModel && $seoTextModel->title ? $seoTextModel->title : ($category ? $category->getName() : $page->name),
    'meta_title' => $seoTextModel && $seoTextModel->title ? $seoTextModel->title : ($category ? $category->getName() : $page->name),
    'meta_description' => $seoTextModel && $seoTextModel->description ? $seoTextModel->description : '',
    'meta_keywords' => $seoTextModel && $seoTextModel->keywords ? $seoTextModel->keywords : '',
    ]);
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
if(!isset($this->params['breadcrumbs'])){
    $this->params['breadcrumbs'] = [];
}
if($category) {
  $this->params['breadcrumbs'] = array_merge($this->params['breadcrumbs'], CatalogHelper::getCategoryBreadcrumbs($category->id, $page));
} else {
  $this->params['breadcrumbs'] = array_merge($this->params['breadcrumbs'], SiteHelper::getCurrentPageBreadcrumbs(false));
}

?>
<?=$headerBanner?>
<div class="container stock-page-body">
    <div class="row">
        <div class="col-12">
            <div class="category-title">
                <h1><?=$metaData['title'];?></h1>
                <div class="category-title__sorting d-none d-lg-flex m-fix_bg">
                  <div class="category-title__popuplar">
                      <div class="category-title__name"><?=\yii::t('modules/catalog/stock', 'Order by');?>:</div>
                      <?=Html::activeDropDownList($filter, 'ordering', CatalogFilter::getOrderingItems(), ['class' => 'stock-page-sorting', 'id' => uniqid(), 'data-mobile' => '0']);?>  
                  </div>
                  <div class="category-title__quantity">
                      <div class="category-title__name"><?=\yii::t('modules/catalog/stock', 'Show by');?>:</div>
                      <?=Html::activeDropDownList($filter, 'limit', CatalogFilter::getLimitItems(), ['class' => 'stock-page-limit', 'id' => uniqid(), 'data-mobile' => '0']);?>    
                  </div>
              </div>
            </div>
        </div>
    </div>
  
    <div class="row">
        <?=$this->render('index-filter', [
            'categoryTree' => $categoryTree,
            'filter' => $filter,
            'minMax' => $minMax,
            'page' => $page,
            'category' => $category
        ]);?>
        <div class="col-lg-9">
          <div class="category-title__sorting d-flex justify-content-between d-lg-none">
              <div class="category-title__popuplar">
                  <div class="category-title__name"><?=\yii::t('modules/catalog/stock', 'Order by');?>:</div>
                  <?=Html::activeDropDownList($filter, 'ordering', CatalogFilter::getOrderingItems(), ['class' => 'stock-page-sorting', 'id' => uniqid(), 'data-mobile' => '1']);?>   
              </div>
              <div class="category-title__quantity">
                  <div class="category-title__name"><?=\yii::t('modules/catalog/stock', 'Show by');?>:</div>
                <?=Html::activeDropDownList($filter, 'limit', CatalogFilter::getLimitItems(), ['class' => 'stock-page-limit', 'id' => uniqid(), 'data-mobile' => '1']);?>    
              </div>
          </div>
          <?=$this->render('list-goods', [
              'products' => $products
                  ])?>
          <div class="malina-catalog__more">
            <p>
              <?=\app\components\SiteHelper::generatePluralPhrase(count($products), [
                  'one' => \yii::t('modules/catalog/stock', 'Showing_one'), 
                  'few' => \yii::t('modules/catalog/stock', 'Showing'),
                  'many' => \yii::t('modules/catalog/stock', 'Showing'),
                  'other' => \yii::t('modules/catalog/stock', 'Showing')]);?> 
              <span class="showing-numbers"><?=count($products);?></span>
              <?=\app\components\SiteHelper::generatePluralPhrase(count($products), [
                  'one' => \yii::t('modules/catalog/stock', 'item'), 
                  'few' => \yii::t('modules/catalog/stock', 'items_few'),
                  'many' => \yii::t('modules/catalog/stock', 'items'),
                  'other' => \yii::t('modules/catalog/stock', 'items')]);?> 
               <?=\yii::t('modules/catalog/stock', 'of')?> 
              <span><?=(int)$pagination->totalCount;?></span>
            </p>
            <?= Html::a(\yii::t('modules/catalog/stock', 'Show more'), '#', [
                'class' => 'btn btn-outline-primary d-flex align-items-center justify-content-center show-more-btn',
                'data-total' => $pagination->totalCount,
                'data-page' => (int)$pagination->getPage() + 1 ,
                'data-limit' => $pagination->getLimit(),
                'data-url' => $category 
                    ? Url::to(['/catalog/default/category/', 'page_id' => $page->id, 'categoryAlias' => $category->alias] + \Yii::$app->request->getQueryParams()) 
                    : Url::to(['/catalog/default/index/', 'page_id' => $page->id] + \Yii::$app->request->getQueryParams())
                ]);?>
          </div>
      </div>
    </div>
</div>

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
	
