<?php

namespace app\modules\catalog\components;
use app\modules\catalog\models\WatchedProduct;
use app\modules\catalog\models\Product;

class WatchedProductHelper
{
  CONST LIMIT = 100;
  
  CONST COOKIE_KEY = 'watched_products';
  
  /**
   * 
   * @param int $productId
   */
  public static function add($productId)
  {
    $key = self::getUserKey();
    if(WatchedProduct::find()->where(['user_key' => $key, 'product_id' => $productId])->count() > 0) {
      return;
    }
    $model = new WatchedProduct();
    if(!\Yii::$app->user->isGuest) {
      $model->user_id = (int)\Yii::$app->user->getId();
    }
    $model->user_key = self::getUserKey();
    $model->product_id = (int)$productId;
    $model->add_date =  date('Y-m-d H:i:s');
    $model->save(false);
    
    register_shutdown_function(function(){
      WatchedProductHelper::clear();
    });
  }
  
  /**
   * 
   * @return string
   */
  public static function getUserKey()
  {
    $cookie = \Yii::$app->request->cookies;
    $key = $cookie->get(self::COOKIE_KEY, null);
    if($key === null) {
      $key = md5(microtime().rand());
      $cookies = \Yii::$app->response->cookies;
      $cookies->add(new \yii\web\Cookie([
          'name' => self::COOKIE_KEY,
          'value' => $key,
          'expire' => time() + 30*3600*24,
          'path' => '/',
      ]));
    }
    return $key;
  }
  
  public static function clear()
  {
    if(rand(0, 10) !== 10) {
      return;
    }
    $condition = [];
    $condition['user_key'] = self::getUserKey();
    if(!\Yii::$app->user->isGuest) {
      $condition['user_id'] = (int)\Yii::$app->user->getId();
    }
    $sql = WatchedProduct::find()
            ->select('id')
            ->where($condition)
            ->offset(self::LIMIT)
            ->orderBy(['add_date' => SORT_DESC])
            ->createCommand()
            ->getRawSql();
    WatchedProduct::deleteAll("id IN ({$sql})");
  }
  
  
  /**
   * 
   * @param int $limit
   * @return Product[]
   */
  public static function getProducts($limit)
  {
    $condition = [];
    self::sync();
    if(\Yii::$app->user->isGuest) {
      $condition[WatchedProduct::tableName().'.user_key'] = self::getUserKey();
    } else {
      $condition[WatchedProduct::tableName().'.user_id'] = (int)\Yii::$app->user->getId();
    }
    return Product::find()->published()
            ->innerJoin(WatchedProduct::tableName(), WatchedProduct::tableName().'.product_id='.Product::tableName().'.id')
            ->andWhere($condition)
            ->limit($limit)
            ->groupBy( Product::tableName().'.id')
            ->all();
  }
  
  public static function sync()
  {
    if(\Yii::$app->user->isGuest) {
      return;
    }
    $userId = \Yii::$app->user->getId();
    if(\Yii::$app->session->get(self::class) == $userId) {
      return;
    }
    \Yii::$app->session->set(self::class, $userId);
    $userKey = self::getUserKey();
    WatchedProduct::updateAll(['user_key' => $userKey], ['user_id' => $userId]);
    WatchedProduct::updateAll(['user_id' => $userId], ['user_key' => $userKey]);
  }
  
  
}