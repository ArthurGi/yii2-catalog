<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;
/**
 * @property string $name
 * @property int $id
 * @property string $code
 * @property int $published
 * @property Products[] $products
 */
class Snippet extends ActiveRecord
{
  public $_privateAdminName;
  public $_privatePluralName;
  public $_privateAdminRepr = 'name';
  /**
   * 
   * @return string
   */
  public $codeForContent = '';
  
  public static function tableName()
  {
      return '{{%snippet}}';
  }
  
  public function init()
  {
      parent::init();
      Module::registerTranslations();
      $this->_privateAdminName = \yii::t('modules/catalog/private', 'Snippet');
      $this->_privatePluralName = \yii::t('modules/catalog/private', 'Snippets');
  }
  
  public function attributeLabels()
  {
    return [
        'id' => 'ID',
        'name' => \yii::t('modules/catalog/private', 'Name'),
        'code' => \yii::t('modules/catalog/private', 'Snippet code'),
        'products' => \yii::t('modules/catalog/private', 'Products'),
        'published' => \yii::t('modules/catalog/private', 'Published'),
        'codeForContent' => \yii::t('modules/catalog/private', 'Code for content')
    ];
  }
  
  public function rules()
  {
    return [
        [['name'], 'required', 'on' => ['insert', 'update']],
        [['code'], 'validateCode', 'skipOnEmpty' => false, 'on' => ['insert', 'update']],
        [['code'], 'unique', 'on' => ['insert', 'update']],
        [['published'], 'number', 'integerOnly' => true, 'on'=>['insert', 'update']],
        [['name', 'code', 'published'], 'safe', 'on' => ['search']]
    ];
  }
  
  public function validateCode($attribute, $params)
  {
    $this->{$attribute} = trim($this->{$attribute});
    if($this->{$attribute} === '') {
      $this->{$attribute} = strtoupper(uniqid());
    }
    if(preg_match('/^\w+$/ui', $this->{$attribute}) !== 1) {
      $this->addError($attribute, 'Код должен содержать только буквы, цифры и знак подчёркивания');
    }
  }
  
  public function getProducts()
  {
    return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('{{%snippet_product_link}}', ['snippet_id' => 'id']);
  }
  
  public function getAdminFields()
  {
    return ['id', 'code', 'published', 'products', 'codeForContent'];
  }
  
  public function getFieldsDescription()
  {
    return [
        'name' => 'RDbText',
        'code' => 'RDbText',
        'products' => ['RDbRelation', 'products'],
        'published' => 'RDbBoolean',
        'codeForContent' => 'RDbText'
    ];
  }
  
  public function getFormExcludedFields()
  {
    return ['id', 'codeForContent'];
  }
  
  public function afterFind()
  {
    parent::afterFind();
    $this->initSnippetCode();
  }
  
  protected function initSnippetCode()
  {
    $this->codeForContent = \app\modules\catalog\components\SnippetHelper::generateSnippetCode($this->code);
  }
}