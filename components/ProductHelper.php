<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;
use app\modules\catalog\models\Product;
use app\components\ImageHelper;

class ProductHelper extends \yii\base\Component
{
  /**
   * 
   * @param Product $product
   * @return string
   */
  public static function getImgPreview($product)
  {
    return ImageHelper::thumbnail($product->getImagePath(), 350, 350, ImageHelper::THUMBNAIL_RESIZE_AND_CROP);
  }
  
  /**
   * 
   * @param Product $product
   * @return string
   */
  public static function getImage($product)
  {
    return ImageHelper::thumbnail($product->getImagePath(), 350, 350, ImageHelper::THUMBNAIL_RESIZE);
  }
  
  /**
   * 
   * @staticvar array $models
   * @param string $alias
   * @return Product
   */
  public static function getModelByAlis($alias)
  {
    static $models = array();
    if(!array_key_exists($alias, $models)) {
      $models[$alias] = Product::find()->where(['alias' => $alias])->one();
    }
    return $models[$alias];
  }
  
  /**
   * 
   * @staticvar array $models
   * @param string $id
   * @return Product
   */
  public static function getModelById($id)
  {
    static $models = array();
    if(!array_key_exists($id, $models)) {
      $models[$id] = Product::find()->where(['id' => $id])->one();
    }
    return $models[$id];
  }
  
  /**
   * 
   * @param Category $category
   * @param Product $product
   * @param \app\models\Page $page
   * @param boolean $last
   * @return type
   */
  public static function getProductBreadcrumbs($category, $product, $page, $last = true)
  {
    $categoryBreadcrumbs = CatalogHelper::getCategoryBreadcrumbs($category->id, $page, false);
    $categoryBreadcrumbs[] = 
        [
        'label' => $product->getName(),
        'url' => $last 
            ? null
            :  [
            '/catalog/default/category', 
            'page_id' => $page->id, 
            'categoryAlias' => $category->alias,
            'productAlias' => $product->alias
            ]
    ];
    return $categoryBreadcrumbs;
  }
  
  /**
   * 
   * @param Product $product
   * @param boolean $scheme
   * @return string
   */
  public static function getDetailPageUrl($product, $scheme = false)
  {
    return \yii\helpers\Url::to([
        '/catalog/default/view/', 
        'categoryAlias' => CatalogHelper::getCategoryAliasById($product->category_id),
        'productAlias' => $product->alias,
        'page_id' => \app\components\SiteHelper::getPageIdByHandler('catalog/default/index')
        ], $scheme);
  }
  
  /**
   * 
   * @param type $product
   * @return type
   */
  public static function getBrandName($product)
  {
    $param = ParamHelper::getParamByAlias('brand');
    if(!$param) {
      return null;
    }
    return ParamHelper::getParamValueNameForProduct($param, $product->id);
  }
}