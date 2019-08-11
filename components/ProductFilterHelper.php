<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;
use app\modules\catalog\models\Product;
use app\modules\catalog\models\NosqlProduct;
use yii\helpers\ArrayHelper;
use app\modules\catalog\models\AdditionalProductCategory;
use app\components\CurrencyHelper;
use app\modules\catalog\models\StockFilter;

class ProductFilterHelper
{
  /**
   * @param bool $isCountRequest
   * @return \yii\db\ActiveQuery
   */
  public static function buildProductQuery($isCountRequest = false)
  {
    $category =  \Yii::$app->catalogFilter->getCategory();
    if($category) {
      $query = static::getQueryForCategory($category);
    } else {
      $query = Product::find();
      $query->innerJoin(NosqlProduct::tableName().' as nosql_table', 'nosql_table.product_id='.Product::tableName().'.id');
    }
    $query = static::addPriceCondition($query);
    $query = static::addStockCondition($query);
    
    $filters = \Yii::$app->catalogFilter->getFilter();
    foreach($filters as $alias => $filter) {
      $type = ParamHelper::getFilterTypeIdByAlis($alias);
      $propId = ParamHelper::getParamIdByAlias($alias);
      $propName = NosqlProductHelper::getPropColumnName($propId);
      if($type == FilterHelper::TYPE_RANGE) {
        $from = ArrayHelper::getValue($filter, 'from');
        $to = ArrayHelper::getValue($filter, 'to');
        if($from !== null) {
          $slqParamId = uniqid($propName);
          $query->andWhere("nosql_table.{$propName} >= :{$slqParamId}", [":{$slqParamId}" => $from]);
        }
        if($to !== null) {
          $slqParamId = uniqid($propName);
          $query->andWhere("nosql_table.{$propName} <= :{$slqParamId}", [":{$slqParamId}" => $to]);
        }
      } elseif($type == FilterHelper::TYPE_SELECT) {
        $filter = array_map(function($item) use ($propId) { 
          return ParamHelper::getParamValueIdByAlias($propId, $item);
        }, $filter);
        $query->andWhere(["nosql_table.{$propName}" => $filter]);
      } elseif($type == FilterHelper::TYPE_MULTIPLE_SELECT) {
        $filter = array_map(function($item) use ($propId) { 
          return ParamHelper::getParamValueIdByAlias($propId, $item);
        }, $filter);
        $query->andWhere(["nosql_table.{$propName}" => $filter]);
      } elseif($type == FilterHelper::TYPE_CHECKBOX) {
        $query->andWhere(["nosql_table.{$propName}" => (array_shift($filter) == '1' ? 1 : 0)]);
      }
    }
    if($isCountRequest) { 
      $query->select(['nosql_table.product_id']);
      $query->distinct();
    } else {
      $query->groupBy([Product::tableName().'.id']);
      
      if(in_array(\Yii::$app->catalogFilter->getOrdering(), [StockFilter::SORT_BY_PRICE_ASCENDING, StockFilter::SORT_BY_PRICE_DESCENDING])) {
        if(CurrencyHelper::getCurrency() === 'rub') {
          $field = 'nosql_table.price_rub';
        } else {
          $field = 'nosql_table.price_usd';
        }
        $query->orderBy([$field => (\Yii::$app->catalogFilter->getOrdering() == StockFilter::SORT_BY_PRICE_ASCENDING ? SORT_ASC : SORT_DESC)]);
      }
    }
    return $query;
  }
  
  /**
   * 
   * @param \yii\db\ActiveQuery $query
   * @return \yii\db\ActiveQuery
   */
  public static function addPriceCondition($query)
  {
    $filters = \Yii::$app->catalogFilter->getFilter();
    $priceFrom = ArrayHelper::getValue($filters, 'price.from');
    $priceTo = ArrayHelper::getValue($filters, 'price.to');
    if($priceFrom > 0) {
      if(\app\components\CurrencyHelper::getCurrency() === 'rub') {
        $query->andWhere('nosql_table.price_rub >= :fromPrice', [':fromPrice' => (int)$priceFrom]);
      }
      if(\app\components\CurrencyHelper::getCurrency() === 'usd') {
        $query->andWhere('nosql_table.price_usd >= :fromPrice', [':fromPrice' => (int)$priceFrom]);
      }
    }
    if($priceTo > 0) {
      if(\app\components\CurrencyHelper::getCurrency() === 'rub') {
        $query->andWhere('nosql_table.price_rub <= :toPrice', [':toPrice' => (int)$priceTo]);
      }
      if(\app\components\CurrencyHelper::getCurrency() === 'usd') {
        $query->andWhere('nosql_table.price_usd <= :toPrice', [':toPrice' => (int)$$priceTo]);
      }
    }
    return $query;
  }
  
  
  /**
   * 
   * @param \yii\db\ActiveQuery $query
   * @return \yii\db\ActiveQuery
   */
  public static function addStockCondition($query)
  {
    $filters = \Yii::$app->catalogFilter->getFilter();
    $stockParamValue = ArrayHelper::getValue($filters, 'stock');
    $isStock = is_array($stockParamValue) && in_array('1', $stockParamValue) || $stockParamValue === '1';
    if($isStock) {
      $query->andWhere(['nosql_table.is_stock' => '1']);
    }
    return $query;
  }
  
  /**
   * 
   * @param Category $category
   * @return int
   */
  public static function getTotalCount($category)
  {
    $query = static::getQueryForCategory($category);
    $query->select(['nosql_table.product_id']);
    $query->distinct();
    return  $query->count();
  }
  
  /**
   * 
   * @param Category $category
   * @return \yii\db\ActiveQuery
   */
  public static function getQueryForCategory($category)
  {
    $query = Product::find();
    $query->innerJoin(NosqlProduct::tableName().' as nosql_table', 'nosql_table.product_id='.Product::tableName().'.id');
    $additionalCategorySql = AdditionalProductCategory::find()
              ->select(['product_id'])
              ->where(['category_id' => $category->id])
              ->createCommand()->getRawSql();
    $query->andWhere(['OR', ['nosql_table.category_id' => $category->id], "nosql_table.product_id IN ({$additionalCategorySql})"]);
    return $query;
  }
  
}