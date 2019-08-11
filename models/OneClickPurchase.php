<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;
use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;

/**
 * @property int $id
 * @property string $phone
 * @property string $name
 * @property int $product_id
 * @property int $offer_id
 * @property string $add_date_time
 * @property string $comment
 * @property int $status
 * @property Offer $offer
 * @property Product $product
 */
class OneClickPurchase extends ActiveRecord
{
  CONST STATUS_NEW = 0;
  CONST STATUS_PROCESSED = 1;
  public $_privateAdminName;
  public $_privatePluralName;
  public $_privateAdminRepr = 'phone';
  
  public static function tableName()
  {
      return '{{%one_click_purchase}}';
  }
  
  public function init()
  {
      parent::init();
      Module::registerTranslations();
      $this->_privateAdminName = \yii::t('modules/catalog/private', 'One click purchase');
      $this->_privatePluralName = \yii::t('modules/catalog/private', 'One click purchases');
  }
  
  public function rules()
  {
    return [
        [['id', 'phone', 'name', 'add_date_time', 'comment', 'status'], 'safe', 'on'=>['search']]
    ];
  }
  
  public function attributeLabels()
  {
    return [
        'id' => 'ID',
        'name' => \yii::t('modules/catalog/private', 'Name'),
        'phone' => \yii::t('modules/catalog/private', 'Phone'),
        'product_id' => \yii::t('modules/catalog/private', 'Product'),
        'offer_id' => \yii::t('modules/catalog/private', 'Offer'),
        'add_date_time' => \yii::t('modules/catalog/private', 'Add datetime'),
        'comment' => \yii::t('modules/catalog/private', 'Comment'),
        'status' => \yii::t('modules/catalog/private', 'Status')
    ];
  }
  
  public function getAdminFields()
  {
    return ['name', 'phone', 'product_id', 'offer_id', 'add_date_time', 'comment', 'status'];
  }
  
  public function getFieldsDescription()
  {
    return [
        'name' => 'RDbText',
        'phone' => 'RDbText',
        'product_id' => ['RDbRelation', 'product'],
        'offer_id' => ['RDbRelation', 'offer'],
        'comment' => 'RDbText',
        'status' => ['RDBSelect', 'data' => self::getStatusList()],
        ];
  }
  
  public function getProduct()
  {
    return $this->hasOne(Product::className(), ['id' => 'product_id']);
  }
  
  public function getOffer()
  {
    return $this->hasOne(Offer::className(), ['id' => 'offer_id']);
  }
  
  public static function getStatusList()
  {
    return [
        self::STATUS_NEW => \yii::t('modules/catalog/private', 'New'),
        self::STATUS_PROCESSED => \yii::t('modules/catalog/private', 'Processed'),
    ];
  }
  
  /**
   * 
   * @return \app\components\CommonActiveQuery
   */
  public static function find()
  {
    $query = parent::find();
    $query->defaultOrder = ['id' => SORT_DESC];
    return $query;
  }
}