<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;
use app\modules\catalog\models\CatalogFilter;
use app\modules\catalog\models\Product;
use yii\helpers\ArrayHelper;
use app\modules\catalog\models\NosqlProduct;
use app\modules\catalog\models\AdditionalProductCategory;

class FirstLevelCategoryHelper
{
   /**
   * 
   * @param CatalogFilter $filter
   * @return \yii\db\ActiveQuery
   */
  public static function buildProductQuery($filter)
  {
    $query = Product::find();
    $query->innerJoin(NosqlProduct::tableName(), NosqlProduct::tableName().'.product_id='.Product::tableName().'.id');
    if($filter->priceFrom > 0) {
      $lang = \app\components\MultilangHelper::getLang();
      if($lang === 'ru') {
        $query->andWhere(NosqlProduct::tableName().'.price_rub >= :fromPrice', [':fromPrice' => $filter->priceFrom]);
      }
      if($lang === 'en') {
        $query->andWhere(NosqlProduct::tableName().'.price_usd >= :fromPrice', [':fromPrice' => $filter->priceFrom]);
      }
    }
    if($filter->priceTo > 0) {
      if(\app\components\CurrencyHelper::getCurrency() === 'rub') {
        $query->andWhere(NosqlProduct::tableName().'.price_rub <= :toPrice', [':toPrice' => $filter->priceTo]);
      } else {
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
    
    if(in_array($filter->ordering, [CatalogFilter::SORT_BY_PRICE_ASCENDING, CatalogFilter::SORT_BY_PRICE_DESCENDING])) {
      if(\app\components\CurrencyHelper::getCurrency() === 'rub') {
        $field = NosqlProduct::tableName().'.price_rub';
      } else {
        $field = NosqlProduct::tableName().'.price_usd';
      }
      $query->orderBy([$field => ($filter->ordering == CatalogFilter::SORT_BY_PRICE_ASCENDING ? SORT_ASC : SORT_DESC)]);
    }
    
    return $query;
  }
  
  /**
   * @param CatalogFilter $filter
   * @return array [min => min, max => max]
   */
  public static function getMinMaxPrice($filter)
  {
    $query = NosqlProduct::find();
    $lang = \app\components\CurrencyHelper::getCurrency();
    if($lang === 'rub') {
      $query->select(['min(price_rub) as min', 'max(price_rub) as max']);
    }
    if($lang === 'usd') {
      $query->select(['min(price_usd) as min', 'max(price_usd) as max']);
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
    $data = $query->asArray()->one();
    return $data;
  }
}