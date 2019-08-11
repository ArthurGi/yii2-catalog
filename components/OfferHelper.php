<?php

namespace app\modules\catalog\components;
use app\modules\catalog\models\Offer;
use yii\helpers\Url;

class OfferHelper
{
  /**
   * 
   * @param Offer $offer
   * @return string
   */
  public static function getDetailPageUrl($offer)
  {
    return Url::to([ProductHelper::getDetailPageUrl($offer->product), '#' => 'offer-'.$offer->id], true);
  }
  
  /**
   * 
   * @param Offer $offer
   * @return string
   */
  public static function getOfferImg($offer)
  {
    if($offer->image 
            && file_exists($offer->getAbsoluteImagePath())) {
      return $offer->getImagePath();
    }
    if($offer->product->image
            && file_exists($offer->product->getAbsoluteImagePath()))
    {
      return $offer->product->getImagePath();
    }
    return null;
  }
}