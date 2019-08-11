<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;
use corpsepk\yml\behaviors\YmlCategoryBehavior;

/**
 * @property int $id 
 * @property string $name
 * @property string $name_en 
 * @property string $alias
 * @property int $parent_id
 * @property int $published
 * @property string $external_id
 * @property string $dsc
 * @property string $dsc_en
 * @property string $image
 * @property int $visible
 * @property Category[] $children
 * @property Category $parent
 * @property Param[] $params
 */
class Category extends ActiveRecord
{
  public $_privateAdminName;
  public $_privatePluralName;
  protected $imageDirectory = '/catalog/categories';
  public $_privateAdminRepr = 'name';
  
  public static function tableName()
  {
      return '{{%category}}';
  }
  
  public function init()
  {
      parent::init();
      Module::registerTranslations();
      $this->_privateAdminName = \yii::t('modules/catalog/private', 'Category');
      $this->_privatePluralName = \yii::t('modules/catalog/private', 'Categories');
  }
  
  public function behaviors()
  {
      return [
          'ymlCategory' => [
              'class' => YmlCategoryBehavior::className(),
              'scope' => function ($model) {
                  /** @var \yii\db\ActiveQuery $model */
                  $model->select(['id', 'name', 'name_en', 'parent_id']);
              },
              'dataClosure' => function ($model) {
                  /** @var self $model */
                  return [
                      'id' => $model->id,
                      'name' => $model->getName(),
                      'parentId' => $model->parent_id
                  ];
              }
          ],
      ];
  }
  
  public function attributeLabels()
  {
    return [
        'id' => 'ID',
        'name' => \yii::t('modules/catalog/private', 'Name').' (RU)',
        'name_en' => \yii::t('modules/catalog/private', 'Name').' (EN)',
        'alias' => \yii::t('modules/catalog/private', 'Alias'),
        'parent_id' => \yii::t('modules/catalog/private', 'Parent'),
        'published' => \yii::t('modules/catalog/private', 'Published'),
        'external_id' => \yii::t('modules/catalog/private', 'External ID'),
        'dsc' => \yii::t('modules/catalog/private', 'Description').' (RU)',
        'dsc_en' => \yii::t('modules/catalog/private', 'Description').' (EN)',
        'image' => \yii::t('modules/catalog/private', 'Image'),
        'ordering' => \yii::t('modules/catalog/private', 'Ordering'),
        'params' => \yii::t('modules/catalog/private', 'Params'),
        'visible' => \yii::t('modules/catalog/private', 'Visible'),
    ];
  }
  
  /**
   * 
   * @return \yii\db\ActiveQuery
   */
  public function getChildren()
  {
      return $this->hasMany(Category::className(), ['parent_id' => 'id']);
  }
  
  /**
   * 
   * @return \yii\db\ActiveQuery
   */
  public function getParent()
  {
      if(!$this->isNewRecord)
      {
          $where = [];
          $params = [];

          foreach($this->getTableSchema()->primaryKey as $key)
          {
              $where[] = '`'.$key.'`!=:'.$key;
              $params[':'.$key] = $this->$key;
          }
          return $this->hasOne(Category::className(), ['id' => 'parent_id'])->andWhere(implode(' AND ', $where), $params);
      } else {
          return $this->hasOne(Category::className(), ['id' => 'parent_id']);
      }
  }
  
  /**
   * 
   * @return array
   */
  public function rules()
  {
    return [
        [['name'], 'required', 'on' => ['insert', 'update']],
        [['name_en', 'dsc_en'], 'safe', 'on' => ['insert', 'update']],
        [['alias'], 'validateAlias', 'on'=>['insert', 'update'], 'skipOnEmpty' => false],
        [['image'], 'app\components\CustomImageValidator', 'on'=>['insert', 'update']],
        [['external_id'], 'filter', 'filter' => function($value) {
                return (string) $value;
            }, 'on'=>['insert', 'update']],
        [['published', 'parent_id', 'ordering'], 'number', 'integerOnly' => true, 'on'=>['insert', 'update']],
        [['id', 'name', 'alias', 'parent_id', 'published', 'external_id', 'dsc', 'image', 'ordering', 'name_en', 'dsc_en'], 'safe', 'on' => 'search']
    ];
  }
  
  public function validateAlias($attribute, $params)
  {
    $aliasValidator = new \app\components\AliasValidator();
    $aliasValidator->validateAttribute($this, $attribute);
    $hasAliasQuery = self::find()->where(['alias' => $this->alias]);
    if($this->id > 0) {
      $hasAliasQuery->andWhere(['<>', 'id', $this->id]);
    }
    if($hasAliasQuery->count() > 0)
    {
      $this->alias = $this->alias.'-'.uniqid();
    }
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
  
  
  public function getAdminFields()
  {
    return ['name', 'alias', 'published', 'ordering', 'external_id', 'parent_id', 'name_en', 'dsc_en'];
  }
  
  public function getFieldsDescription()
  {
      $fields = array(
          'parent_id' => array('RDbRelation', 'parent', 'treeParentRel'=>'parent', 'treeChildRel' => 'children'),
          'external_id' => 'RDbText',
          'name' => 'RDbText',
          'name_en' => 'RDbText',
          'alias' => 'RDbText',
          'image' => 'RDbFile',
          'dsc' => 'RDbText',
          'dsc_en' => 'RDbText',
          'ordering' => 'RDbText',
          'published' => 'RDbBoolean'
      );
      if($this->getScenario() == 'update') {
        if(self::find()->where(['parent_id' => $this->id])->count() == 0) {
          $fields['params'] = array('RDbRelation', 'params');
        }
      }
      return $fields;
  }
  
  /**
   * 
   * @return string
   */
  public function getCategoryName()
  {
    return $this->getName();
  }
  
  /**
   * 
   * @return string
   */
  public function getRepr()
  {
    return $this->getCategoryName();
  }
  
  /**
   * Relation to meta data
   * @return ActiveQueryInterface the relational query object.
   */
  public function getMetadata()
  {
      return $this->hasOne(\app\models\Metadata::className(), ['item_id' => 'id'])
              ->where('model = :modelName', ['modelName' => self::className()]);
  }
  
  /**
   * Returs array of child relations. This information is using in back end for convinient work with child records of other models
   * array of
   * <pre>
   * array(
   *  'controller' => controller, that works with records of class of parent model,
   *  'relationName' => name of relation,
   *  'icon' => icon for visual view in action column (fontawesome class),
   *  'color' => background color for visual view in action column,
   *  'urlOptions' => extra URL params for url to list of child records,
   * )
   * </pre>
   * @return array
   */
  public function relationReferenceOptions()
  {
      return [
              'metadata' => array('controller' => 'metadata', 'relationName'=>'item', 'icon'=>'tag', 'color'=>'lilac', 'urlOptions' => ['model' => self::className()]),
      ];
  }
  
  public function getParams()
  {
    return $this->hasMany(Param::className(), ['id' => 'param_id'])->viaTable('{{%param_category_link}}', ['category_id' => 'id']);
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
  public function getDsc()
  {
    $attr = \app\components\MultilangHelper::getMultilangAttr('dsc');
    if(in_array($attr, $this->attributes()) && (string)$this->{$attr} !== '') {
      return $this->{$attr};
    }
    return $this->dsc;
  }
  
  /**
   * 
   * @return Category[]
   */
  public function generateCategories()
  {
    $parentIdList = self::find()->distinct()->select(['parent_id'])->column();
    $cats = self::find()->published()->all();
    return array_map(function ($model) {
                  /** @var self $model */
                  return [
                      'id' => $model->id,
                      'name' => $model->getName(),
                      'parentId' => $model->parent_id
                  ];
              }, $cats);
  }
  
  public function afterDelete()
  {
    $obj = new \app\components\FullTextSearchIndexing();
    $obj->dropCategory($this->id);
    parent::afterDelete();
  }
  
  public function afterSave($insert, $changedAttributes)
  {
    $obj = new \app\components\FullTextSearchIndexing();
    if($this->published) {
      $obj->indexingOneCategory($this->getAttributes());
    } else {
      $obj->dropCategory($this->id);
    }
    parent::afterSave($insert, $changedAttributes);
  }
}