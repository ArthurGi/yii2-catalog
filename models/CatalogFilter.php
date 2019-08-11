<?php

namespace app\modules\catalog\models;

class CatalogFilter extends \yii\base\Model
{
  CONST SORT_BY_PRICE_ASCENDING = 1;
  CONST SORT_BY_PRICE_DESCENDING = 2;
  public $categories = [];
  
  public $priceFrom;
  
  public $priceTo;
  
  public $limit = 12;
  
  public $ordering;
  
  public $params = [];
  
  public $isStock;
  
  public function rules()
  {
    return [
        [['categories', 'priceFrom', 'priceTo', 'limit', 'ordering', 'params', 'isStock'], 'safe']
    ];
  }
  
  /**
   * 
   * @return array
   */
  public static function getOrderingItems()
  { 
    return [
        self::SORT_BY_PRICE_ASCENDING => \yii::t('modules/catalog/stock', 'Price ascending'),
        self::SORT_BY_PRICE_DESCENDING => \yii::t('modules/catalog/stock', 'Price descending')
        ];
  }
  
  /**
   * 
   * @return array
   */
  public static function getLimitItems()
  {
    return [
        12 => 12,
        24 => 24,
        48 => 48,
        96 => 96,
        192 => 192
    ];
  }
  
  public function isShowResetBtn($minPrice, $maxPrice)
  {
    return (is_array($this->params) && count($this->params) > 0)
            || $this->priceFrom > $minPrice
            || $this->priceTo < $maxPrice;
  }
}
