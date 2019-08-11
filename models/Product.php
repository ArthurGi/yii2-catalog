<?php
/**
 * @link http://alkodesign.ru
 */

namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;
use app\modules\catalog\components\CatalogHelper;

/**
 * @property int $id
 * @property string $name
 * @property string $alias
 * @property int $category_id
 * @property int $published
 * @property string $external_id
 * @property string $preview
 * @property string $image
 * @property int $is_discount
 * @property int $is_new
 * @property int $is_hit
 * @property int $is_special
 * @property int $price
 * @property int $quantity
 * @property int $export_to_yml
 * @property int $visible
 * @property Category $category
 * @property Product[] $similarProducts
 * @property \app\models\Photogallery[] $photogallery
 * @property ProductParamValue[] $paramValues
 */
class Product extends ActiveRecord
{
    public $_privateAdminName;
    public $_privatePluralName;
    protected $imageDirectory = '/catalog/products';
    protected $previewDirectory = '/catalog/products/preview';
    public $_privateAdminRepr = 'name';

    public $params = array();

    public static function tableName()
    {
        return '{{%product}}';
    }

    public function init()
    {
        parent::init();
        Module::registerTranslations();
        $this->_privateAdminName = \yii::t('modules/catalog/private', 'Product');
        $this->_privatePluralName = \yii::t('modules/catalog/private', 'Products');
    }

    public function attributeLabels()
    {
        $labels = [
            'id' => 'ID',
            'name' => \yii::t('modules/catalog/private', 'Name') . ' (RU)',
            'name_en' => \yii::t('modules/catalog/private', 'Name') . ' (EN)',
            'alias' => \yii::t('modules/catalog/private', 'Alias'),
            'category_id' => \yii::t('modules/catalog/private', 'Category'),
            'published' => \yii::t('modules/catalog/private', 'Published'),
            'external_id' => \yii::t('modules/catalog/private', 'External ID'),
            'dsc' => \yii::t('modules/catalog/private', 'Description') . ' (RU)',
            'dsc_en' => \yii::t('modules/catalog/private', 'Description') . ' (EN)',
            'short_dsc' => \yii::t('modules/catalog/private', 'Short description') . ' (RU)',
            'short_dsc_en' => \yii::t('modules/catalog/private', 'Short description') . ' (EN)',
            'image' => \yii::t('modules/catalog/private', 'Image'),
            'preview' => \yii::t('modules/catalog/private', 'Preview'),
            'visible' => \yii::t('modules/catalog/private', 'Visible'),
            'is_new' => \yii::t('modules/catalog/private', 'New'),
            'is_special' => \yii::t('modules/catalog/private', 'Special'),
            'is_hit' => \yii::t('modules/catalog/private', 'Hit'),
            'price' => \yii::t('modules/catalog/private', 'Price'),
            'quantity' => \yii::t('modules/catalog/private', 'Quantity'),
            'is_discount' => \yii::t('modules/catalog/private', 'Discount'),
            'export_to_yml' => \yii::t('modules/catalog/private', 'Export to Yandex'),
            'similarProducts' => \yii::t('modules/catalog/private', 'Similar products'),
            'additionalCategories' => \yii::t('modules/catalog/private', 'Additional categories'),
            'interiors' => \yii::t('modules/catalog/private', 'Interiors')
        ];
        return $labels + $this->getParamLabels();
    }

    /**
     *
     * @return string
     */
    public function getDsc()
    {
        $attr = \app\components\MultilangHelper::getMultilangAttr('dsc');
        if (in_array($attr, $this->attributes()) && (string)$this->{$attr} !== '') {
            return $this->{$attr};
        }
        return $this->dsc;
    }
    
    /**
     *
     * @return string
     */
    public function getShortDsc()
    {
        $attr = \app\components\MultilangHelper::getMultilangAttr('short_dsc');
        if (in_array($attr, $this->attributes()) && (string)$this->{$attr} !== '') {
            return $this->{$attr};
        }
        return $this->short_dsc;
    }

    public function getParamLabels()
    {
        $labels = [];
        foreach (\app\modules\catalog\components\ParamHelper::getProductParamsForCategory($this) as $paramModel) {
            $labels["params[{$paramModel->id}]"] = $paramModel->name;
        }
        return $labels;
    }

    public function validateAlias($attribute, $params)
    {
        $aliasValidator = new \app\components\AliasValidator();
        $aliasValidator->validateAttribute($this, $attribute);
        if ($this->isNewRecord && self::find()->where(['alias' => $this->alias])->count() > 0) {
            $this->alias = $this->alias . '-' . uniqid();
        }
    }

    public function rules()
    {
        return [
            [['name', 'category_id'], 'required', 'on' => ['insert', 'update']],
            [['name_en', 'dsc_en'], 'safe', 'on' => ['insert', 'update']],
            [['alias'], 'validateAlias', 'on' => ['insert', 'update'], 'skipOnEmpty' => false],
            [['image'], 'app\components\CustomImageValidator', 'on' => ['insert', 'update']],
            [['preview'], 'app\components\CustomImageValidator', 'on' => ['insert', 'update']],
            [['external_id'], 'filter', 'filter' => function ($value) {
                return (string)$value;
            }, 'on' => ['insert', 'update']],
            [['params'], 'safe', 'on' => ['update']],
            [['published', 'category_id', 'is_new', 'price', 'quantity', 'is_hit', 'is_discount', 'is_special', 'export_to_yml'], 'number', 'integerOnly' => true, 'on' => ['insert', 'update']],
            [['id', 'name', 'alias', 'category_id', 'published', 'external_id',
                'dsc', 'is_discount', 'is_new', 'is_special', 'price', 'quantity', 'export_to_yml', 'name_en', 'dsc_en'], 'safe', 'on' => ['search']]
        ];
    }

    /**
     *
     * @return string
     */
    public function getImageDirectory()
    {
        return \yii::getAlias('@webcatalog') . $this->imageDirectory;
    }

    /**
     * Returns for uploaded image
     * @return string
     */
    public function getImagePath()
    {
        if (!$this->image) {
            return '';
        }
        return $this->getImageDirectory() . '/' . $this->image;
    }
    
    public function getAbsoluteImagePath()
    {
      if (!$this->image) {
            return '';
      }
      return \yii::getAlias('@webcatalogroot') . $this->imageDirectory . '/' . $this->image;
    }
    
    public function getAbsolutePreviewPath()
    {
      if (!$this->image) {
            return '';
      }
      return \yii::getAlias('@webcatalogroot') . $this->previewDirectory . '/' . $this->preview;
    }


    /**
     *
     * @return string
     */
    public function getPreviewDirectory()
    {
        return \yii::getAlias('@webcatalog') . $this->previewDirectory;
    }

    /**
     * Returns for uploaded image
     * @return string
     */
    public function getPreviewPath()
    {
        if (!$this->preview) {
            return '';
        }
        return $this->getPreviewDirectory() . '/' . $this->preview;
    }


    public function getPhotogallery()
    {
        return $this->hasMany(\app\models\Photogallery::className(), ['item_id' => 'id'])
            ->where('model = :modelName', ['modelName' => self::className()]);
    }

    public function relationReferenceOptions()
    {
        return array(
            'photogallery' => array('controller' => 'photo-gallery', 'relationName' => 'item', 'icon' => 'picture-o', 'urlOptions' => ['model' => self::className()]),
            'offers' => array('controller' => 'catalog-offers', 'relationName' => 'product', 'icon' => 'list', 'color' => 'orange', 'urlOptions' => []),
        );
    }

    public function getFieldsDescription()
    {
        $fields = array(
            'category_id' => array('RDbSelect', 'data' => static::getCatgeoryList()),
            'additionalCategories' => array('RDbRelation', 'additionalCategories'),
            'external_id' => 'RDbText',
            'name' => 'RDbText',
            'name_en' => 'RDbText',
            'alias' => 'RDbText',
            //'price' => 'RDbText',
            //'quantity' => 'RDbText',
            'image' => 'RDbFile',
            'preview' => 'RDbFile',
            'published' => 'RDbBoolean',
            'is_new' => 'RDbBoolean',
            'is_hit' => 'RDbBoolean',
            'is_special' => 'RDbBoolean',
            'is_discount' => 'RDbBoolean',
            'export_to_yml' => 'RDbBoolean',
            'short_dsc' => 'RDbText',
            'short_dsc_en' => 'RDbText',
            'dsc' => 'RDbText',
            'dsc_en' => 'RDbText',
            'similarProducts' => array('RDbRelation', 'similarProducts'),
            'interiors' => array('RDbRelation', 'interiors'),
        );
        if ($this->getScenario() === 'update') {
            $fields = $fields + $this->getFieldsForParam();
        }
        return $fields;
    }
    
    public function getFormExcludedFields()
    {
      return ['id', 'interiors'];
    }

    public function getAdminFields()
    {
        return ['category_id', 'external_id', 'name', 'alias', 'price', 'quantity',
            'published', 'is_new', 'is_special', 'is_discount', 'name_en'
        ];
    }

    /**
     *
     * @staticvar array $result
     * @return array
     */
    public static function getCatgeoryList()
    {
        static $result;
        if (is_array($result)) {
            return $result;
        }
        $result = [];
        $categories = Category::find()
            ->where(['NOT IN', 'id', Category::find()->distinct()->select(['parent_id'])->column()])
            ->all();
        foreach ($categories as $category) {
            $tempArray = [$category->getName()];
            $limit = 10;
            $parent = CatalogHelper::getModelById($category->parent_id);
            while ($parent && $limit > 0) {
                $tempArray[] = $parent->getName();
                $parent = CatalogHelper::getModelById($parent->parent_id);
                $limit--;
            }
            $result[$category->id] = implode(' -> ', array_reverse($tempArray));
        }
        asort($result, SORT_NATURAL | SORT_FLAG_CASE);
        return $result;
    }

    public function getFieldsForParam()
    {
        $fields = [];
        foreach (\app\modules\catalog\components\ParamHelper::getProductParamsForCategory($this) as $paramModel) {
            $fields["params[{$paramModel->id}]"] = [
                'RDbParam',
                'paramModel' => $paramModel,
                'label' => \yii::t('modules/catalog/private', 'Param') . ': ' . $this->getAttributeLabel("params[{$paramModel->id}]"),
                'htmlOptions' => ['value' => ProductParamValue::find()->select(['value'])->where([
                    'product_id' => $this->id,
                    'param_id' => $paramModel->id
                ])->scalar()]
            ];
        }
        return $fields;
    }

    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    public function getSimilarProducts()
    {
        if (!$this->isNewRecord) {
            $where = [];
            $params = [];

            foreach ($this->getTableSchema()->primaryKey as $key) {
                $where[] = '`' . $key . '`!=:' . $key;
                $params[':' . $key] = $this->$key;
            }
            return $this->hasMany(Product::className(), ['id' => 'product_id2'])->viaTable('{{%catalog_similar_products}}', ['product_id1' => 'id'])->published()->andWhere(implode(' AND ', $where), $params);
        } else {
            return $this->hasMany(Product::className(), ['id' => 'product_id2'])->viaTable('{{%catalog_similar_products}}', ['product_id1' => 'id'])->published();
        }
    }

    public function getAdditionalCategories()
    {
        $parentIdList = Category::find()->distinct()->select(['parent_id'])->column();
        return $this->hasMany(Category::className(), ['id' => 'category_id'])
            ->viaTable(AdditionalProductCategory::tableName(), ['product_id' => 'id'])
            ->where(['NOT IN', 'id', $parentIdList]);
    }

    public function getInteriors()
    {
        return $this->hasMany(Interior::className(), ['id' => 'interior_id'])->viaTable('{{%interior_product_link}}', ['product_id' => 'id'])->published();
    }

    /**
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOffers()
    {
        return $this->hasMany(Offer::className(), ['product_id' => 'id']);
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
        return $this->name;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->saveParams();
        parent::afterSave($insert, $changedAttributes);
        $this->changeNosqlProduct($insert, $changedAttributes);
        
    }
    
    public function searchIndexing()
    {
      $obj = new \app\components\FullTextSearchIndexing();
      if($this->published) {
        $obj->indexingOneProduct($this->getAttributes());
      } else {
        $obj->dropProduct($this->id);
      }
    }

    protected function changeNosqlProduct($insert, $changedAttributes)
    {
        if ($insert) {
            if ($this->visible) {
                \app\modules\catalog\components\NosqlProductHelper::addProduct($this);
            }
        } else {
            if ($this->visible) {
                \app\modules\catalog\components\NosqlProductHelper::updateProduct($this);
            }
        }
        if (!$this->visible) {
            \app\modules\catalog\components\NosqlProductHelper::delProduct($this);
        }
    }

    /**
     *
     * @return null
     */
    public function saveParams()
    {
        if (!is_array($this->params) || count($this->params) == 0) {
            return;
        }
        $notUnsetParams = [];
        foreach ($this->params as $id => $value) {
            $notUnsetParams[] = $id;
            $paramModel = Param::find()->where(['id' => $id])->limit(1)->one();
            if ($paramModel) {
                $notUnsetParams[$paramModel->id] = $paramModel->id;
                \app\modules\catalog\components\ParamHelper::saveProductParam($this, $paramModel, $value);
            }
        }
        ProductParamValue::deleteAll(['AND', ['product_id' => $this->id], ['NOT IN', 'param_id', $notUnsetParams]]);
    }
    
    public function afterDelete()
    {
      \app\modules\catalog\components\NosqlProductHelper::delProduct($this);
      
      $obj = new \app\components\FullTextSearchIndexing();
      $obj->dropProduct($this->id);
      parent::afterDelete();
      
    }
    
    public function getParamValues()
    {
      return $this->hasMany(ProductParamValue::className(), ['product_id' => 'id']);
    }
    
    public function getUpdateMenu($pma, $menu)
    {
      $addItems = [
          'offers' => [
              'label'=> \yii::t('modules/catalog/private', 'Offers'), 
              'url'=>['/private/catalog-offers/admin', 'product' => $this->id], 
              'visible' => \yii::$app->user->can('/private/catalog-offers/admin'), 
              'template' => '<a href="{url}" class="btn btn-orange"><i class="fa fa-list"></i> {label}</a>'],
      ];
      return $menu;
    }
}