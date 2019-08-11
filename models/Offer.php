<?php
/**
 * @link http://alkodesign.ru
 */

namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;
use corpsepk\yml\behaviors\YmlOfferBehavior;

/**
 * @property int $id
 * @property int $product_id
 * @property int $external_id
 * @property int $published
 * @property string $price_rub
 * @property string $price_usd
 * @property string $image
 * @property int $quantity
 * @property int $export_to_yml
 * @property int $visible
 * @property string $ean
 * @property Product $product
 * @property OfferParamValue[] $paramValues
 */
class Offer extends ActiveRecord
{
    public $_privateAdminName;
    public $_privatePluralName;
    public $_privateAdminRepr = 'product_id';
    public $params = array();
    public $imageDirectory = '/catalog/offers';

    public static function tableName()
    {
        return '{{%offer}}';
    }

    public function init()
    {
        parent::init();
        Module::registerTranslations();
        $this->_privateAdminName = \yii::t('modules/catalog/private', 'Offer');
        $this->_privatePluralName = \yii::t('modules/catalog/private', 'Offers');
    }
    
    public function behaviors()
    {
        return [
            'ymlOffer' => [
                'class' => YmlOfferBehavior::className(),
                'scope' => function ($model) {
                    /** @var \yii\db\ActiveQuery $model */
                    $model->andWhere(['export_to_yml' => 1])->andWhere('price_rub>0');
                },
                'dataClosure' => function ($model) {
                    /** @var self $model */
                    return new \corpsepk\yml\models\Offer([
                        'id' => $model->id,
                        'url' => \app\modules\catalog\components\OfferHelper::getDetailPageUrl($model),
                        'price' => $model->getPrice(),
                        'currencyId' => 'RUR',
                        'categoryId' => $model->product->category_id,
                        'name' => $model->product->getName(),
                        'vendor' => null,
                        'description' => $model->product->getDsc(),
                    ]);
                }
            ],
        ];
    }
   

    public function attributeLabels()
    {
        $labels = [
            'id' => 'ID',
            'product_id' => \yii::t('modules/catalog/private', 'Product'),
            'external_id' => \yii::t('modules/catalog/private', 'External ID'),
            'published' => \yii::t('modules/catalog/private', 'Published'),
            'visible' => \yii::t('modules/catalog/private', 'Visible'),
            'price' => \yii::t('modules/catalog/private', 'Price'),
            'quantity' => \yii::t('modules/catalog/private', 'Quantity'),
            'export_to_yml' => \yii::t('modules/catalog/private', 'Export to Yandex'),
            'price_rub' => \yii::t('modules/catalog/private', 'Price').' (RUB)',
            'price_usd' => \yii::t('modules/catalog/private', 'Price').' (USD)',
            'image' => \yii::t('modules/catalog/private', 'Image'),
            'ean' => 'EAN'
        ];
        return $labels + $this->getParamLabels();
    }

    public function getParamLabels()
    {
        $labels = [];
        foreach (\app\modules\catalog\components\ParamHelper::getOfferParamsForCategory($this) as $paramModel) {
            $labels["params[{$paramModel->id}]"] = $paramModel->name;
        }
        return $labels;
    }

    public function rules()
    {
        return [
            [['price_rub', 'price_usd'], 'double', 'on' => ['insert', 'update']],
            [['product_id'], 'required', 'on' => ['insert', 'update']],
            [['external_id'], 'filter', 'filter' => function ($value) {
                return (string)$value;
            }, 'on' => ['insert', 'update']],
            [['image'], 'app\components\CustomImageValidator', 'on' => ['insert', 'update']],
            [['ean'], 'validateEan', 'on' => ['insert', 'update']],
            [['quantity'], 'integer', 'min' => 0, 'on' => ['insert', 'update']],
            [['params'], 'safe', 'on' => ['update']],
            [['published', 'product_id', 'price', 'quantity', 'export_to_yml'], 'number', 'integerOnly' => true, 'on' => ['insert', 'update']],
            [['id', 'product_id', 'external_id', 'published', 'price', 'quantity', 'ean'], 'safe', 'on' => ['search']]
        ];
    }
    
    public function validateEan($attribute, $params)
    {
      if($this->ean && Offer::find()->where(['ean' => $this->ean])->count() > 0) {
        $this->addError($attribute, 'EAN должен быть уникальным');
      }
    }

    public function getFieldsDescription()
    {
        $fields = [
            'product_id' => ['RDbRelation', 'product'],
            'price_rub' => 'RDbText',
            'price_usd' => 'RDbText',
            'quantity' => 'RDbText',
            'ean' => 'RDbText',
            'external_id' => 'RDbText',
            'export_to_yml' => 'RDbBoolean',
            'published' => 'RDbBoolean',
            'image' => 'RDbFile'
        ];
        if ($this->getScenario() === 'update') {
            $fields = $fields + $this->getFieldsForParam();
        }
        return $fields;
    }

    public function getFieldsForParam()
    {
        $fields = [];
        foreach (\app\modules\catalog\components\ParamHelper::getOfferParamsForCategory($this) as $paramModel) {
            $value = OfferParamValue::find()->select(['value'])->where([
                    'offer_id' => $this->id,
                    'param_id' => $paramModel->id
                ])->scalar();
            $this->params[$paramModel->id] = $value;
            $fields["params[{$paramModel->id}]"] = [
                'RDbParam',
                'paramModel' => $paramModel,
                'label' => \yii::t('modules/catalog/private', 'Param') . ': ' . $this->getAttributeLabel("params[{$paramModel->id}]"),
                'htmlOptions' => ['value' => $value]
            ];
        }
        return $fields;
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public function getSelf()
    {
        return $this->hasOne(self::className(), ['id' => 'id']);
    }

    public function getReprName()
    {
        return $this->product ? $this->product->name . '-' . $this->self->price : $this->id;
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->saveParams();
        parent::afterSave($insert, $changedAttributes);
        $this->changeNosqlProduct($insert, $changedAttributes);
    }

    protected function changeNosqlProduct($insert, $changedAttributes)
    {
        if ($insert) {
            if ($this->visible) {
                \app\modules\catalog\components\NosqlProductHelper::addOffer($this);
            }
        } else {
            if ($this->visible) {
                \app\modules\catalog\components\NosqlProductHelper::updateOffer($this);
            }
        }
        if (!$this->visible) {
            \app\modules\catalog\components\NosqlProductHelper::delOffer($this);
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
            if($value === null) {
              continue;
            }
            $notUnsetParams[] = $id;
            $paramModel = Param::find()->where(['id' => $id])->limit(1)->one();
            if ($paramModel) {
                $notUnsetParams[$paramModel->id] = $paramModel->id;
                \app\modules\catalog\components\ParamHelper::saveOfferParam($this, $paramModel, $value);
            }
        }
        OfferParamValue::deleteAll(['AND', ['offer_id' => $this->id], ['NOT IN', 'param_id', $notUnsetParams]]);
    }
    
    public function getPrice()
    {
      $lang = \app\components\CurrencyHelper::getCurrency();
      if($lang === 'rub') {
        return $this->price_rub;
      }
      if($lang === 'usd') {
         return $this->price_usd;
      }
      return $this->price_rub;
    }

    public function getCurency()
    {
        $lang = \app\components\CurrencyHelper::getCurrency();
        if($lang === 'rub') {
            return '₽';
        }
        if($lang === 'usd') {
            return '$';
        }
        return '₽';
    }
    /**
     * 
     * @return string
     */
    public function getName()
    {
      return $this->product->getName();
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
    
    public function getAdminFields()
    {
      return ['id', 'product_id', 'external_id', 'published', 'price_rub', 'price_usd', 'quantity', 'ean'];
    }
    
    public function getParamValues()
    {
      return $this->hasMany(OfferParamValue::className(), ['offer_id' => 'id']);
    }
    
    public function afterDelete()
    {
      \app\modules\catalog\components\NosqlProductHelper::delOffer($this);
      parent::afterDelete();
      
    }
}