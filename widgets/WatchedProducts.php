<?php

namespace app\modules\catalog\widgets;
use app\modules\catalog\components\WatchedProductHelper;

class WatchedProducts extends \yii\base\Widget
{
  CONST LIMIT = 12;
  
  public function run()
  {
    $products = WatchedProductHelper::getProducts(self::LIMIT);
    if(count($products) < 4) {
      return '';
    }
    \Yii::$app->view->registerJsFile('/web/js/watched-products.js');
    return $this->render('watched-products/watched-products', [
        'products' => $products
    ]);
  }
}