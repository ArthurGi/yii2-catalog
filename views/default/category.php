<?php
use app\components\SiteHelper;
use app\modules\catalog\components\CatalogHelper;
use yii\helpers\Html;
/* $var $seoText app\modules\seo\models\SeoText */
/* $var $category \app\modules\catalog\models\Category */
$seoText = \app\modules\seo\components\SeoGenerator::getOnlySeoText(\Yii::$app->request->getPathInfo());
$metaData = SiteHelper::metaData($this, [$category, $page], [
    'title' => $category->name,
    'meta_title' => $category->name,
    'meta_description' => '',
    'meta_keywords' => '',
    ]);
if(!isset($this->params['breadcrumbs'])){
    $this->params['breadcrumbs'] = [];
}
$this->params['breadcrumbs'] = array_merge($this->params['breadcrumbs'], CatalogHelper::getCategoryBreadcrumbs($category->id, $page));
?>
<?=$seoText ? $seoText->begin_text : '';?>
<div>
  <h1><?=$metaData['title']?></h1>
  <?php
  foreach($category->children as $childCat) {
    ?>
    <div class="item" style="width: 50%; float: left;">
      <?=Html::img(CatalogHelper::getCatalogPreview($childCat), ['alt' => $childCat->name])?>
      <ul>
        <?php
        foreach($childCat->children as $childChildCat) {
          ?>
          <li>
            <?= Html::a($childChildCat->name, ['/catalog/default/category', 'categoryAlias' => $childChildCat->alias, 'page_id' => $page->id]);?>
          </li>
        <?php
        }
        ?>
      </ul>
    </div>  
    <?php
  }
  ?>
</div>
<?=$seoText ? $seoText->end_text : '';?>


