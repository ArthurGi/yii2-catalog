<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;

use app\modules\catalog\models\Product;
use app\modules\catalog\models\Offer;
use app\modules\catalog\models\Category;
use app\modules\catalog\models\OfferParamValue;

class OffersParamHelper
{
  /**
   *
   * @var Category 
   */
  protected $category;
  /**
   *
   * @var Product 
   */
  protected $product;
  
  /**
   *
   * @var Offer[] 
   */
  protected $offers;
  /**
   *
   * @var null|array
   */
  protected $productParams = null;
  
  protected $offerParams = null;
  
  /**
   * 
   * @param Product $product
   * @param Offer[] $offers
   * @param Category $category
   */
  public function __construct($product, $offers, $category)
  {
    $this->product = $product;
    $this->category = $category;
    $this->offers = $offers;
  }
  
  /**
   * 
   * @return Offer|false
   */
  public function getCurrentOffer()
  {
    return reset($this->offers);
  }
  
  /**
   * 
   * @return boolean
   */
  public function hasOffers()
  {
    return count($this->offers) > 0;
  }
  
  public function getProductParams()
  {
    $offerParams = $this->getOfferParams();
  }
  
  public function getOfferParams()
  {
    $params = \yii\helpers\ArrayHelper::index(ParamHelper::getOfferParamsForCategory($this->category), 'id');
    if(is_array($this->offerParams)) {
      return $this->offerParams;
    }
    $offerIds = [];
    foreach($this->offers as $offer) {
        $offerIds = $offer->id;
    }
    $paramValues = OfferParamValue::find()
            ->where(['IN', 'offer_id', $offerIds])->asArray()->all();
    foreach($paramValues as $paramValue) {
      if(!array_key_exists($paramValue['param_id'], $params)) {
        continue;
      }
      if(!isset($this->offerParams[$paramValue['param_id']])) {
        
      }
    }
  }
}