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
 * @property int $published
 * @property string $code
 * @property string $image
 * @property Products $products
 * @property \app\models\Photogallery[] $photogallery
 */
class Interior extends ActiveRecord
{
  public $_privateAdminName;
  public $_privatePluralName;
  protected $imageDirectory = '/catalog/interior';
  public $_privateAdminRepr = 'name_ru';
  /**
   * 
   * @return string
   */
  public $codeForContent = '';
  
  public static function tableName()
  {
      return '{{%interior}}';
  }
  
  public function init()
  {
      parent::init();
      Module::registerTranslations();
      $this->_privateAdminName = \yii::t('modules/catalog/private', 'Interior');
      $this->_privatePluralName = \yii::t('modules/catalog/private', 'Interiors');
  }
  
  public function attributeLabels()
  {
    return [
        'id' => 'ID',
        'name_ru' => \yii::t('modules/catalog/private', 'Name').' (RU)',
        'name_en' => \yii::t('modules/catalog/private', 'Name').' (EN)',
        'code' => \yii::t('modules/catalog/private', 'Snippet code'),
        'products' => \yii::t('modules/catalog/private', 'Products'),
        'published' => \yii::t('modules/catalog/private', 'Published'),
        'codeForContent' => \yii::t('modules/catalog/private', 'Code for content'),
        'image' => \yii::t('modules/catalog/private', 'Image')
    ];
  }
  
  /**
   * 
   * @return string
   */
  public function getImageDirectory()
  {
      return \yii::getAlias('@webcatalog').$this->imageDirectory;
  }
  
  /**
   * Returns for uploaded image
   * @return string
   */
  public function getImagePath()
  {
      if(!$this->image) {
          return '';
      }
      return $this->getImageDirectory().'/'.$this->image;
  }
  
  public function rules()
  {
    return [
        [['name_ru'], 'required', 'on' => ['insert', 'update']],
        [['name_en'], 'string', 'on' => ['insert', 'update']],
        [['code'], 'validateCode', 'skipOnEmpty' => false, 'on' => ['insert', 'update']],
        [['code'], 'unique', 'on' => ['insert', 'update']],
        [['published'], 'number', 'integerOnly' => true, 'on'=>['insert', 'update']],
        [['image'], 'app\components\CustomImageValidator', 'on'=>['insert', 'update']],
        [['name_ru', 'name_en', 'code', 'published'], 'safe', 'on' => ['search']]
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
    return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('{{%interior_product_link}}', ['interior_id' => 'id']);
  }
  
  public function getAdminFields()
  {
    return ['id', 'code', 'published', 'products', 'codeForContent', 'name_ru', 'name_en'];
  }
  
  public function getFieldsDescription()
  {
    return [
        'name_ru' => 'RDbText',
        'name_en' => 'RDbText',
        'code' => 'RDbText',
        'image' => 'RDbFile',
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
    $this->codeForContent = \app\modules\catalog\components\InteriorHelper::generateSnippetCode($this->code);
  }
  
  
  /**
   *
   * @return string
   */
  public function getName()
  {
      $attr = \app\components\MultilangHelper::getMultilangAttr('name');
      if (in_array($attr, $this->attributes()) && (string)$this->{$attr} !== '') {
          return $this->{$attr};
      }
      return $this->name_ru;
  }
  
  public function getPhotogallery()
  {
      return $this->hasMany(\app\models\Photogallery::className(), ['item_id' => 'id'])
          ->where('model = :modelName', ['modelName' => self::className()]);
  }
  
  public function relationReferenceOptions()
  {
      return array(
          'photogallery' => array('controller' => 'catalog-photo-gallery', 'relationName' => 'item', 'icon' => 'picture-o', 'urlOptions' => ['model' => self::className()]),
      );
  }
}