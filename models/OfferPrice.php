<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;

/**
 * @property int $id
 * @property int $offer_id
 * @property int $city_id
 * @property string $price_rub
 * @property string $price_usd
 */
class OfferPrice extends ActiveRecord
{
  public $_privateAdminName;
  public $_privatePluralName;
  
  public static function tableName()
  {
      return '{{%offer_price}}';
  }
  
  public function init()
  {
      parent::init();
      Module::registerTranslations();
      $this->_privateAdminName = \yii::t('modules/catalog/private', 'Price');
      $this->_privatePluralName = \yii::t('modules/catalog/private', 'Prices');
  }
  
  
  public function attributeLabels()
  {
    return [
        'id' => 'ID',
        'price_rub' => \yii::t('modules/catalog/private', 'Price').' (RUB)',
        'price_usd' => \yii::t('modules/catalog/private', 'Price').' (USD)',
        'offer_id' => \yii::t('modules/catalog/private', 'Offer'),
        'city_id' => \yii::t('modules/catalog/private', 'City'),
    ];
  }
  
  public function rules()
  {
    return [
        [['price_rub', 'price_usd'], 'double', 'on' => ['insert', 'update']],
        [['price_rub', 'offer_id', 'city_id'], 'required', 'on' => ['insert', 'update']],
        [['offer_id', 'city_id'], 'integer', 'on' => ['insert', 'update']],
        [['offer_id'], 'validateOfferId', 'skipOnEmpty' => false, 'on' => ['insert', 'update']],
        [['price_rub', 'price_usd', 'offer_id', 'city_id'], 'safe', 'on' => ['search']]
    ];
  }
  
  public function validateOfferId($attribute, $params = [])
  {
    if(self::find()->where(['offer_id' => $this->offer_id, 'city_id' => $this->city_id])->count() > 0) {
      $this->addError('city_id', 'Для указанного региона и торгового предложения цена уже задана');
    }
  }
  
  
  public function getOffer()
  {
    return $this->hasOne(Offer::className(), ['id' => 'offer_id']);
  }
  
  public function getCity()
  {
    return $this->hasOne(\app\models\Cities::className(), ['id' => 'city_id']);
  }
  
  public function getFieldsDescription()
  {
    return [
        'city_id' => ['RDbRelation', 'city'],
        'offer_id' => ['RDbRelation', 'offer'],
        'price_rub' => 'RDbText',
        'price_usd' => 'RDbText',
        ];
  }
  
  public function getAdminFields()
  {
    return ['price_rub', 'price_usd', 'offer_id', 'city_id'];
  }
  
}