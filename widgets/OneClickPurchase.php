<?php

namespace app\modules\catalog\widgets;
use app\modules\catalog\models\OneClickPurchaseForm;

class OneClickPurchase extends \yii\base\Widget
{
  /**
   *
   * @var \app\modules\catalog\models\Product 
   */
  public $product;
  /**
   *
   * @var \app\modules\catalog\models\Offer 
   */
  public $offer;
  
  public function run()
  {
    if(!\Yii::$app->request->isAjax) {
      \Yii::$app->view->registerJsFile('/web/js/one-click-purchase.js');
    }
    $model = new OneClickPurchaseForm();
    if(\Yii::$app->request->isPost) {
      $model->load(\Yii::$app->request->post());
      if($model->save()) {
        return  $this->render('one-click-purchase/success');
      }
    }
    if($this->product) {
      $model->product_id = $this->product->id;
    } elseif($this->offer) {
      $model->product_id = $this->offer->product_id;
    }
    if($this->offer) {
      $model->offer_id = $this->offer->id;
    }
    return $this->render('one-click-purchase/one-click-purchase', [
        'model' => $model,
        'product' => $this->product,
        'offer' => $this->offer
    ]);
  }
}