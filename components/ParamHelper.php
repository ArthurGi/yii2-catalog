<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;
use app\modules\catalog\models\ProductParamValue;
use app\modules\catalog\models\OfferParamValue;
use app\modules\catalog\models\Param;
use yii\helpers\ArrayHelper;
use app\modules\catalog\models\ParamValue;

class ParamHelper
{
  /**
   * целое число
   */
  CONST TYPE_INTEGER = 1;
  /**
   * строка
   */
  CONST TYPE_STRING = 2;
  /**
   * справочник
   */
  CONST TYPE_DICTIONARY = 3;
  /**
   * флажок
   */
  CONST TYPE_CHECKBOX = 4;
  
  /**
   * 
   * @return array
   */
  public static function getTypeList()
  {
    return [
        self::TYPE_INTEGER => \yii::t('modules/catalog/private', 'Integer'),
        //self::TYPE_STRING => \yii::t('modules/catalog/private', 'String'),
        self::TYPE_DICTIONARY => \yii::t('modules/catalog/private', 'Dictionary'),
        self::TYPE_CHECKBOX => \yii::t('modules/catalog/private', 'Checkbox'),
    ];
  }
  
  /**
   * 
   * @param \app\modules\catalog\models\Product $product
   * @return Param[]
   */
  public static function getProductParamsForCategory($product)
  {
    return $product->category ? self::getParamsForCategory($product->category) : [];
  }
  
  /**
   * 
   * @param \app\modules\catalog\models\Offer $offer
   * @return Param[]
   */
  public static function getOfferParamsForCategory($offer)
  {
    return $offer->product && $offer->product->category ? self::getParamsForCategory($offer->product->category) : [];
  }
  
  /**
   * 
   * @param Category $category
   * @return Param[]
   */
  public static function getParamsForCategory($category)
  {
    return $category->params;
  }
  
  /**
   * 
   * @param \app\modules\catalog\models\Product $product
   * @param \app\modules\catalog\models\Param $paramModel
   * @param mixed $value
   */
  public static function saveProductParam($product, $paramModel, $value)
  {
    $covertedValue = static::convertValue($paramModel, $value);
    if(in_array($paramModel->type_id, [static::TYPE_INTEGER, static::TYPE_STRING, static::TYPE_DICTIONARY, static::TYPE_CHECKBOX])) {
      $model = ProductParamValue::find()
            ->where([
                'product_id' => $product->id,
                'param_id' => $paramModel->id
                ])->one();
      if(!$model) {
        $model = new ProductParamValue;
        $model->product_id = $product->id;
        $model->param_id = $paramModel->id;
      }
      $model->value = $covertedValue;
      $model->save(false);
    }
  }
  
  /**
   * 
   * @param \app\modules\catalog\models\Offer $offer
   * @param \app\modules\catalog\models\Param $paramModel
   * @param mixed $value
   */
  public static function saveOfferParam($offer, $paramModel, $value)
  {
    $covertedValue = static::convertValue($paramModel, $value);
    if(in_array($paramModel->type_id, [static::TYPE_INTEGER, static::TYPE_STRING, static::TYPE_DICTIONARY, static::TYPE_CHECKBOX])) {
      $model = OfferParamValue::find()
            ->where([
                'offer_id' => $offer->id,
                'param_id' => $paramModel->id
                ])->one();
      if(!$model) {
        $model = new OfferParamValue;
        $model->offer_id = $offer->id;
        $model->param_id = $paramModel->id;
      }
      $model->value = $covertedValue;
      $model->save(false);
    }
  }
  
  /**
   * 
   * @param \app\modules\catalog\models\Param $paramModel
   * @param mixed $value
   * @return type
   */
  public static function convertValue($paramModel, $value)
  {
    if($paramModel->type_id == static::TYPE_INTEGER
            || $paramModel->type_id == static::TYPE_DICTIONARY) {
      if($value === null || $value === '') {
        return null;
      }
      return (int)$value;
    }
    if($paramModel->type_id == static::TYPE_STRING) {
      if($value === null || $value === '') {
        return null;
      }
      return (string)$value;
    }
    if($paramModel->type_id == static::TYPE_CHECKBOX) {
      return $value ? '1' : '0';
    }
    throw new \yii\base\Exception('Type not found');
  }
  
  /**
   * 
   * @staticvar array $cache
   * @param string $alias
   * @return int
   */
  public static function getFilterTypeIdByAlis($alias)
  {
    static $cache;
    if(!is_array($cache)) { 
      $cache = ArrayHelper::map(static::getParamsArray(), 'alias', 'filter_type_id');
    }
    return (int)ArrayHelper::getValue($cache, $alias);
  }
  
  /**
   * 
   * @staticvar array $cache
   * @return array
   */
  public static function getParamsArray()
  {
    static $cache;
    if(!is_array($cache)) { 
      $cache = ArrayHelper::index(Param::find()->asArray()->all(), 'id');
    }
    return $cache;
  }
  
  /**
   * 
   * @staticvar array $cache
   * @param array $alais
   * @return Param
   */
  public static function getParamById($id)
  {
    $params = self::getParamsArray();
    if(array_key_exists($id, $params)) {
      $model = new Param();
      $model->setAttributes($params[$id], false);
      return $model;
    }
    return null;
  }
  
  /**
   * 
   * @staticvar array $cache
   * @param array $alais
   * @return Param
   */
  public static function getParamByAlias($alais)
  {
    static $cache;
    if(!is_array($cache)) { 
      $cache = ArrayHelper::index(static::getParamsArray(), 'alias');
    }
    if(array_key_exists($alais, $cache)) {
      $model = new Param();
      $model->setAttributes($cache[$alais], false);
      return $model;
    }
    return null;
  }
  
  /**
   * 
   * @staticvar array $cache
   * @param string $alias
   * @return int
   */
  public static function getParamIdByAlias($alias)
  {
    static $cache;
    if(!is_array($cache)) { 
      $cache = ArrayHelper::map(static::getParamsArray(), 'alias', 'id');
    }
    return (int)ArrayHelper::getValue($cache, $alias);
  }
  
  
  /**
   * 
   * @staticvar array $cache
   * @return array
   */
  public static function getParamValuesArray()
  {
    static $cache;
    if(!is_array($cache)) { 
      $cache = ArrayHelper::index(ParamValue::find()->asArray()->all(), 'id');
    }
    return $cache;
  }
  
  /**
   * 
   * @staticvar array $cache
   * @param int|string $paramid
   * @param string $alias
   * @return int|null
   */
  public static function getParamValueIdByAlias($paramid, $alias)
  {
    static $cache;
    if(!is_array($cache)) { 
      foreach(static::getParamValuesArray() as $paramValue) {
        $cache["{$paramValue['param_id']}_{$paramValue['alias']}"] = $paramValue['id'];
      }
    }
    return (int)ArrayHelper::getValue($cache, "{$paramid}_{$alias}");
  }
  
  /**
   * 
   * @staticvar array $cache
   * @param Param $param
   * @param string|int $productId
   * @return string
   */
  public static function getParamValueNameForProduct($param, $productId)
  {
    static $cache = [];
    if(array_key_exists($productId, $cache) && array_key_exists($param->id, $cache[$productId])) {
      return $cache[$productId][$param->id];
    }
    $values = ProductParamValue::find()->select(['value'])
            ->where(['product_id' => $productId, 'param_id' => $param->id])
            ->column();
    if((int)$param->type_id === self::TYPE_DICTIONARY) {
      $names = [];
      $paramValues = self::getParamValuesArray();
      foreach($values as $value) {
        if(array_key_exists($value, $paramValues)) {
          $paramValueModel = new ParamValue();
          $paramValueModel->setAttributes($paramValues[$value], false);
          $names[] = $paramValueModel->getName();
        }
      }
      return implode(', ', $names);
    }
    return implode(', ', $values);
  }
  
  /**
   * 
   * @staticvar array $cache
   * @param Param $param
   * @param string|int $offerId
   * @return string
   */
  public static function getParamValueNameForOffer($param, $offerId)
  {
    static $cache = [];
    if(array_key_exists($offerId, $cache) && array_key_exists($param->id, $cache[$offerId])) {
      return $cache[$offerId][$param->id];
    }
    $values = OfferParamValue::find()->select(['value'])
            ->where(['offer_id' => $offerId, 'param_id' => $param->id])
            ->column();
    if((int)$param->type_id === self::TYPE_DICTIONARY) {
      $names = [];
      $paramValues = self::getParamValuesArray();
      foreach($values as $value) {
        if(array_key_exists($value, $paramValues)) {
          $paramValueModel = new ParamValue();
          $paramValueModel->setAttributes($paramValues[$value], false);
          $names[] = $paramValueModel->getName();
        }
      }
      return implode(', ', $names);
    }
    return implode(', ', $values);
  }
  
  /**
   * 
   * @param string $typeId
   * @param string $value
   * @return string|null
   */
  public static function getValueName($typeId, $value) 
  {
    if((int)$typeId === self::TYPE_DICTIONARY) {
      $paramValues = self::getParamValuesArray();
      if(array_key_exists($value, $paramValues)) {
        $paramValueModel = new ParamValue();
        $paramValueModel->setAttributes($paramValues[$value], false);
        return $paramValueModel->getName();
      }
      return null;
    }
    return $value;
  }
}