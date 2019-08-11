<?php
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $filter app\modules\catalog\models\StockFilter */
/* @var $categoryModels app\modules\catalog\models\Category[] */
/* @var $pagination \yii\data\Pagination */

$mobileHitId = uniqid('ch_');
$mobileNewId = uniqid('ch_');
$mobileStockId = uniqid('ch_');
$desktopHitId = uniqid('ch_');
$desktopNewId = uniqid('ch_');
$desktopStockId = uniqid('ch_');
?>
<div class="category_left_menu" id="stock-filter">
  <form id="mobile-stock-filter" action="<?=yii\helpers\Url::to(['/catalog/default/stock/', 'page_id' => $page->id]);?>" method="GET">
    <?=Html::activeHiddenInput($filter, 'limit', ['class' => 'stock-page-limit-input listen-input-change'])?>
    <?=Html::activeHiddenInput($filter, 'ordering', ['class' => 'stock-page-ordering-input listen-input-change'])?>
  <div class="block_filter_full d-flex align-items-center justify-content-between d-lg-none">
      <div class="block_pop_parent block_filter">
          <div class="title">
              <?=\yii::t('modules/catalog/stock', 'filters');?>
          </div>
          <div class="block_category_list"> 
              <div class="mob_header_filter">
                  <div class="subtitle">
                      <?=\yii::t('modules/catalog/stock', 'filters');?>
                  </div>	
                  <a href="#" class="close">╳</a>
              </div>
              <div class="block_filter">
                   <div class="block_top_filter control-group">
                     <label class="control control-checkbox" for="<?=$mobileHitId;?>">
                       <?=Html::activeCheckbox($filter, 'isHit', ['value' => 1, 'label' => null, 'id' => $mobileHitId, 'class' => 'listen-input-change'])?>
                          <div class="control_indicator"></div>
                          <?=\yii::t('modules/catalog/stock', 'HIT');?>
                      </label>
                      <label class="control control-checkbox" for="<?=$mobileNewId;?>">
                          <?=Html::activeCheckbox($filter, 'isNew', ['value' => 1, 'label' => null, 'id' => $mobileNewId, 'class' => 'listen-input-change'])?>
                          <div class="control_indicator"></div>
                          <?=\yii::t('modules/catalog/stock', 'NEW');?>
                      </label>
                      <label class="control control-checkbox" for="<?=$mobileStockId;?>">
                          <?=Html::activeCheckbox($filter, 'isStock', ['value' => 1, 'label' => null, 'id' => $mobileStockId, 'class' => 'listen-input-change'])?>
                          <div class="control_indicator"></div>
                           <?=\yii::t('modules/catalog/stock', 'STOCK');?>
                      </label>
                  </div>
                  <div class="category_left_menu__title"><?=\yii::t('modules/catalog/stock', 'Price');?></div>
                  <?= Html::textInput('', '', [
                      'class' => 'stock-range-slider',
                      'data-type' => 'double',
                      'data-min' => (int)$minMax['min'],
                      'data-max' => (int)$minMax['max'],
                      'data-from' => (int)$filter->priceFrom,
                      'data-to' => (int)$filter->priceTo,
                      'data-currency' => app\components\CurrencyHelper::getCurrency() === 'rub' ? '₽' : '$'
                  ]);?>
                  <?= Html::hiddenInput(Html::getInputName($filter, 'priceFrom'), (int)$filter->priceFrom, ['class' => 'price-from-filter listen-input-change']);?>
                  <?= Html::hiddenInput(Html::getInputName($filter, 'priceTo'), (int)$filter->priceTo, ['class' => 'price-to-filter listen-input-change']);?>
                  
                  <div class="category_left_menu__wrap-price">
                    <input type="text" class="category_left_menu__min-price"/>
                    <input type="text" class="category_left_menu__max-price" value=""/>
                  </div>
                  <div class="category_checkbox">
                      <div class="category_left_menu__menu">
                          <div class="category_left_menu__title"><?=\yii::t('modules/catalog/stock', 'Categories');?> <span>(<?=count($categoryModels);?>)</span></div>
                          <div class="control-group">
                            <?php
                              foreach($categoryModels as $categoryModel) {
                                $checkBoxId = uniqid('ch_');
                                ?>
                                <label class="control control-checkbox" for="<?=$checkBoxId;?>">
                                    <?= Html::checkbox(
                                            Html::getInputName($filter, 'categories')."[{$categoryModel->id}]",
                                            in_array($categoryModel->id, $filter->categories), 
                                            ['id' => $checkBoxId, 'value' => $categoryModel->id])?>
                                    <div class="control_indicator"></div>
                                    <?=$categoryModel->getName();?>
                                </label>
                              <?php
                              }
                              ?>
                          </div>
                      </div>
                    
                      <div class="block_btn">
                          <a href="#" class="btn btn-primary apply-stock-filter">		
                              <?=\yii::t('modules/catalog/stock', 'Apply');?>
                          </a>
                        <?php
                        if($filter->isShowResetBtn((int)$minMax['min'], (int)$minMax['max']))
                        {
                        ?>
                          <div class="category_left_menu__reset-filter">
                            <a href="#" class="reset reset-stock-filters">
                              <?=\yii::t('modules/catalog/stock', 'Reset filters');?>
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
      <div class="block_pop_parent block_category">
          <div class="title">
              <?=\yii::t('modules/catalog/stock', 'categories');?>
          </div>
          <div class="block_category_list">
              <div class="mob_header_filter">
                  <div class="subtitle">
                      <?=\yii::t('modules/catalog/stock', 'categories');?>
                  </div>	
                  <a href="#" class="close">╳</a>
              </div>
              <div class="category_left_menu__menu stock-cat-filter">
                <?php
                $tree = \app\modules\catalog\components\CatalogHelper::getParentTree($categoryModels);
                foreach($tree[0]['childs'] as $firstParentCategory) {
                  ?>
                  <ul>
                    <li class="main_cat">
                      <a href="#"><?=$firstParentCategory['model']->getName()?></a>
                    </li>
                    <?php
                    foreach($firstParentCategory['childs'] as $secondParentCategory) {
                      $linkParams = [
                        '/catalog/default/stock/',
                        'page_id' => $page->id,
                        $filter->formName() => $filter->attributes()
                      ];
                      $linkParams[$filter->formName()]['categories'] = array_map(function($model){
                        return $model['model']->id;
                      }, $secondParentCategory['childs']);
                      $allCategoriesLink = yii\helpers\Url::to($linkParams);
                      ?>
                      <li>
                        <span class="child"><?=$secondParentCategory['model']->getName()?></span>
                        <ul>
                          <li>
                            <a href="<?=$allCategoriesLink?>"><?=\yii::t('modules/catalog/stock', 'All categories');?></a>
                          </li>
                          <?php
                          foreach($secondParentCategory['childs'] as $childCatgegory) {
                            $childLinkParams = [
                                '/catalog/default/stock/',
                                'page_id' => $page->id,
                                $filter->formName() => $filter->attributes()
                              ];
                            $linkParams[$filter->formName()]['categories'] = [$childCatgegory['model']->id];
                            $childCategoriesLink = yii\helpers\Url::to($linkParams);
                            ?>
                              <li><a href="<?=$childCategoriesLink;?>"><?=$childCatgegory['model']->getName();?></a></li>
                            <?php
                          }
                          ?>
                        </ul>
                      </li>
                      <?php
                    }
                    ?>
                  </ul><?php
                }
                ?>
               </div>
          </div>
      </div>
  </div>
  </form>
  <form id="desktop-stock-filter" action="<?=yii\helpers\Url::to(['/catalog/default/stock/', 'page_id' => $page->id]);?>" method="GET">
    <?=Html::activeHiddenInput($filter, 'limit', ['class' => 'stock-page-limit-input listen-input-change'])?>
    <?=Html::activeHiddenInput($filter, 'ordering', ['class' => 'stock-page-ordering-input listen-input-change'])?>
    <div class="block_filter d-none d-lg-block">
        <div class="block_filter_name"><?=\yii::t('modules/catalog/stock', 'Filters');?></div>
         <div class="block_top_filter control-group">
            <label class="control control-checkbox" for="<?=$desktopHitId;?>">
                <?=Html::activeCheckbox($filter, 'isHit', ['value' => 1, 'label' => null, 'id' => $desktopHitId, 'class' => 'listen-input-change'])?>
                <div class="control_indicator"></div>
                <?=\yii::t('modules/catalog/stock', 'HIT');?>
            </label>
            <label class="control control-checkbox" for="<?=$desktopNewId;?>">
                <?=Html::activeCheckbox($filter, 'isNew', ['value' => 1, 'label' => null, 'id' => $desktopNewId, 'class' => 'listen-input-change'])?>
                <div class="control_indicator"></div>
                <?=\yii::t('modules/catalog/stock', 'NEW');?>
            </label>
            <label class="control control-checkbox" for="<?=$desktopStockId;?>">
                <?=Html::activeCheckbox($filter, 'isStock', ['value' => 1, 'label' => null, 'id' => $desktopStockId, 'class' => 'listen-input-change'])?>
                <div class="control_indicator"></div>
                 <?=\yii::t('modules/catalog/stock', 'STOCK');?>
            </label>
        </div>

        <div class="category_left_menu__title"><?=\yii::t('modules/catalog/stock', 'Price');?></div>
        <?= Html::hiddenInput('', '', [
                        'class' => 'stock-range-slider',
                        'data-type' => 'double',
                        'data-min' => (int)$minMax['min'],
                        'data-max' => (int)$minMax['max'],
                        'data-from' => (int)$filter->priceFrom,
                        'data-to' => (int)$filter->priceTo,
                        'data-currency' => app\components\CurrencyHelper::getCurrency() === 'rub' ? '₽' : '$'
                    ]);?>
        <?= Html::hiddenInput(Html::getInputName($filter, 'priceFrom'), (int)$filter->priceFrom, ['class' => 'price-from-filter listen-input-change']);?>
        <?= Html::hiddenInput(Html::getInputName($filter, 'priceTo'), (int)$filter->priceTo, ['class' => 'price-to-filter listen-input-change']);?>
        <div class="category_left_menu__wrap-price">
            <input type="text" class="category_left_menu__min-price" value=""/>
           <input type="text" class="category_left_menu__max-price" value=""/>
        </div>
        <div class="category_checkbox">
            <div class="category_left_menu__menu">
                <div class="category_left_menu__title"><?=\yii::t('modules/catalog/stock', 'Categories');?> <span>(<?=count($categoryModels);?>)</span></div>
                <div class="control-group">
                  <?php
                  foreach($categoryModels as $categoryModel) {
                    $checkBoxId = uniqid('ch_');
                    ?>
                    <label class="control control-checkbox" for="<?=$checkBoxId;?>">
                        <?= Html::checkbox(
                                Html::getInputName($filter, 'categories')."[{$categoryModel->id}]",
                                in_array($categoryModel->id, $filter->categories), 
                                ['id' => $checkBoxId, 'class'=> 'listen-input-change', 'value' => $categoryModel->id])?>
                        <div class="control_indicator"></div>
                        <?=$categoryModel->getName();?>
                    </label>
                  <?php
                  }
                  ?>
                </div>
            </div>
          <?php
          if($filter->isShowResetBtn((int)$minMax['min'], (int)$minMax['max']))
          {
          ?>
            <div class="category_left_menu__finded">
                <?=\yii::t('modules/catalog/stock', 'Found products');?>: <span><?=$pagination->totalCount;?></span> <?=\yii::t('modules/catalog/stock', 'of');?> <span><?=$totalCountWithoutFilter?></span>
            </div>
            <div class="category_left_menu__reset-filter">
              <a href="<?= yii\helpers\Url::to(['/catalog/default/stock/', 'page_id' => $page->id,]);?>" class="reset reset-stock-filters">
                <?=\yii::t('modules/catalog/stock', 'Reset filters');?>
              </a>
            </div>
          <?php
          }
          ?>
        </div>
    </div>
  </form>
</div>