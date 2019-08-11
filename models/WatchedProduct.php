<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;

/**
 * @property int $id
 * @property int $user_id
 * @property string $user_key
 * @property int $product_id
 * @property string $add_date
 */
class WatchedProduct extends ActiveRecord
{
  public static function tableName()
  {
      return '{{%watched_product}}';
  }
}