<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;

class CatalogFilter extends \yii\base\Component
{
  /**
   *
   * @var \app\modules\catalog\models\Category 
   */
  protected $category;
  
  protected $filter = [];
  
  protected $limit = 12;
  
  protected $ordering;
  
  /**
   * 
   * @return \app\modules\catalog\models\Category
   */
  public function getCategory()
  {
    return $this->category;
  }
  
  public function getOrdering()
  {
    return $this->ordering;
  }
  
  /**
   * 
   * @param \app\modules\catalog\models\Category  $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  
  /**
   * 
   * @param string $alias
   * @param string|array $value
   */
  public function addSelectedItem($alias, $value)
  {
    if(!array_key_exists($alias, $this->filter)) {
      $this->filter[$alias] = [];
    }
    if(!in_array($value, $this->filter[$alias])) {
      $this->filter[$alias][] = $value;
    }
  }
  
  /**
   * 
   * @param string $alias
   * @param int|null $from
   * @param int|null $to
   */
  public function setRangeItem($alias, $from = null, $to = null)
  {
    if($from === null && $to === null) {
      return;
    }
    $this->filter[$alias] = [];;
    $paramId = ParamHelper::getParamIdByAlias($alias);
    if($paramId && $this->category) {
      $minMaxArr = FilterHelper::getMinMaxForParamByCategory($paramId, $this->category->id);
      if($from !== null && $minMaxArr[0] != $from) {
        $this->filter[$alias]['from'] = $from;
      }
      if($to !== null && $minMaxArr[1] != $to) {
        $this->filter[$alias]['to'] = $to;
      }
    } else {
      if($from !== null) {
        $this->filter[$alias]['from'] = $from;
      }
      if($to !== null) {
        $this->filter[$alias]['to'] = $to;
      }
    }
  }
  
  /**
   * 
   * @return \app\modules\catalog\models\CatalogFilter
   */
  public function getCatalogFilterModel()
  {
    $model = new \app\modules\catalog\models\CatalogFilter();
    $model->params = $this->filter;
    $model->priceFrom = \yii\helpers\ArrayHelper::getValue($this->filter, 'price.from');
    $model->priceTo = \yii\helpers\ArrayHelper::getValue($this->filter, 'price.to');
    $model->isStock = \yii\helpers\ArrayHelper::getValue($this->filter, 'stock') === '1';
    $model->categories = $this->category ? [$this->category->id] : [];
    $model->limit = $this->limit > 0 ? $this->limit : 12;
    $model->ordering = $this->ordering;
    return $model;
  }
  
  /**
   * 
   * @param \app\modules\catalog\models\CatalogFilter $model
   */
  public function loadFromCatalogFilterModel($model)
  {
    if(is_array($model->params)) {
      $this->filter = $model->params;
    } else {
      $this->filter = [];
    }
    if($model->priceFrom > 0 && isset($this->filter['price']['from'])) {
      $this->filter['price']['from'] = $model->priceFrom;
    }
    if($model->priceTo > 0 && isset($this->filter['price']['to'])) {
      $this->filter['price']['to'] = $model->priceTo;
    }
    $this->limit = $model->limit;
    $this->ordering = $model->ordering;
  }
  
  /**
   * 
   * @param string $alias
   * @param string|array $value
   */
  public function unSelectValue($alias, $value)
  {
    if(!array_key_exists($alias, $this->filter)) {
      return;
    }
    foreach($this->filter[$alias] as $id => $selectedValue) {
      if($value === $selectedValue) {
        unset($this->filter[$alias][$id]);
      }
    }
    if(count($this->filter[$alias]) == 0)
    {
      unset($this->filter[$alias]);
    }
  }
  
  /**
   * 
   * @param string $alias
   */
  public function unSelectParam($alias)
  {
    unset($this->filter[$alias]);
  }
  
  /**
   * 
   * @return array
   */
  public function getFilter()
  {
    return $this->filter;
  }
  
  /**
   * 
   * @param string $alias
   * @return null|array
   */
  public function getSelectValuesForParam($alias)
  {
    return array_key_exists($alias, $this->filter) ? $this->filter[$alias] : [];
  }
  
  /**
   * 
   * @param string $alias
   * @param string|array $value
   * @return boolean
   */
  public function isSelected($alias, $value)
  {
    return array_key_exists($alias, $this->filter) && in_array($value, $this->filter[$alias]);
  }
}