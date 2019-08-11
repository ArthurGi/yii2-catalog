<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

class StockFilter extends \yii\base\Model
{
  CONST SORT_BY_PRICE_ASCENDING = 1;
  CONST SORT_BY_PRICE_DESCENDING = 2;
  
  public $isHit;
  public $isNew;
  public $isStock;
  
  public $categories = [];
  
  public $priceFrom;
  
  public $priceTo;
  
  public $limit = 12;
  
  public $ordering;
  
  public function rules()
  {
    return [
        [['isHit', 'isNew', 'isStock', 'categories', 'priceFrom', 'priceTo', 'limit', 'ordering'], 'safe']
    ];
  }
  
  public function attributeLabels()
  {
    return [
        'isHit' => \yii::t('modules/catalog/stock', 'HIT'),
        'isNew' => \yii::t('modules/catalog/stock', 'NEW'),
        'isStock' => \yii::t('modules/catalog/stock', 'STOCK')
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
    return $this->isHit === '1' ||  $this->isNew === '1' || $this->isStock === '1' 
            || (is_array($this->categories) && count($this->categories) > 0)
            || $this->priceFrom > $minPrice
            || $this->priceTo < $maxPrice;
  }
}