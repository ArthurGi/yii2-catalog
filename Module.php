<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog;

use yii\helpers\ArrayHelper;

use yii\helpers\Url;

/**
 * The article module for Alchemy CMS
 * 
 * @author AlkoDesign <info@alkodesign.ru>
 * @since 2.0
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\catalog\controllers';

    public function init()
    {
        parent::init();
        self::registerTranslations();
    }
    
    /**
     * Registers the translations
     */
    public static function registerTranslations()
    {
        \Yii::$app->i18n->translations['modules/catalog/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@app/modules/catalog/messages',
            'fileMap' => [
                'modules/catalog/app' => 'app.php',
                'modules/catalog/private' => 'private.php',
                'modules/catalog/stock' => 'stock.php',
            ],
        ];
    }
    
    /**
     * Returns list of handlers.
     * @see \app\models\Page::getHandlers
     * @return array
     */
    public function getHandlers()
    {
        return [
            'catalog/default/index' => \yii::t('modules/catalog/private', 'Catalog'),
            'catalog/special/index' => \yii::t('modules/catalog/private', 'Specials'),
            'catalog/cart/index' => \yii::t('modules/catalog/private', 'Cart'),
            'catalog/default/stock' => \yii::t('modules/catalog/private', 'Stocks'),
        ];
    }
    
    /**
     * Returns list of additional handlers data.
     * @see \app\models\Page::getHandlerData
     * @return array
     */
    public function getHandlerData()
    {
        $result = [
            'catalog/default/index' => [],
            'catalog/special/index' => [],
            'catalog/cart/index' => [],
            ];
        return $result;
    }
    
    /**
     * Returns list of additional url rules.
     * @see \app\models\Page::getHandlerRules
     * @return array
     */
    public function getHandlerRules()
    {
        $result = [
            'catalog/default/index' => [
                ['class' => 'app\\modules\\catalog\\components\\UrlRule'],
            ],
            'catalog/special/index' => [
                ['class' => 'app\\modules\\catalog\\components\\UrlRule'],
            ],
            'catalog/cart/index' => [
                ['class' => 'app\\modules\\catalog\\components\\UrlRule'],
            ],
        ];
        
        return $result;
    }
    
    /**
     * Returns tree of data for site map
     * @see \app\models\Page::getSitemapByHandler
     * @return array
     */
    public function getSitemapByHandler($page_id, $handler, $data = [])
    {
        $result = [];
        if($handler == 'catalog/default/index')
        {
            
        }
        
        return $result;
    }
    
    /**
     * Returns array of search results
     * @see \app\models\Page::getSearchResultsByHandler
     * @return array
     */
    public function getSearchResultsByHandler($form, $page_id, $handler, $data = [])
    {
        $result = [];
        if($handler == 'catalog/default/index')
        {
            
        }
        if($handler == 'catalog/default/view')
        {
            
        }
        
        return $result;
    }
    
    /**
     * Returns menu items for main menu for back end
     * @see \app\modules\privatepanel\Module::getMenus
     * @return array
     */
    public function privateMenuLinks()
    {
        return [
            ['label' => \yii::t('modules/catalog/private', 'Catalog'), 'url'=> ['/private/catalog-category'], 
                'items' => [
                  ['label' => \yii::t('modules/catalog/private', 'Categories'), 'url'=> ['/private/catalog-category'], 'visible' => \yii::$app->user->can('/private/catalog-category/admin')],
                  ['label' => \yii::t('modules/catalog/private', 'Products'), 'url'=> ['/private/catalog-products'], 'visible' => \yii::$app->user->can('/private/catalog-products/admin')],
                  ['label' => \yii::t('modules/catalog/private', 'Snippets'), 'url'=> ['/private/catalog-snippets'], 'visible' => \yii::$app->user->can('/private/catalog-snippets/admin')],
                  ['label' => \yii::t('modules/catalog/private', 'Special'), 'url'=> ['/private/catalog-special'], 'visible' => \yii::$app->user->can('/private/catalog-special/admin')],
                  ['label' => \yii::t('modules/catalog/private', 'Interiors'), 'url'=> ['/private/catalog-interior'], 'visible' => \yii::$app->user->can('/private/catalog-interior/admin')],
                  ['label' => \yii::t('modules/catalog/private', 'Params'), 'url'=> ['/private/catalog-params'], 'visible' => \yii::$app->user->can('/private/catalog-params/admin')],
                  ['label' => \yii::t('modules/catalog/private', 'Param values'), 'url'=> ['/private/catalog-param-values'], 'visible' => \yii::$app->user->can('/private/catalog-param-values/admin')],
            ]],
            ['label' => \yii::t('modules/catalog/private', 'Reviews'), 'url'=> ['/private/catalog-review'], 'visible' => \yii::$app->user->can('/private/catalog-review/admin')],
            ['label' => \yii::t('modules/catalog/private', 'One click purchases'), 'url'=> ['/private/one-click-purchase'], 'visible' => \yii::$app->user->can('/private/one-click-purchase/admin')]
          ];
    }
    
    /**
     * Returns additional controllers for back end
     * @see \app\modules\privatepanel\Module
     * @return array
     */
    public function privateControllerMap()
    {
        return [
            'catalog-category' => '\app\modules\catalog\controllers\privatepanel\CategoryController',
            'catalog-products' => '\app\modules\catalog\controllers\privatepanel\ProductController',
            'catalog-photo-gallery' => '\app\modules\catalog\controllers\privatepanel\PhotoGalleryController',
            'catalog-offers' => '\app\modules\catalog\controllers\privatepanel\OfferController',
            'catalog-snippets' => '\app\modules\catalog\controllers\privatepanel\SnippetController',
            'catalog-special' => '\app\modules\catalog\controllers\privatepanel\SpecialController',
            'catalog-interior' => '\app\modules\catalog\controllers\privatepanel\InteriorController',
            'catalog-params' => '\app\modules\catalog\controllers\privatepanel\ParamController',
            'catalog-param-values' => '\app\modules\catalog\controllers\privatepanel\ParamValueController',
            'catalog-review' => '\app\modules\catalog\controllers\privatepanel\ReviewController',
            'one-click-purchase' => '\app\modules\catalog\controllers\privatepanel\OneClickPurchaseController',
            'catalog-offer-price' => '\app\modules\catalog\controllers\privatepanel\OfferPriceController',
        ];
    }
    
}
