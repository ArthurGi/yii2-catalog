<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

/**
 * @property int $id
 * @property int $offer_id
 * @property int $param_id
 * @property string $value
 */
class OfferParamValue extends \yii\db\ActiveRecord
{
  public static function tableName()
  {
      return '{{%offer_param_value}}';
  }
}