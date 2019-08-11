<?php
/* @var $this \yii\web\View */
/* @var $categoryTree array */

use yii\helpers\Url;
use yii\helpers\Html;
$formUrl = $category 
          ? Url::to(['/catalog/default/category/', 'page_id' => $page->id, 'categoryAlias' => $category->alias]) 
          : Url::to(['/catalog/default/index/', 'page_id' => $page->id]);
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
                  <?= Html::hiddenInput(Html::getInputName($filter, 'priceFrom'), (int)$filter->priceFrom, ['class' => 'price-from-filter listen-input-change']);?>
                  <?= Html::hiddenInput(Html::getInputName($filter, 'priceTo'), (int)$filter->priceTo, ['class' => 'price-to-filter listen-input-change']);?>
              <div class="category_left_menu__wrap-price">
                  <input type="text" class="category_left_menu__min-price" value=""/>
                  <input type="text" class="category_left_menu__max-price" value=""/>
              </div>
              <div class="block_btn">
                  <a href="#" class="btn btn-primary apply-stock-filter">
                      <?=\yii::t('modules/catalog/app', 'Apply')?>
                  </a>
                  <?php
                  if($filter->isShowResetBtn((int)$minMax['min'], (int)$minMax['max']))
                  {
                  ?>
                  <div class="category_left_menu__reset-filter">
                      <a href="#" class="reset"><?=\yii::t('modules/catalog/app', 'Reset filter');?></a>
                  </div>
                  <?php
                  }
                  ?>
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
                        foreach($categoryTree as $category)
                        { ?>
                          <li class="main_cat">
                            <a href="<?=$category['url']?>"><?=$category['name']?></a>
                          </li>
                          <?php
                          if(array_key_exists('children', $category)) {
                            foreach($category['children'] as $childCategory)
                            {
                            ?>
                            <li>
                              <?php
                              if(array_key_exists('children', $childCategory) && count($childCategory['children'])) {
                                ?>
                                <span class="child"><?=$childCategory['name']?></span>
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
            <?= Html::hiddenInput(Html::getInputName($filter, 'priceFrom'), (int)$filter->priceFrom, ['class' => 'price-from-filter listen-input-change']);?>
            <?= Html::hiddenInput(Html::getInputName($filter, 'priceTo'), (int)$filter->priceTo, ['class' => 'price-to-filter listen-input-change']);?>
           <div class="category_left_menu__wrap-price">
                <input type="text" class="category_left_menu__min-price" value=""/>
                <input type="text" class="category_left_menu__max-price" value=""/>
            </div>
           <div class="category_left_menu__menu">
              <ul>
                <?php
                foreach($categoryTree as $category)
                { ?>
                  <li>
                    <a href="<?=$category['url']?>"><?=$category['name']?></a>
                    <?php
                    if(array_key_exists('children', $category)) { ?>
                    <ul>
                      <?php
                      
                      foreach($category['children'] as $childCategory)
                      {
                      ?>
                        <li>
                          <a href="<?=$childCategory['url']?>"><?=$childCategory['name']?></a>
                        </li>
                      <?php
                      }
                      ?>
                    </ul>
                    <?php
                    }
                    ?>
                  </li>
                <?php
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
      </form>
  </div>
</div>