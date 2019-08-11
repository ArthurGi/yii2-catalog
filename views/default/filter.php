<?php
use app\modules\catalog\components\FilterHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\catalog\components\CatalogHelper;
use yii\helpers\ArrayHelper;
/* $var $category \app\modules\catalog\models\Category */

$formUrl = Url::to(['/catalog/default/category/', 'page_id' => $page->id, 'categoryAlias' => $category->alias]) ;

$categoryTree = CatalogHelper::getCategoriesTree();
$filterItems = FilterHelper::getFilterItems($category);
?>

<div class="col-lg-3">
  <div class="category_left_menu" id="stock-filter">
    <form id="mobile-stock-filter" action="<?=$formUrl;?>" method="GET">
      <?=Html::activeHiddenInput($filter, 'limit', ['class' => 'stock-page-limit-input listen-input-change'])?>
      <?=Html::activeHiddenInput($filter, 'ordering', ['class' => 'stock-page-ordering-input listen-input-change'])?>
      <div class="block_filter_full d-flex align-items-center justify-content-between d-lg-none">
          <div class="block_pop_parent block_filter">
              <div class="title">
                  <?=\yii::t('modules/catalog/app', 'filters')?>
              </div>
              <div class="block_category_list">
                  <div class="mob_header_filter">
                      <div class="subtitle">
                          <?=\yii::t('modules/catalog/app', 'filters')?>
                      </div>
                      <a href="#" class="close">╳</a>
                  </div>
                  <div class="block_filter">
                      <div class="category_left_menu__title"><?=\yii::t('modules/catalog/app', 'Price');?></div>
                      <?= Html::textInput('', '', [
                      'class' => 'stock-range-slider',
                      'data-type' => 'double',
                      'data-min' => (int)$minMax['min'],
                      'data-max' => (int)$minMax['max'],
                      'data-from' => (int)$filter->priceFrom,
                      'data-to' => (int)$filter->priceTo,
                      'data-currency' => app\components\CurrencyHelper::getCurrency() === 'rub' ? '₽' : '$'
                          ]);?>
                      <?= Html::hiddenInput(Html::getInputName($filter, 'params').'[price][from]', (int)$filter->priceFrom, ['class' => 'price-from-filter listen-input-change']);?>
                      <?= Html::hiddenInput(Html::getInputName($filter, 'params').'[price][to]', (int)$filter->priceTo, ['class' => 'price-to-filter listen-input-change']);?>
                      <div class="category_left_menu__wrap-price">
                          <input type="text" class="category_left_menu__min-price" value=""/>
                          <input type="text" class="category_left_menu__max-price" value=""/>
                      </div>
                      <div class="category_checkbox">
                          <div class="control-group sele">
                              <label class="control control-checkbox" for="m-with-discount-checkbox">
                                  <?=Html::checkbox(
                                          Html::getInputName($filter, 'params').'[stock][]',
                                          is_array(ArrayHelper::getValue($filter->params, 'stock')) && in_array('1', ArrayHelper::getValue($filter->params, 'stock')) || ArrayHelper::getValue($filter->params, 'stock') === '1',
                                          ['id' => 'm-with-discount-checkbox', 'value' => '1'])?>
                                  <div class="control_indicator"></div>
                                  <?=\yii::t('modules/catalog/app', '% With discount');?>
                              </label>
                          </div>
                          <?php
                          foreach($filterItems as $filterItem) {
                            switch($filterItem['type']) {
                              case FilterHelper::TYPE_RANGE:
                                 \app\modules\catalog\widgets\FilterRange::widget([
                                    'info' => $filterItem,
                                    'model' => $filter
                                ]);
                                break;
                              case FilterHelper::TYPE_MULTIPLE_SELECT:
                                 echo \app\modules\catalog\widgets\FilterChecboxList::widget([
                                    'info' => $filterItem,
                                    'model' => $filter
                                ]);
                                break;
                            }
                          }
                          ?>
                          <div class="block_btn">
                              <a href="#" class="btn btn-primary apply-stock-filter">
                                  <?=\yii::t('modules/catalog/app', 'Apply')?>
                              </a>
                              <?php
                              if($filter->isShowResetBtn((int)$minMax['min'], (int)$minMax['max']))
                              {
                              ?>
                              <div class="category_left_menu__reset-filter">
                                  <a href="<?=$formUrl;?>" class="reset reset-stock-filters"><?=\yii::t('modules/catalog/app', 'Reset filter');?></a>
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
                  <?=\yii::t('modules/catalog/app', 'categories')?>
              </div>
              <div class="block_category_list">
                  <div class="mob_header_filter">
                      <div class="subtitle">
                          <?=\yii::t('modules/catalog/app', 'categories')?>
                      </div>
                      <a href="#" class="close">╳</a>
                  </div>
                  <div class="category_left_menu__menu">
                      <ul>
                        <?php
                        foreach($categoryTree as $categoryRow)
                        { ?>
                          <li class="main_cat">
                            <a href="<?=$categoryRow['url']?>"><?=$categoryRow['name']?></a>
                          </li>
                          <?php
                          if(array_key_exists('children', $categoryRow)) {
                            foreach($categoryRow['children'] as $childCategory)
                            {
                            ?>
                            <li>
                              ?>
                              <span class="child"><?=$childCategory['name']?></span>
                              <?php
                              if(array_key_exists('children', $childCategory) && count($childCategory['children'])) {
                                ?>
                                <ul>
                                  <?php
                                  foreach($childCategory['children'] as $childChildCategory) {
                                    ?>
                                    <li>
                                      <a href="<?=$childChildCategory['url']?>"><?=$childChildCategory['name']?></a>
                                    </li>
                                    <?php
                                  }
                                  ?>
                                </ul>
                              <?php
                              } else {
                              ?>
                                <li>
                                  <a href="<?=$childCategory['url']?>"><?=$childCategory['name']?></a>
                                </li>
                              <?php
                              }
                              ?>
                            </li>
                          <?php
                            }
                          }
                        }
                        ?>
                        <li class="action_link">
                          <a href="<?= Url::to(['/catalog/default/stock/', 'page_id' => \app\components\SiteHelper::getPageIdByHandler('catalog/default/index')]);?>">
                            <?=\yii::t('modules/catalog/app', '% Stocks')?>
                          </a>
                        </li>
                      </ul>
                   </div>
              </div>
          </div>
      </div>
     </form>
      <form id="desktop-stock-filter" action="<?=$formUrl;?>" method="GET">
        <?=Html::activeHiddenInput($filter, 'limit', ['class' => 'stock-page-limit-input listen-input-change'])?>
        <?=Html::activeHiddenInput($filter, 'ordering', ['class' => 'stock-page-ordering-input listen-input-change'])?>
      <div class="block_filter d-none d-lg-block">
          <div class="block_filter_name"><?=\yii::t('modules/catalog/app', 'Filters');?></div>
          <div class="category_left_menu__title"><?=\yii::t('modules/catalog/app', 'Price');?></div>
           <?= Html::hiddenInput('', '', [
                        'class' => 'stock-range-slider',
                        'data-type' => 'double',
                        'data-min' => (int)$minMax['min'],
                        'data-max' => (int)$minMax['max'],
                        'data-from' => (int)$filter->priceFrom,
                        'data-to' => (int)$filter->priceTo,
                        'data-currency' => app\components\CurrencyHelper::getCurrency() === 'rub' ? '₽' : '$'
                    ]);?>
            <?= Html::hiddenInput(Html::getInputName($filter, 'params').'[price][from]', (int)$filter->priceFrom, ['class' => 'price-from-filter listen-input-change']);?>
            <?= Html::hiddenInput(Html::getInputName($filter, 'params').'[price][to]', (int)$filter->priceTo, ['class' => 'price-to-filter listen-input-change']);?>
           <div class="category_left_menu__wrap-price">
                <input type="text" class="category_left_menu__min-price" value=""/>
                <input type="text" class="category_left_menu__max-price" value=""/>
            </div>
          <div class="category_checkbox">
              <div class="control-group sele">
                  <label class="control control-checkbox" for="with-discount-checkbox">
                      <?=Html::checkbox(
                              Html::getInputName($filter, 'params').'[stock][]',
                              is_array(ArrayHelper::getValue($filter->params, 'stock')) && in_array('1', ArrayHelper::getValue($filter->params, 'stock')) || ArrayHelper::getValue($filter->params, 'stock') === '1',
                              ['id' => 'with-discount-checkbox', 'value' => '1', 'class' => 'listen-input-change'])?>
                      <div class="control_indicator"></div>
                      <?=\yii::t('modules/catalog/app', '% With discount');?>
                  </label>
              </div>
              <?php

              foreach($filterItems as $filterItem) {
                switch($filterItem['type']) {
                  case FilterHelper::TYPE_RANGE:
                     \app\modules\catalog\widgets\FilterRange::widget([
                        'info' => $filterItem,
                        'model' => $filter
                    ]);
                    break;
                  case FilterHelper::TYPE_MULTIPLE_SELECT:
                     echo \app\modules\catalog\widgets\FilterChecboxList::widget([
                        'info' => $filterItem,
                        'model' => $filter
                    ]);
                    break;
                }
              }
              ?>
              <?php
              if($filter->isShowResetBtn((int)$minMax['min'], (int)$minMax['max']))
              {
              ?>
              <div class="category_left_menu__finded">
                  <?=\yii::t('modules/catalog/stock', 'Found products');?>: <span><?=$pagination->totalCount;?></span> <?=\yii::t('modules/catalog/stock', 'of');?> <span><?=$totalCountWithoutFilter?></span>
              </div>
              <div class="category_left_menu__reset-filter">
                  <a href="<?=$formUrl;?>" class="reset reset-stock-filters"><?=\yii::t('modules/catalog/app', 'Reset filter');?></a>
              </div>
              <?php
              }
              ?>
          </div>
      </div>
    </form>
  </div>
</div>

