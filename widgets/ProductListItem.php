<?php
namespace app\modules\catalog\widgets;
use app\modules\catalog\models\Product;
use app\modules\catalog\components\ProductItem;
use app\modules\catalog\models\Offer;

class ProductListItem extends \yii\base\Widget
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
  public $offer;
  
  /**
   *
   * @var array 
   */
  public $offerParams;
  
  /**
   *
   * @var string 
   */
  public $changedParam;
  
  public function run()
  {
    $productItem = new ProductItem(['product' => $this->product]);
    if($this->offer) {
      $productItem->setOffer($this->offer);
    } elseif($this->offerParams && $this->changedParam) {
      $productItem->setOfferByParams($this->offerParams, $this->changedParam);
    }
    if(!$productItem->getOffer()) {
      return '';
    }
    return $this->render('product-list-item/product-list-item', [
        'productItem' => $productItem
    ]);
  }
  
}