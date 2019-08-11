<?php
use app\components\SiteHelper;
use \yii\helpers\Html;


$metaData = SiteHelper::metaData($this, [$page], [
    'title' => $page->name,
    'meta_title' => $page->name,
    'meta_description' => '',
    'meta_keywords' => '',
    ]);

if(!isset($this->params['breadcrumbs'])){
    $this->params['breadcrumbs'] = [];
}
$this->params['breadcrumbs'] = array_merge($this->params['breadcrumbs'], SiteHelper::getCurrentPageBreadcrumbs(false));
?>
<div>
  <?php
  foreach($specials as $special) {
    ?>
    <div>
      <h2><?= Html::a($special->name, ['/catalog/special/view', 'specialAlias' => $special->alias, 'page_id' => $page->id]);?></h2>
    </div>
      <?php
  }
  ?>
</div>
