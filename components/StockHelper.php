<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;

use app\modules\catalog\models\Product;
use yii\helpers\ArrayHelper;
use app\modules\catalog\models\NosqlProduct;
use app\modules\catalog\models\StockFilter;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\AdditionalProductCategory;

class StockHelper
{
  /**
   * 
   * @param StockFilter $filter
   * @return \yii\db\ActiveQuery
   */
  public static function buildProductQuery($filter)
  {
    $query = Product::find();
    $query->innerJoin(NosqlProduct::tableName(), NosqlProduct::tableName().'.product_id='.Product::tableName().'.id');
    if(!$filter->isHit && !$filter->isNew && !$filter->isStock) {
      $query->andWhere([
          'OR', 
          [NosqlProduct::tableName().'.is_hit' => 1],
          [NosqlProduct::tableName().'.is_new' => 1],
          [NosqlProduct::tableName().'.is_stock' => 1]
      ]);
    }
    if($filter->isHit) {
      $query->andWhere([NosqlProduct::tableName().'.is_hit' => 1]);
    }
    if($filter->isNew) {
      $query->andWhere([NosqlProduct::tableName().'.is_new' => 1]);
    }
    if($filter->isStock) {
      $query->andWhere([NosqlProduct::tableName().'.is_stock' => 1]);
    }
    if($filter->priceFrom > 0) {
      if(\app\components\CurrencyHelper::getCurrency() === 'rub') {
        $query->andWhere(NosqlProduct::tableName().'.price_rub >= :fromPrice', [':fromPrice' => $filter->priceFrom]);
      }
      if(\app\components\CurrencyHelper::getCurrency() === 'usd') {
        $query->andWhere(NosqlProduct::tableName().'.price_usd >= :fromPrice', [':fromPrice' => $filter->priceFrom]);
      }
    }
    if($filter->priceTo > 0) {
      if(\app\components\CurrencyHelper::getCurrency() === 'rub') {
        $query->andWhere(NosqlProduct::tableName().'.price_rub <= :toPrice', [':toPrice' => $filter->priceTo]);
      }
      if(\app\components\CurrencyHelper::getCurrency() === 'usd') {
        $query->andWhere(NosqlProduct::tableName().'.price_usd <= :toPrice', [':toPrice' => $filter->priceTo]);
      }
    }
    if(is_array($filter->categories) && count($filter->categories) > 0) {
      $categoryCondition = ['OR'];
      $additionalCategoryCondition = ['OR'];
      foreach($filter->categories as $catId) {
        $additionalCategoryCondition[] = [AdditionalProductCategory::tableName().'.category_id' => $catId];
        $categoryCondition[] = [NosqlProduct::tableName().'.category_id' => $catId];
      }
      $additionalCategorySql = AdditionalProductCategory::find()
              ->select(['product_id'])
              ->where($additionalCategoryCondition)
              ->createCommand()->getRawSql();
      $query->andWhere(['OR', $categoryCondition, NosqlProduct::tableName().".product_id IN ({$additionalCategorySql})"]);
    }
    $query->andWhere([NosqlProduct::tableName().'.published' => 1]);
    $query->groupBy(Product::tableName().'.id');
    
    if(in_array($filter->ordering, [StockFilter::SORT_BY_PRICE_ASCENDING, StockFilter::SORT_BY_PRICE_DESCENDING])) {
      if(\app\components\CurrencyHelper::getCurrency() === 'rub') {
        $field = NosqlProduct::tableName().'.price_rub';
      } else {
        $field = NosqlProduct::tableName().'.price_usd';
      }
      $query->orderBy([$field => ($filter->ordering == StockFilter::SORT_BY_PRICE_ASCENDING ? SORT_ASC : SORT_DESC)]);
    }
    
    return $query;
  }
  
  /**
   * 
   * @return Category[]
   */
  public static function getCategoryModels()
  {
    $query = Category::find();
    $query->innerJoin(NosqlProduct::tableName(), NosqlProduct::tableName().'.category_id='.Category::tableName().'.id');
    $query->andWhere([
          'OR', 
          [NosqlProduct::tableName().'.is_hit' => 1],
          [NosqlProduct::tableName().'.is_new' => 1],
          [NosqlProduct::tableName().'.is_stock' => 1]
      ]);
    $query->andWhere([Category::tableName().'.published' => 1]);
    $categories = ArrayHelper::index($query->all(), 'id');
    
    $additionalCategoryQuery = Category::find();
    $additionalCategoryQuery->innerJoin(AdditionalProductCategory::tableName(), AdditionalProductCategory::tableName().'.category_id='.Category::tableName().'.id');
    $additionalCategoryQuery->innerJoin(NosqlProduct::tableName(), NosqlProduct::tableName().'.product_id='.AdditionalProductCategory::tableName().'.product_id');
    $additionalCategoryQuery->andWhere([
          'OR', 
          [NosqlProduct::tableName().'.is_hit' => 1],
          [NosqlProduct::tableName().'.is_new' => 1],
          [NosqlProduct::tableName().'.is_stock' => 1]
      ]);
    $additionalCategoryQuery->groupBy( Category::tableName().'.id');
    $additionalCategoryQuery->andWhere([Category::tableName().'.published' => 1]);
    $additionalCategories = ArrayHelper::index($additionalCategoryQuery->all(), 'id');
    return $categories + $additionalCategories;
  }
  
  /**
   * 
   * @return array [min => min, max => max]
   */
  public static function getMinMaxPrice()
  {
    $query = NosqlProduct::find();
    $lang = \app\components\CurrencyHelper::getCurrency();
    if($lang === 'rub') {
      $query->select(['min(price_rub) as min', 'max(price_rub) as max']);
    }
    if($lang === 'usd') {
      $query->select(['min(price_usd) as min', 'max(price_usd) as max']);
    }
    $query->andWhere([
          'OR', 
          [NosqlProduct::tableName().'.is_hit' => 1],
          [NosqlProduct::tableName().'.is_new' => 1],
          [NosqlProduct::tableName().'.is_stock' => 1]
      ]);
    $query->andWhere([NosqlProduct::tableName().'.published' => 1]);
    $data = $query->asArray()->one();
    return $data;
  }
  
  
}