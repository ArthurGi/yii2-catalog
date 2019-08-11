<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;

/**
 * @property int $id
 * @property int $product_id
 * @property int $user_id
 * @property int $published
 * @property int $activity
 * @property int $rating
 * @property string $add_datetime
 * @property string $message
 * @property int $visible
 * @property Product $product
 * @property \app\modules\lk\models\Profile $user
 */
class Review extends ActiveRecord
{
  public $_privateAdminName;
  public $_privatePluralName;
  public $_privateAdminRepr = 'id';
  
  public static function tableName()
  {
      return '{{%review}}';
  }
  
  public function init()
  {
      parent::init();
      Module::registerTranslations();
      $this->_privateAdminName = \yii::t('modules/catalog/private', 'Review');
      $this->_privatePluralName = \yii::t('modules/catalog/private', 'Reviews');
  }
  
  public function attributeLabels()
  {
    return [
        'id' => 'ID',
        'product_id' => \yii::t('modules/catalog/private', 'Product'),
        'user_id' => \yii::t('modules/catalog/private', 'User'),
        'activity' => \yii::t('modules/catalog/private', 'Activity'),
        'published' => \yii::t('modules/catalog/private', 'Published'),
        'rating' => \yii::t('modules/catalog/private', 'Rating'),
        'add_datetime' => \yii::t('modules/catalog/private', 'Add datetime'),
        'message' => \yii::t('modules/catalog/private', 'Review'),
        'visible' => \yii::t('modules/catalog/private', 'Visible'),
    ];
  } 
  
  public function rules()
  {
    return [
        [['activity', 'published'], 'integer', 'on' => ['insert', 'update']],
        [['message'], 'string', 'on' => ['insert', 'update']],
        [['product_id', 'user_id', 'activity', 'published', 'rating', 'add_datetime'], 'safe', 'on' => ['search']]
    ];
  }
  
  public function getAdminFields()
  {
    return ['product_id', 'user_id', 'activity', 'published', 'rating', 'add_datetime'];
  }
  
  public function getFieldsDescription()
  {
    return [
        'product_id' => ['RDbRelation', 'product', 'htmlOptions' => ['disabled' => true]],
        'published' => 'RDbBoolean',
        'activity' => 'RDbBoolean',
        'message' => ['RDbText', 'forceTextArea' => 'forceTextArea']
    ];
  }
  
  public function getProduct()
  {
      return $this->hasOne(Product::className(), ['id' => 'product_id']);
  }
  
  public function getUser()
  {
    return $this->hasOne(\app\modules\lk\models\Profile::className(), ['user_id' => 'user_id']);
  }
  
}