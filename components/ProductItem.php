<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;
use app\modules\catalog\models\Product;
use app\modules\catalog\models\Offer;
use app\modules\catalog\models\OfferParamValue;

class ProductItem extends \yii\base\Component
{
  /**
   *
   * @var Product 
   */
  public $product;
  /**
   *
   * @var Offer 
   */
  public $currentOffer;
  
  /**
   *
   * @var Offer[] 
   */
  public $offers;
  
  /**
   *
   * @var null|array 
   */
  public $images;
  
  /**
   *
   * @var null|array
   */
  public $offerParams;
  
  /**
   *
   * @var null|array 
   */
  public $productParams;
 
  
  public function init()
  {
    parent::init();
    $this->initOffer();
  }
  
  protected function initOffer()
  {
    if($this->currentOffer) {
      return;
    }
    $this->offers = \yii\helpers\ArrayHelper::index($this->product->offers, 'id');
    if(count($this->offers) > 0) {
      uasort($this->offers, function($a, $b) {
        $priceA = (double)$a->getPrice();
        $priceB = (double)$b->getPrice();
        return $priceA > $priceB ? 1 : -1;
      });
      $this->currentOffer = reset($this->offers);
    }
  }
  
  /**
   * 
   * @param Offer $offer
   */
  public function setOffer($offer)
  {
    $this->currentOffer = $offer;
  }
  
  /**
   * 
   * @return string
   */
  public function getName()
  {
    return $this->product->getName();
  }
  
  /**
   * 
   * @return string
   */
  public function getPrice()
  {
    if($this->currentOffer)
    {
      return $this->currentOffer->getPrice();
    }
    return '';
  }
  
  /**
   * 
   * @return string
   */
  public function getPriceWithCurency()
  {
    $price = $this->getPrice();
    if($price === '') {
      return '';
    }
    $lang = \app\components\CurrencyHelper::getCurrency();
    if($lang === 'rub') {
      return $price.' ₽';
    }
    if($lang === 'usd') {
       return $price.' $';
    }
    return $price.' ₽';
  }
  
  public function getMainImg()
  {
    return $this->getAllImages()[0];
  }
  
  /**
   * 
   * @return array
   */
  public function getAllImages()
  {
    /* @var $offer Offer */
    if(is_array($this->images)) {
      return $this->images;
    }
    $this->images = [];
    $productPreviewImage = $this->product->getPreviewPath();
    if($productPreviewImage) {
      $this->images[] = [
          'src' => $productPreviewImage,
          'offer' => null
        ];          
    }
    if(count($this->images) === 0) {
      $productImage = $this->product->getImagePath();
      if($productImage) {
        $this->images[] = [
            'src' => $productImage,
            'offer' => null
          ];          
      }
    }
    foreach($this->offers as $offer) {
      $offerImage = $offer->getImagePath();
      if($offerImage) {
        $this->images[] = [
          'src' => $offerImage,
          'offer' => $offer->id
        ];
      }
    }
    foreach($this->product->photogallery as $photo) {
      $this->images[] = [
          'src' => $photo->getImagePath(),
          'offer' => null
      ];
    }
    if(count($this->images) === 0) {
      $this->images[] = [
          'src' => '/web/img/not-available.png',
          'offer' => null
      ];
    }
    return $this->images;
  }
  
  /**
   * 
   * @return Product
   */
  public function getProduct()
  {
    return $this->product;
  }
  
  /**
   * 
   * @return Offer
   */
  public function getOffer()
  {
    return $this->currentOffer;
  }
  
  /**
   * 
   * @return boolean
   */
  public function isSale()
  {
    return (string)$this->product->is_special === '1';
  }
  
  /**
   * 
   * @return boolean
   */
  public function isNew()
  {
    return (string)$this->product->is_new === '1';
  }
  
  /**
   * 
   * @return array
   */
  public function getOfferParams()
  {
    if(is_array($this->offerParams)) {
      return $this->offerParams;
    }
    $this->offerParams = array();
    $offerParamValues = [];
    $currentOffer = $this->getOffer();
    if($currentOffer) {
      foreach($currentOffer->paramValues as $offerParamValue) {
        $offerParamValues[$offerParamValue->param_id][] = $offerParamValue->value;
      }
    }

    foreach($this->offers as $offer) {
      foreach($offer->paramValues as $paramValue) {
        $param = ParamHelper::getParamById($paramValue->param_id);
        if(!$param) {
          continue;
        }
        
        if((string)$paramValue->value === '') {
          continue;
        }
        
        if(!array_key_exists($param->id, $this->offerParams)) {
          $this->offerParams[$param->id] = [
              'id' => $param->id,
              'name' => $param->getName(),
              'values' => []
          ];
        }
        $this->offerParams[$param->id]['values'][(string)$paramValue->value] = [
            'paramId' => $param->id,
            'value' => (string)$paramValue->value,
            'name' => ParamHelper::getValueName($param->type_id, $paramValue->value).' '.$param->getUnit(),
            'checked' => array_key_exists($param->id, $offerParamValues) && in_array($paramValue->value, $offerParamValues[$param->id])
            ];
      }
    }
    return $this->offerParams;
  }
  
  public function getProductParams()
  {
    if(is_array($this->productParams)) {
      return $this->productParams;
    }
    $this->productParams = [];
    foreach($this->product->paramValues as $paramValue) {
      $param = ParamHelper::getParamById($paramValue->param_id);
      if(!$param) {
        continue;
      }
      if((string)$paramValue->value === '') {
        continue;
      }
      if(!array_key_exists($param->id, $this->productParams)) {
          $this->productParams[$param->id] = [
              'id' => $param->id,
              'name' => $param->getName(),
              'values' => []
          ];
      }
      $this->productParams[$param->id]['values'][(string)$paramValue->value] = ParamHelper::getValueName($param->type_id, $paramValue->value).' '.$param->getUnit();
    }
    return $this->productParams;
  }
  
  /**
   * 
   * @param int $id
   */
  public function setOfferById($id) {
    if(array_key_exists($id, $this->offers)) {
      $this->setOffer($this->offers[$id]);
    }
  }
  
  /**
   * 
   * @param array $params
   * @param string $changedPropertyId
   */
  public function setOfferByParams($params, $changedPropertyId) { 
    if(!is_array($params) || !array_key_exists($changedPropertyId, $params)) {
      return;
    }
    $findValue = $params[$changedPropertyId];
    $paramValues = [];
    $similarity = [];
    foreach($this->offers as $offer) {
      foreach($offer->paramValues as $paramValue) {
        if((string)$paramValue->value === '') {
          continue;
        }
        if((int)$paramValue->param_id === (int)$changedPropertyId 
                && (string)$paramValue->value !== $findValue) {
          break;
        }
        
        if(!array_key_exists($offer->id, $similarity)) {
          $similarity[$offer->id] = 0;
        }
        if(array_key_exists($paramValue->param_id, $params)
                && (string)$params[$paramValue->param_id] === (string)$paramValue->value) {
          $similarity[$offer->id]++;
        }
      }
    }
    $this->setOfferById(array_search(max($similarity), $similarity));
  }
}