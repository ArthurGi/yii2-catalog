<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;

/**
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property int $published
 * @property string $add_date
 * @property string $start_date
 * @property string $end_date
 * @property string $banner
 * @property string $dsc
 * @property Offers $offers
 */
class Special extends ActiveRecord
{
  CONST DATE_TIME_FORMAT = 'd.m.Y H:i';
  public $_privateAdminName;
  public $_privatePluralName;
  public $_privateAdminRepr = 'name';
  protected $bannerDirectory = '/catalog/special';
  
  public static function tableName()
  {
      return '{{%special}}';
  }
  
  public function init()
  {
      parent::init();
      Module::registerTranslations();
      $this->_privateAdminName = \yii::t('modules/catalog/private', 'Special');
      $this->_privatePluralName = \yii::t('modules/catalog/private', 'Specials');
  }
  
  public function attributeLabels()
  {
    return [
        'id' => 'ID',
        'name' => \yii::t('modules/catalog/private', 'Name').' (RU)',
        'name_en' => \yii::t('modules/catalog/private', 'Name').' (EN)',
        'alias' => \yii::t('modules/catalog/private', 'Alias'),
        'offers' => \yii::t('modules/catalog/private', 'Offers'),
        'published' => \yii::t('modules/catalog/private', 'Published'),
        'add_date' => \yii::t('modules/catalog/private', 'Date of publication'),
        'start_date' => \yii::t('modules/catalog/private', 'Start date'),
        'end_date' => \yii::t('modules/catalog/private', 'End date'),
        'banner' => \yii::t('modules/catalog/private', 'Banner'),
        'dsc' => \yii::t('modules/catalog/private', 'Description'),
    ];
  }
  
  public function rules()
  {
    return [
      [['name'], 'required', 'on' => ['insert', 'update']],
      [['alias'], 'app\components\AliasValidator', 'on'=>['insert', 'update'], 'skipOnEmpty' => false],
      [['banner'], 'app\components\CustomImageValidator', 'on'=>['insert', 'update']],
      [['start_date', 'end_date', 'name_en'], 'safe', 'on'=>['insert', 'update']],
      [['start_date'], 'validateActivityDates', 'skipOnEmpty' => false, 'on'=>['insert', 'update']], 
      [['add_date'], 'addDateValidator', 'skipOnEmpty' => false, 'on'=>['insert', 'update']], 
      [['published'], 'number', 'integerOnly' => true, 'on'=>['insert', 'update']],
      [['id', 'name', 'alias', 'published', 'dsc', 'add_date', 'start_date', 'end_date', 'name_en'], 'safe', 'on' => ['search']]  
    ];
  }
  
  
  public function addDateValidator($attribute, $params)
  {
    if($this->isNewRecord || !$this->{$attribute})
    {
      $this->{$attribute} = date('Y-m-d');
    }
  }
  
  public function validateActivityDates($attribute, $params)
  {
    $startTime = strtotime($this->start_date);
    $endTime = strtotime($this->end_date);
    if($startTime === false) {
       $this->addError('start_date', 'Необходимо заполнить поле «'.\yii::t('modules/catalog/private', 'Start date').'»');
    }
    if($endTime === false) {
       $this->addError('end_date', 'Необходимо заполнить поле «'.\yii::t('modules/catalog/private', 'End date').'»');
    }
    if($startTime !== false && $endTime !== false && $startTime > $endTime)
    {
      $this->addError('start_date', 'Дата в поле «'.\yii::t('modules/catalog/private', 'Start date').'» больше даты в поле «'.\yii::t('modules/catalog/private', 'End date').'»');
    }
  }
  
  /**
   * 
   * @return string
   */
  public function getBannerDirectory()
  {
      return \yii::getAlias('@webcatalog').$this->imageDirectory;
  }
  
  /**
   * Returns for uploaded image
   * @return string
   */
  public function getBannerPath()
  {
      if(!$this->image) {
          return '';
      }
      return $this->getBannerDirectory().'/'.$this->image;
  }
  
  public function getAdminFields()
  {
    return [
        'id', 'name', 'alias', 'published', 'dsc', 'add_date', 'start_date', 'end_date', 'name_en'
    ];
  }
  
  public function getFieldsDescription()
  {
    return [
        'name' => 'RDbText',
        'name_en' => 'RDbText',
        'alias'=> 'RDbText',
        'published' => 'RDbBoolean',
        'add_date' => 'RDbDate',
        'start_date' => ['RDbDateTime', 'format' => static::DATE_TIME_FORMAT, 'inputFormat' => static::DATE_TIME_FORMAT],
        'end_date' => ['RDbDateTime', 'format' => static::DATE_TIME_FORMAT, 'inputFormat' => static::DATE_TIME_FORMAT],
        'offers' => ['RDbRelation', 'offers'],
        'dsc' => 'RDbText'
    ];
  }
  
  public function getFormExcludedFields()
  {
    return ['id', 'add_date'];
  }
  
  public function getOffers()
  {
    return $this->hasMany(Offer::className(), ['id' => 'offer_id'])->viaTable('{{%special_offer_link}}', ['special_id' => 'id']);
  }
  
  public function beforeSave($insert)
  {
    $this->convertToMysqlDatetime();
    return parent::beforeSave($insert);
  }
  
  public function afterFind()
  {
    parent::afterFind();
    $this->convertFromMysqlDatetime();
  }
  
  public function convertToMysqlDatetime()
  {
    $fields = ['start_date', 'end_date'];
    foreach($fields as $field) {
      $time = strtotime($this->{$field});
      if($time !== false) {
        $this->{$field} = date('Y-m-d H:i:s', $time);
      } else {
        $this->{$filed} = null;
      }
    }
  }
  
  public function convertFromMysqlDatetime()
  {
    $fields = ['start_date', 'end_date'];
    foreach($fields as $field) {
      $time = strtotime($this->{$field});
      if($time !== false) {
        $this->{$field} = date(static::DATE_TIME_FORMAT, $time);
      } else {
        $this->{$filed} = null;
      }
    }
  }
  
  /**
   * 
   * @return \yii\db\ActiveQuery
   */
  public static function findOnlyActive()
  {
    return self::find()->where('star_date<=:now_date and end_date>=:now_date', [
        ':now_date' => date('Y-m-d H:i:s')
    ])->published();
  }
  
  /**
   * 
   * @return string
   */
  public function getName()
  {
    $attr = \app\components\MultilangHelper::getMultilangAttr('name');
    if(in_array($attr, $this->attributes()) && (string)$this->{$attr} !== '') {
      return $this->{$attr};
    }
    return $this->name;
  }
}