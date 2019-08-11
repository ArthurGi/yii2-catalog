<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;
use app\modules\catalog\models\NosqlProduct;
use app\modules\catalog\models\ParamValue;
use app\modules\catalog\models\Category;
use yii\helpers\ArrayHelper;

class FilterHelper
{
  /**
   * выпадающий список
   */
  CONST TYPE_SELECT = 1;
  /**
   * выпадающий список c множественным выбором
   */
  CONST TYPE_MULTIPLE_SELECT = 2;
  /**
   * диапазон
   */
  CONST TYPE_RANGE = 3;
  /**
   * диапазон
   */
  CONST TYPE_CHECKBOX = 4;
  
  /**
   * 
   * @param int|string $paramId
   * @param int|string $categoryId
   * @return type
   */
  public static function getEnabledParamValuesByCategory($paramId, $categoryId)
  {
    $columnName = NosqlProductHelper::getPropColumnName($paramId);
    $query = ParamValue::find()
            ->innerJoin(NosqlProduct::tableName(), NosqlProduct::tableName().'.'.$columnName.'='.ParamValue::tableName().'.id')
            ->andWhere([ParamValue::tableName().'.param_id' => $paramId])
            ->andWhere([NosqlProduct::tableName().'.category_id' => $categoryId])
            ->orderBy([ParamValue::tableName().'.name' => SORT_ASC]);
    return $query->all();
  }
  
  /**
   * @param Category $category
   * @return array
   */
  public static function getFilterItems($category)
  {
    $items = [];
    $items[] = self::getFilterItemForPrice($category);
    return array_merge($items, self::getFilterItemsForCategory($category));
  }
  
  /**
   * 
   * @param type $category
   * @return type
   */
  public static function getFilterItemsForCategory($category)
  {
    $items = [];
    $params = ParamHelper::getParamsForCategory($category);
    foreach($params as $param) {
      if($param->filter_type_id == self::TYPE_SELECT
              || $param->filter_type_id == self::TYPE_MULTIPLE_SELECT) {
        $paramValues = self::getEnabledParamValuesByCategory($param->id, $category->id);
        if(count($paramValues) === 0) {
          continue;
        }
        $values = [];
        
        $selectedParamValues = static::selectedParamValues($param->id, $category->id);
        
        foreach($paramValues as $paramValue){
          if(!in_array($paramValue->id, $selectedParamValues)) {
            continue;
          }
          $values[$paramValue->alias] = $paramValue->getName();
        }
        if(count($values) === 0) {
          continue;
        }
        $items[] = [
            'id' => $param->id,
            'type' => $param->filter_type_id,
            'alias' => $param->alias,
            'name' => $param->getName(),
            'values' => $values,
            'value' => \Yii::$app->catalogFilter->getSelectValuesForParam($param->alias)
        ];
      } elseif($param->filter_type_id == self::TYPE_RANGE) {
        $selectValue = \Yii::$app->catalogFilter->getSelectValuesForParam($param->alias);
        $minMax = self::getMinMaxForParamByCategory($param->id, $category->id);
        $items[] = [
            'id' => $param->id,
            'type' => $param->filter_type_id,
            'alias' => $param->alias,
            'name' => $param->getName(),
            'min' => $minMax[0],
            'max' => $minMax[1],
            'value' => $selectValue ? $selectValue : ['from' => 0, 'to' => 1256]
          ];
      }
    }
    return $items;
  }
  
  /**
   * 
   * @param int $propId
   * @param int $categoryId
   * @return array
   */
  public static function selectedParamValues($propId, $categoryId)
  {
    $propName = NosqlProductHelper::getPropColumnName($propId);
    $query = NosqlProduct::find()->distinct()->select([$propName]);
    $additionalCategorySql = \app\modules\catalog\models\AdditionalProductCategory::find()
              ->distinct()
              ->select(['product_id'])
              ->where(['category_id' => $categoryId])
              ->createCommand()->getRawSql();
    $query->andWhere(['OR', [NosqlProduct::tableName().'.category_id' => $categoryId], NosqlProduct::tableName().".product_id IN ({$additionalCategorySql})"]);
    $query->andWhere(['published' => 1]);
    return $query->column();
  }
  
  
  /**
   * 
   * @param Category $category
   * @return array
   */
  public static function getFilterItemForPrice($category)
  {
    $selectValue = \Yii::$app->catalogFilter->getSelectValuesForParam('price');
    return [
      'id' => 'price',
      'type' => self::TYPE_RANGE,
      'alias' => 'price',
      'name' => \yii::t('modules/catalog/app', 'Price'),
      'min' => 0,
      'max' => 1256,
      'value' => $selectValue ? $selectValue : ['from' => 0, 'to' => 1256]
    ];
  }
  
  /**
   * 
   * @param int|string $paramId
   * @param int|string $categoryId
   * @return array [min, max]
   */
  public static function getMinMaxForParamByCategory($paramId, $categoryId)
  {
    $lang = \app\components\CurrencyHelper::getCurrency();
    if($lang === 'rub') {
      $columnName = 'price_usd';
    }
    if($lang === 'usd') {
      $columnName = 'price_rub';
    }
    $additionalCategorySql = \app\modules\catalog\models\AdditionalProductCategory::find()
              ->select(['product_id'])
              ->where(['category_id' => $categoryId])
              ->createCommand()->getRawSql();
    $query = NosqlProduct::find()
            ->select(["min({$columnName}) as min", "max($columnName) as max"])
            ->andWhere(['OR', ['category_id' => $categoryId], NosqlProduct::tableName().".product_id IN ({$additionalCategorySql})"])
            ->andWhere("{$columnName} IS NOT NULL")
            ->asArray();
    $row = $query->one();
    if(!$row) {
      return [0, 0];
    }
    return [(int)$row['min'], (int)$row['max']];
  }
  
  /**
   * 
   * @param int|string $categoryId
   * @return array [min, max]
   */
  public static function getMinMaxPriceByCategory($categoryId)
  {
    $lang = \app\components\CurrencyHelper::getCurrency();
    if($lang === 'rub') {
      $columnName = 'price_rub';
    }
    if($lang === 'usd') {
      $columnName = 'price_rub';
    }
    $additionalCategorySql = \app\modules\catalog\models\AdditionalProductCategory::find()
              ->select(['product_id'])
              ->where(['category_id' => $categoryId])
              ->createCommand()->getRawSql();
    $query = NosqlProduct::find()
            ->select(["min({$columnName}) as min", "max($columnName) as max"])
            ->andWhere(['OR', ['category_id' => $categoryId], NosqlProduct::tableName().".product_id IN ({$additionalCategorySql})"])
            ->andWhere("{$columnName} IS NOT NULL")
            ->asArray();
    $row = $query->one();
    if(!$row) {
      return ['min' => 0, 'max' => 0];
    }
    return $row;
  }
  
  /**
   * 
   * @return array
   */
  public static function getTypeList()
  {
    return [
        self::TYPE_SELECT => \yii::t('modules/catalog/private', 'Select'),
        self::TYPE_MULTIPLE_SELECT => \yii::t('modules/catalog/private', 'Multiple select'),
        self::TYPE_RANGE => \yii::t('modules/catalog/private', 'Range'),
        self::TYPE_CHECKBOX => \yii::t('modules/catalog/private', 'Checkbox')
    ];
  }
  
  /**
   * 
   * @param type $data
   */
  public static function parseFilterFromFormData($data)
  {
    $priceFrom = ArrayHelper::getValue($data, 'price.from');
    $priceTo = ArrayHelper::getValue($data, 'price.to');
    \Yii::$app->catalogFilter->setRangeItem('price', $priceFrom, $priceTo);
    unset($data['price']);
    
    $category = Category::find()
            ->where(['id' => ArrayHelper::getValue($data, 'category_id')])
            ->limit(1)->one();
    \Yii::$app->catalogFilter->setCategory($category);
    
    foreach($data as $alias => $values) {
      $type = ParamHelper::getFilterTypeIdByAlis($alias);
      if(in_array($type, [self::TYPE_SELECT, self::TYPE_MULTIPLE_SELECT]) && is_array($values)) {
        foreach($values as $value) {
          \Yii::$app->catalogFilter->addSelectedItem($alias, $value);
        }
      }
      if(in_array($type, [self::TYPE_CHECKBOX]) && !is_string($values)) {
        \Yii::$app->catalogFilter->addSelectedItem($alias, $values);
      }
      if(in_array($type, [self::TYPE_RANGE])) {
        \Yii::$app->catalogFilter->setRangeItem($alias, 
                ArrayHelper::getValue($values, 'from'), 
                ArrayHelper::getValue($values, 'to'));
      }
    }
  }
}

