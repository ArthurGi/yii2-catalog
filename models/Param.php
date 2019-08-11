<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;

/**
 * @property string $name
 * @property string $alias
 * @property int $type_id
 * @property int $filter_type_id
 * @property string $external_id
 * @property int $show_in_filter
 * @property Category[] $categories
 * @property ParamValue[] $values
 * @property int $id
 */
class Param extends ActiveRecord
{
  public $_privateAdminName;
  public $_privatePluralName;
  public $_privateAdminRepr = 'name';
  
  public static function tableName()
  {
      return '{{%param}}';
  }
  
  public function init()
  {
      parent::init();
      Module::registerTranslations();
      $this->_privateAdminName = \yii::t('modules/catalog/private', 'Param');
      $this->_privatePluralName = \yii::t('modules/catalog/private', 'Params');
  }
  
  public function attributeLabels()
  {
    return [
        'id' => 'ID',
        'name' => \yii::t('modules/catalog/private', 'Name').' (RU)',
        'name_en' => \yii::t('modules/catalog/private', 'Name').' (EN)',
        'alias' => \yii::t('modules/catalog/private', 'Alias'),
        'type_id' => \yii::t('modules/catalog/private', 'Type'),
        'filter_type_id' => \yii::t('modules/catalog/private', 'Type for filter'),
        'external_id' => \yii::t('modules/catalog/private', 'External ID'),
        'show_in_filter' => \yii::t('modules/catalog/private', 'Show in filter'),
        'categories' => \yii::t('modules/catalog/private', 'Categories'),
        'unit_ru' => \yii::t('modules/catalog/private', 'Unit').' (RU)',
        'unit_en' => \yii::t('modules/catalog/private', 'Unit').' (RU)',
    ];
  }
  
  /**
   * 
   * @return array
   */
  public function rules()
  {
    return [
        [['name'], 'required', 'on' => ['insert', 'update']],
        [['name_en', 'unit_ru', 'unit_en'], 'safe', 'on' => ['insert', 'update']],
        [['external_id'], 'string', 'on' => ['insert', 'update']],
        [['alias'], 'validateAlias', 'on'=>['insert', 'update'], 'skipOnEmpty' => false],
        [['show_in_filter', 'type_id', 'filter_type_id'], 'number', 'integerOnly' => true, 'on'=>['insert', 'update']],
        [['id', 'name', 'alias', 'show_in_filter', 'type_id', 'filter_type_id', 'external_id', 'name_en'], 'safe', 'on'=>['search']]
    ];
  }
  
  public function validateAlias($attribute, $params)
  {
    $aliasValidator = new \app\components\AliasValidator();
    $aliasValidator->validateAttribute($this, $attribute);
    $this->{$attribute} = str_replace('-', '_', $this->{$attribute});
    $uniqueValidator = new \yii\validators\UniqueValidator();
    $uniqueValidator->validateAttribute($this, $attribute);
  }
  
  /**
   * 
   * @return \yii\db\ActiveQuery
   */
  public function getValues()
  {
      return $this->hasMany(ParamValue::className(), ['param_id' => 'id']);
  }
  
  public function afterSave($insert, $changedAttributes)
  {
    if($insert) {
      \app\modules\catalog\components\NosqlProductHelper::addProp($this);
    } else {
      \app\modules\catalog\components\NosqlProductHelper::updateProp($this);
    }
    parent::afterSave($insert, $changedAttributes);
  }
  
  public function afterDelete()
  {
    parent::afterDelete();
    \app\modules\catalog\components\NosqlProductHelper::delProp($this);
  }
  
  public function getAdminFields()
  {
    return [
        'id', 'name', 'alias', 'show_in_filter', 'type_id', 'filter_type_id', 'external_id', 'name_en',  'unit_ru', 'unit_en'
    ];
  }
  
  public function getFieldsDescription()
  {
    return [
        'name' => 'RDbText', 
        'name_en' => 'RDbText', 
        'alias' => 'RDbText',
        'external_id' => 'RDbText',
        'type_id' => ['RDbSelect', 'data' => \app\modules\catalog\components\ParamHelper::getTypeList()],
        'filter_type_id' => ['RDbSelect', 'data' => \app\modules\catalog\components\FilterHelper::getTypeList()],
        'unit_ru' => 'RDbText', 
        'unit_en' => 'RDbText',
        'show_in_filter' => 'RDbBoolean',
        'categories' => ['RDbRelation', 
            'categories', 
            'condition' => ['NOT IN', 'id', Category::find()->distinct()->select(['parent_id'])->column()]]
    ];
  }
  
  public function getCategories()
  {
    return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable('{{%param_category_link}}', ['param_id' => 'id']);
  }
  
  public function getGridButtonColumns($pma, $columnParams = array('buttons'=>array(), 'template'=>'')) 
  {
    $columnParams['template'] = '{values} '.$columnParams['template'];
    $columnParams['buttons']['values'] = function ($url, $model, $key) {
      if(!in_array($model->type_id, [\app\modules\catalog\components\ParamHelper::TYPE_DICTIONARY])) {
        return '';
      }
      $url = ['/private/catalog-param-values/admin/', 'param' => $model->id];
        $options = array_merge([
            'title' => \yii::t('modules/catalog/private', 'Param values'),
            'aria-label' => \yii::t('modules/catalog/private', 'Param values'),
            'class' => 'btn btn-blue btn-sm',
        ], []);
        return \yii\helpers\Html::a('<i class="fa fa-list"></i> ', $url, $options);
    };
    return $columnParams; 
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
  
   /**
   * 
   * @return string
   */
  public function getUnit()
  {
    $attr = \app\components\MultilangHelper::getMultilangAttr('unit');
    if(in_array($attr, $this->attributes()) && (string)$this->{$attr} !== '') {
      return $this->{$attr};
    }
    return $this->unit_ru;
  }
}