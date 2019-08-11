<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;
use yii\helpers\ArrayHelper;
use app\modules\catalog\models\Category;
use app\components\SiteHelper;
use app\components\ImageHelper;

class CatalogHelper extends \yii\base\Component
{
  
  /**
   * массив со всеми категориями
   * @staticvar array $categories
   * @return array [id => [...]]
   */
  public static function getCategories()
  {
    static $categories;
    if(!is_array($categories)) {
      $categories = ArrayHelper::index(Category::find()
              ->orderBy(['ordering'=> SORT_ASC])
              ->published()->asArray()->all(), 'id');
    }
    return $categories;
  }

  /**
   * Массив дерева категорий
   * @param Integer $parent_id - id-родителя
   * @param Integer $level - уровень вложености
   */
  public static function getCategoriesTree()
  {
      $cat_page_id = \app\components\SiteHelper::getPageIdByHandler('catalog/default/index');
      $categories = self::getCategories();
      $levels = array();
      $tree = array();
      $cur = array();
      $model = new Category();
      foreach ($categories as $cat) {
          $model->setAttributes($cat, false);
          $cur = &$levels[$cat['id']];
          $cur['parent_id'] = $cat['parent_id'];
          $cur['id'] = $cat['id'];
          $cur['name'] = $model->getName();
          $cur['alias'] = $cat['alias'];
          $cur['url'] = \yii\helpers\Url::to(['/catalog/default/category', 'categoryAlias' => $cat['alias'], 'page_id' => $cat_page_id]);
          if($cat['parent_id'] == 0){
              $tree[$cat['id']] = &$cur;
          }
          else{
              $levels[$cat['parent_id']]['children'][$cat['id']] = &$cur;
          }
      }
      return $tree;
  }
  
  /**
   * 
   * @param Category $category
   * @return array
   */
  public static function getCategoryChilds($category) {
    $cat_page_id = \app\components\SiteHelper::getPageIdByHandler('catalog/default/index');
    $categories = self::getCategories();
    $levels = array();
    $tree = array();
    $cur = array();
    $model = new Category();
    $link = null;
    foreach ($categories as $cat) {
          $model->setAttributes($cat, false);
          $cur = &$levels[$cat['id']];
          $cur['parent_id'] = $cat['parent_id'];
          $cur['name'] = $model->getName();
          $cur['alias'] = $cat['alias'];
          $cur['url'] = \yii\helpers\Url::to(['/catalog/default/category', 'categoryAlias' => $cat['alias'], 'page_id' => $cat_page_id]);
          if($cat['parent_id'] == 0){
              $tree[$cat['id']] = &$cur;
          }
          else{
              $levels[$cat['parent_id']]['children'][$cat['id']] = &$cur;
          }
    }
    return [$levels[$category->id]];
  }
  
  /**
   * 
   * @param string|int $parentId
   * @return array
   */
  public static function getAllChildIds($parentId)
  {
    $childs = [];
    $categories = self::getCategories();
    $needChilds = [];
    while($parentId !== NULL) {
      foreach($categories as $id => $category) {
        if((int)$category['parent_id'] === (int)$parentId) {
          $childs[] = (int)$category['id'];
          $needChilds[] = (int)$category['id'];
          unset($category[$id]);
        }
      }
      $parentId = array_shift($needChilds);
    }
    return $childs;
  }
  
  /**
   * 
   * @staticvar array $cache
   * @param string|int $id
   * @return string|null
   */
  public static function getCategoryAliasById($id)
  {
    static $cache;
    if(!is_array($cache)) { 
      $cache = ArrayHelper::map(static::getCategories(), 'id', 'alias');
    }
    return array_key_exists($id, $cache) ? $cache[$id] : null;
  }
  
  /**
   * 
   * @staticvar array $models
   * @param int|string $id
   * @return Category|null
   */
  public static function getModelById($id)
  {
    static $models = array();
    if(!array_key_exists($id, $models)) {
       $models[$id] = Category::find()->where(['id' => $id])->one();
    }
    return $models[$id];
  }
  
  /**
   * 
   * @staticvar array $aliasToIdArr
   * @param string $alias
   * @return Category|null
   */
  public static function getModelByAlis($alias)
  {
    static $aliasToIdArr;
    if(!is_array($aliasToIdArr)) {
      foreach(static::getCategories() as $id => $row) {
        $aliasToIdArr[$row['alias']] = $id;
      }
    }
    return array_key_exists($alias, $aliasToIdArr) 
            ? static::getModelById($aliasToIdArr[$alias]) 
            : null;
  }
  
  /**
   * 
   * @param int|string $categoryId
   * @param \app\models\Page $page
   * @param boolean $last
   * @return type
   */
  public static function getCategoryBreadcrumbs($categoryId, $page, $last = true)
  {
    $breadcrumbs = SiteHelper::getCurrentPageBreadcrumbs(true, $page);
    $tempBreadcrumbs = [];
    $category = static::getModelById($categoryId);
    $tempBreadcrumbs[] = [
        'label' => $category->getCategoryName(),
        'url' => $last ? null : [
            '/catalog/default/index', 
            'page_id' => $page->id, 
            'categoryAlias' => $category->alias]
    ];
    $parentId = $category->parent_id;
    $limit = 10;
    while($parentId > 0 && $limit > 0) {
      $parentCategory = static::getModelById($parentId);
      $tempBreadcrumbs[] = [
        'label' => $parentCategory->getCategoryName(),
        'url' => [
            '/catalog/default/category', 
            'page_id' => $page->id, 
            'categoryAlias' => $parentCategory->alias]
      ];
      $parentId = $parentCategory->parent_id;
      $limit--;
    }
    return array_merge($breadcrumbs, array_reverse($tempBreadcrumbs));
  }
  
  /**
   * 
   * @param Category $category
   * @return string
   */
  public static function getCatalogPreview($category)
  {
    return ImageHelper::thumbnail($category->getImagePath(), 350, 350, ImageHelper::THUMBNAIL_RESIZE_AND_CROP);
  }
  
  /**
   * 
   * @param Category[] $categories
   * @return array
   */
  public static function getParentTree($categories)
  {
    $tree = [];
    $needParents = [];
    $allCategoriesArray = self::getCategories();
    foreach($categories as $category)
    {
      $tree[(int)$category->id] = [
          'model' => $category,
          'childs' => []
      ];
      if(array_key_exists((int)$category->parent_id, $tree)) {
        $tree[(int)$category->parent_id]['childs'][] = $tree[$category->id];
      } else {
        $tree[(int)$category->parent_id] = [
            'model' => null,
            'childs' => [$tree[$category->id]]
        ];
      }
      if($category->parent_id > 0) {
        $needParents[$category->parent_id] = $category->parent_id;
      }
    }
    $counter = 0;
    $id = reset($needParents);
    while($id) {
      if(!array_key_exists((int)$id, $allCategoriesArray)) {
        continue;
      }
      $model = new Category();
      $model->setAttributes($allCategoriesArray[$id], false);
      if(array_key_exists((int)$model->id, $tree)) {
        $tree[(int)$model->id]['model'] = $model;
      } else {
        $tree[(int)$model->id] = [
            'model' => $model,
            'childs' => []
        ];
      }
      if(array_key_exists((int)$model->parent_id, $tree)) {
        $tree[(int)$model->parent_id]['childs'][] = $tree[$model->id];
      } else {
        $tree[(int)$model->parent_id] = [
            'model' => null,
            'childs' => [$tree[$model->id]]
        ];
      }
      if($model->parent_id > 0) {
        $needParents[$model->parent_id] = $model->parent_id;
      }
      
      if($counter > 1000) {
        break;
      }
      $id = next($needParents);
    }
    return $tree;
  }
}