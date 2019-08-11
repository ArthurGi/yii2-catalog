<?php
use yii\helpers\Html;
use app\modules\catalog\models\StockFilter;
/* @var $this \yii\web\View */
/* @var $filter app\modules\catalog\models\StockFilter */
/* @var $categoryModels app\modules\catalog\models\Category[] */
/* @var $pagination \yii\data\Pagination */
\app\components\SiteHelper::metaData($this, [$page], [
    'title' => \yii::t('modules/catalog/stock', 'Stocks'), 
    'meta_title' => \yii::t('modules/catalog/stock', 'Stocks'),
    'meta_description' => '',
    'meta_keywords' => ''
]);
$seo = \app\modules\seo\components\SeoGenerator::getOnlySeoText(\Yii::$app->request->getPathInfo());
if(!isset($this->params['breadcrumbs'])){
    $this->params['breadcrumbs'] = [];
}
$this->params['breadcrumbs'] = array_merge($this->params['breadcrumbs'], [['label' => \yii::t('modules/catalog/stock', 'Stocks')]]);
?>
<div class="container stock-page-body">
  <div class="row">
      <div class="col-12">
          <div class="category-title">
              <h1><?=\yii::t('modules/catalog/stock', 'Stocks');?></h1>
              <div class="category-title__sorting d-none d-lg-flex m-fix_bg">
                  <div class="category-title__popuplar">
                      <div class="category-title__name"><?=\yii::t('modules/catalog/stock', 'Order by');?>:</div>
                      <?=Html::activeDropDownList($filter, 'ordering', StockFilter::getOrderingItems(), ['class' => 'stock-page-sorting', 'id' => uniqid(), 'data-mobile' => '0']);?>  
                  </div>
                  <div class="category-title__quantity">
                      <div class="category-title__name"><?=\yii::t('modules/catalog/stock', 'Show by');?>:</div>
                      <?=Html::activeDropDownList($filter, 'limit', StockFilter::getLimitItems(), ['class' => 'stock-page-limit', 'id' => uniqid(), 'data-mobile' => '0']);?>    
                  </div>
              </div>
          </div>
      </div>
  </div>
		<div class="row">
			<div class="col-lg-3">
				<?=$this->render('stock-filter', [
                    'categoryModels' => $categoryModels,
                    'filter' => $filter,
                    'minMax' => $minMax,
                    'page' => $page,
                    'totalCountWithoutFilter' => $totalCountWithoutFilter,
                    'pagination' => $pagination
                ]);?>
			</div>
			<div class="col-lg-9">
				<div class="category-title__sorting d-flex justify-content-between d-lg-none">
					<div class="category-title__popuplar">
						<div class="category-title__name"><?=\yii::t('modules/catalog/stock', 'Order by');?>:</div>
                        <?=Html::activeDropDownList($filter, 'ordering', StockFilter::getOrderingItems(), ['class' => 'stock-page-sorting', 'id' => uniqid(), 'data-mobile' => '1']);?>   
					</div>
					<div class="category-title__quantity">
						<div class="category-title__name"><?=\yii::t('modules/catalog/stock', 'Show by');?>:</div>
                      <?=Html::activeDropDownList($filter, 'limit', StockFilter::getLimitItems(), ['class' => 'stock-page-limit', 'id' => uniqid(), 'data-mobile' => '1']);?>    
					</div>
				</div>
				<?=$this->render('stock-goods', [
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
                      'data-url' => \yii\helpers\Url::to(['/catalog/default/stock/', 'page_id' => $page->id] + \Yii::$app->request->getQueryParams())
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
                  <?=$seo ? $seo->end_text : ''?>
                </div>
            </div>
        </div>
    </section>
  </div>
</div>