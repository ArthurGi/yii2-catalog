<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;
use app\modules\catalog\models\Product;
use app\modules\catalog\models\Offer;
use app\modules\catalog\models\Param;
use app\modules\catalog\models\NosqlProduct;
use app\modules\catalog\models\ProductParamValue;
use app\modules\catalog\models\OfferParamValue;

class NosqlProductHelper extends \yii\base\Component
{
  /**
   * 
   * @param Param $propModel
   */
  public static function addProp($propModel)
  {
    $db = NosqlProduct::getDb();
    $columnName = static::getPropColumnName($propModel->id);
    $hasColumn = NosqlProduct::getTableSchema()->getColumn($columnName) != null;
    if($hasColumn) {
      NosqlProduct::updateAll([$columnName => null]);
      return;
    }
    $migration = new \yii\db\Migration([
        'db' => $db
    ]);
    $migration->addColumn(NosqlProduct::tableName(), $columnName, \yii\db\mysql\Schema::TYPE_STRING);
  }
  
  /**
   * 
   * @param Param $propModel
   */
  public static function updateProp($propModel)
  {
    $db = NosqlProduct::getDb();
    $columnName = static::getPropColumnName($propModel->id);
    $hasColumn = NosqlProduct::getTableSchema()->getColumn($columnName) != null;
    if(!$hasColumn) {
      $migration = new \yii\db\Migration([
        'db' => $db
      ]);
      $migration->addColumn(NosqlProduct::tableName(), $columnName, \yii\db\mysql\Schema::TYPE_STRING);
    }
  }
  
  /**
   * 
   * @param Param $propModel
   */
  public static function delProp($propModel)
  {
    $db = NosqlProduct::getDb();
    $columnName = static::getPropColumnName($propModel->id);
    $hasColumn = NosqlProduct::getTableSchema()->getColumn($columnName) != null;
    if($hasColumn) {
      $migration = new \yii\db\Migration([
        'db' => $db
      ]);
      $migration->dropColumn(NosqlProduct::tableName(), $columnName);
    }
  }
  
  public static function recalcProductPrice($productId)
  {
    $minRub = NosqlProduct::find()->select(['min(price_rub) as min_rub'])
            ->andWhere(['product_id' => $productId])
            ->andWhere('offer_id>0 AND price_rub>0 AND price_rub IS NOT NULL')
            ->andWhere('published=1')
            ->scalar();
    $minUsd = NosqlProduct::find()->select(['min(price_usd) as min_usd'])
            ->andWhere(['product_id' => $productId])
            ->andWhere('offer_id>0 AND price_usd>0 AND price_usd IS NOT NULL')
            ->andWhere('published=1')
            ->scalar();
    NosqlProduct::updateAll(['price_rub' => $minRub, 'price_usd' => $minUsd], 
            ['product_id' => $productId, 'offer_id' => 0]);
  }
  
  /**
   * 
   * @param Product $productModel
   */
  public static function addProduct($productModel)
  {
    $model = NosqlProduct::find()->where(['product_id' => $productModel->id, 'offer_id' => 0])->one();
    if(!$model) {
      $model = new NosqlProduct();
      $model->product_id = $productModel->id;
      $model->offer_id = 0;
    }
    $model->category_id = $productModel->category_id;
    $model->published = (int)$productModel->published;
    $model->is_stock = (int)$productModel->is_discount;
    $model->is_hit = (int)$productModel->is_hit;
    $model->is_new = (int)$productModel->is_new;
    $model->save(false);
    static::setProductProps($productModel);
    static::recalcProductPrice($productModel->id);
  }
  
  /**
   * 
   * @param Product $productModel
   */
  public static function delProduct($productModel)
  {
    NosqlProduct::deleteAll(['product_id' => $productModel->id]);
  }
  
  /**
   * 
   * @param Product $productModel
   */
  public static function updateProduct($productModel)
  {
    $hasProductRow = false;
    $models = NosqlProduct::find()->where(['product_id' => $productModel->id])->all();
    foreach($models as $model) {
      if($model->offer_id == 0) {
        $hasProductRow = true;
      }
      $model->category_id = $productModel->category_id;
      $model->published = (int)$productModel->published;
      $model->is_stock = (int)$productModel->is_discount;
      $model->is_hit = (int)$productModel->is_hit;
      $model->is_new = (int)$productModel->is_new;
      $model->save(false);
    }
    if(!$hasProductRow) {
      static::addProduct($productModel);
      return;
    }
    static::setProductProps($productModel);
    foreach($productModel->offers as $offer) {
      self::updateOffer($offer);
    }
  }
  
  /**
   * 
   * @param Offer $offerModel
   */
  public static function addOffer($offerModel)
  {
    static::addProduct($offerModel->product);
    $model = NosqlProduct::find()->where(['product_id' => $offerModel->product_id, 'offer_id' => $offerModel->id])->one();
    if(!$model) {
      $model = new NosqlProduct();
      $model->product_id =  $offerModel->product_id;
      $model->offer_id = $offerModel->id;
    }
    $model->category_id = $offerModel->product->category_id;
    $model->published = (int)($offerModel->product->published && $offerModel->published);
    $model->is_stock = (int)$offerModel->product->is_discount;
    $model->is_hit = (int)$offerModel->product->is_hit;
    $model->is_new = (int)$offerModel->product->is_new;
    $model->price_rub = (double)$offerModel->price_rub;
    $model->price_usd = (double)$offerModel->price_usd;
    $model->save(false);
    static::setOfferProps($offerModel);
  }
  
  /**
   * 
   * @param Offer $offerModel
   */
  public static function delOffer($offerModel)
  {
    NosqlProduct::deleteAll(['offer_id' => $offerModel->id]);
    static::recalcProductPrice($offerModel->product->id);
  }
  
  /**
   * 
   * @param Offer $offerModel
   */
  public static function updateOffer($offerModel)
  {
    $model = NosqlProduct::find()->where(['product_id' => $offerModel->product_id, 'offer_id' => $offerModel->id])->one();
    if(!$model) {
      static::addOffer($offerModel);
      return;
    }
    $model->category_id = $offerModel->product->category_id;
    $model->published = (int)($offerModel->product->published && $offerModel->published);
    $model->price_rub = (double)$offerModel->price_rub;
    $model->price_usd = (double)$offerModel->price_usd;
    $model->save(false);
    static::setOfferProps($offerModel);
    static::recalcProductPrice($offerModel->product->id);
  }
  
  /**
   * 
   * @param Prodcut $productModel
   */
  public static function setProductProps($productModel)
  {
    $props = [];
    $models = ProductParamValue::find()
            ->where(['product_id' => $productModel->id])
            ->all();
    foreach($models as $model)
    {
      $columnName = static::getPropColumnName($model->param_id);
      $props[$columnName] = $model->value;
    }
    $params = Param::find()->select(['id'])
            ->asArray()->column();
    foreach($params as $id)
    {
      $columnName = static::getPropColumnName($id);
      if(array_key_exists($columnName, $props)) {
        continue;
      } else {
        $props[$columnName] = null;
      }
    }
    if(count($props) > 0) {
      NosqlProduct::updateAll($props, ['product_id' => $productModel->id]);
    }
  }
  
  /**
   * 
   * @param Offer $offerModel
   */
  public static function setOfferProps($offerModel)
  {
    $props = [];
    $params = $offerModel->product->category->params;
    $models = OfferParamValue::find()
            ->where(['offer_id' => $offerModel->id])
            ->all();
    foreach($models as $model)
    {
      $columnName = static::getPropColumnName($model->param_id);
      $props[$columnName] = $model->value;
    }
    foreach($params as $param) {
      $columnName = static::getPropColumnName($param->id);
      if(isset($props[$columnName])) {
        continue;
      }
      $productParamValue = ProductParamValue::find()
              ->where(['product_id' => $offerModel->product->id, 'param_id' => $param->id])
              ->scalar();
      $props[$columnName] = ($productParamValue === false ? null : $productParamValue);
    }
    $params = Param::find()->select(['id'])
            ->asArray()->column();
    foreach($params as $id)
    {
      $columnName = static::getPropColumnName($id);
      if(array_key_exists($columnName, $props)) {
        continue;
      } else {
        $props[$columnName] = null;
      }
    }
    if(count($props) > 0) {
      NosqlProduct::updateAll($props, ['product_id' => $offerModel->product_id, 'offer_id' => $offerModel->id]);
    }
  }
  
  /**
   * 
   * @param int_string $id
   */
  public static function getPropColumnName($id)
  {
    return 'prop_'.$id;
  }
}