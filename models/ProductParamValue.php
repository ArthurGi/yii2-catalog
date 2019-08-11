<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

/**
 * @property int $id
 * @property int $product_id
 * @property int $param_id
 * @property string $value
 */
class ProductParamValue extends \yii\db\ActiveRecord
{
  public static function tableName()
  {
      return '{{%product_param_value}}';
  }
}