<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

/**
 * @property int $product_id
 * @property int $offer_id
 * @property int $category_id
 * @property int $published
 * @property double $price_rub
 * @property double $price_usd
 * @property int $is_hit
 * @property int $is_new
 * @property int $is_stock
 */
class NosqlProduct extends \yii\db\ActiveRecord
{
  public static function tableName()
  {
      return '{{%nosql_product_and_offer}}';
  }
}