<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;

/**
 * @property int $id
 * @property int $category_id
 * @property int $product_id
 */
class AdditionalProductCategory extends ActiveRecord
{
  public static function tableName()
  {
      return '{{%additional_product_category}}';
  }
}