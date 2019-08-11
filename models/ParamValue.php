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
 * @property int $param_id
 * @property string $external_id
 * @property Param $param
 */
class ParamValue extends ActiveRecord
{
  public $_privateAdminName;
  public $_privatePluralName;
  public $_privateAdminRepr = 'name';
  
  public static function tableName()
  {
      return '{{%param_value}}';
  }
  
  public function init()
  {
      parent::init();
      Module::registerTranslations();
      $this->_privateAdminName = \yii::t('modules/catalog/private', 'Param value');
      $this->_privatePluralName = \yii::t('modules/catalog/private', 'Param values');
  }
  
  public function attributeLabels()
  {
    return [
        'id' => 'ID',
        'name' => \yii::t('modules/catalog/private', 'Name').' (RU)',
        'name_en' => \yii::t('modules/catalog/private', 'Name').' (EN)',
        'alias' => \yii::t('modules/catalog/private', 'Alias'),
        'param_id' => \yii::t('modules/catalog/private', 'Param'),
        'external_id' => \yii::t('modules/catalog/private', 'External ID'),
    ];
  }
  
   /**
   * 
   * @return array
   */
  public function rules()
  {
    return [
        [['name', 'param_id'], 'required', 'on' => ['insert', 'update']],
        [['name_en'], 'safe', 'on' => ['insert', 'update']],
        [['external_id'], 'string', 'on' => ['insert', 'update']],
        [['alias'], 'validateAlias', 'on'=>['insert', 'update'], 'skipOnEmpty' => false],
        [['param_id'], 'number', 'integerOnly' => true, 'on'=>['insert', 'update']],
        [['id', 'name', 'alias', 'param_id', 'external_id', 'name_en'], 'safe', 'on'=>['search']]
    ];
  }
  
  public function validateAlias($attribute, $params)
  {
    $aliasValidator = new \app\components\AliasValidator();
    $aliasValidator->validateAttribute($this, $attribute);
    $this->{$attribute} = str_replace('-', '_', $this->{$attribute});
    $uniqueValidator = new \yii\validators\UniqueValidator(['targetAttribute' => ['alias', 'param_id']]);
    $uniqueValidator->validateAttribute($this, $attribute);
  }
  
  public function getParam()
  {
    return $this->hasOne(Param::className(), ['id' => 'param_id']);
  }
  
  public function getFieldsDescription()
  {
    return [
        'param_id' => ['RDbRelation', 'param', 'condition' => ['type_id' => \app\modules\catalog\components\ParamHelper::TYPE_DICTIONARY]],
        'name' => 'RDbText',
        'name_en' => 'RDbText',
        'alias' => 'RDbText',
        'external_id' => 'RDbText',
    ];
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