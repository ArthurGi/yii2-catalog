<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\components;

use yii\web\UrlRuleInterface;
use yii\base\BaseObject;
use app\modules\catalog\models;

/**
 * A class of article url rule
 *
 * @author AlkoDesign <info@alkodesign.ru>
 * @since 2.0
 */
class UrlRule extends BaseObject implements UrlRuleInterface
{
  public function createUrl($manager, $route, $params)
  {
//      die;
    $urlArr = [];
    if(isset($params['pageObject'])) {
      $urlArr[] = $params['pageObject']->alias;
      if(isset($params['section_id']) && isset($params['pageObject']->handler_data['section_id']) 
              && ($params['section_id'] == $params['pageObject']->handler_data['section_id'])) { 
          unset($params['section_id']);
      }
      unset($params['pageObject']);
    }
      if($route == 'catalog/special/index') {

          if (!empty($params) && ($query = http_build_query($params)) !== '') {
              $url = implode('/', $urlArr).'?' . $query;
          } else {
              $url = implode('/', $urlArr).'/';
          }
          return $url;
      }
      if($route == 'catalog/default/stock') {
        $urlArr[] = 'stock';
        if (!empty($params) && ($query = http_build_query($params)) !== '') {
          $url = implode('/', $urlArr).'?' . $query;
        } else {
          $url = implode('/', $urlArr).'/';
        }
        return $url;
      }

      if($route == 'catalog/special/view') {
          $urlArr[] = $params['specialAlias'];
          unset($params['specialAlias']);
          if (!empty($params) && ($query = http_build_query($params)) !== '') {
              $url = implode('/', $urlArr).'?' . $query;
          } else {
              $url = implode('/', $urlArr).'/';
          }
          return $url;
      }
    if($route == 'catalog/default/index') {
      if (!empty($params) && ($query = http_build_query($params)) !== '') {
        $url = implode('/', $urlArr).'?' . $query;
      } else {
        $url = implode('/', $urlArr).'/';
      }
      return $url;
    }
    if($route == 'catalog/default/category') {
      $urlArr[] = $params['categoryAlias'];
      unset($params['categoryAlias']);
      if (!empty($params) && ($query = http_build_query($params)) !== '') {
        $url = implode('/', $urlArr).'?' . $query;
      } else {
        $url = implode('/', $urlArr).'/';
      }
      return $url;
    }
    
    if($route == 'catalog/default/filter') {
      $urlArr[] = $params['categoryAlias'];
      $filterRequest = static::getFilterRequest();
      if($filterRequest) {
          $urlArr[] = $filterRequest;
      }
      unset($params['categoryAlias']);
      if (!empty($params) && ($query = http_build_query($params)) !== '') {
        $url = implode('/', $urlArr).'?' . $query;
      } else {
        $url = implode('/', $urlArr).'/';
      }
      return $url;
    }
    
    if($route == 'catalog/default/view') {
      $urlArr[] = $params['categoryAlias'];
      $urlArr[] = $params['productAlias'];
      unset($params['categoryAlias']);
      unset($params['productAlias']);
      if (!empty($params) && ($query = http_build_query($params)) !== '') {
        $url = implode('/', $urlArr).'?' . $query;
      } else {
        $url = implode('/', $urlArr).'/';
      }
      return $url;
    }
    return false;
  }
  
  public function parseRequest($manager, $request)
  {
     $pathInfo = trim($request->getPathInfo(), '/');
     $params = $request->getQueryParams();
     if($pathInfo == '' && $params['page_id']) {
         $page_id = $params['page_id'];
         $query = new \yii\db\Query();
         $pageObject = $query->from(\app\models\Page::getTableSchema()->name)->where('`id`=:id AND published=1 AND visible=1', [':id'=> $page_id])->one();
         if($pageObject && $pageObject['handler']){
             return [$pageObject['handler'], []];
         }
     }
     $pathInfoArr = explode('/', $pathInfo);
     if(count($pathInfoArr) === 1) {
         if( $page_id = $params['page_id']){
             $query = new \yii\db\Query();
             $pageObject = $query->from(\app\models\Page::getTableSchema()->name)->where('`id`=:id AND published=1 AND visible=1', [':id'=> $page_id])->one();
             if($pageObject['handler'] == 'catalog/special/index'){
                 return ['catalog/special/view', ['specialAlias' => $pathInfoArr[0]]];
             }
             if($pageObject['handler'] == 'catalog/cart/index'){
                 //var_dump($pathInfoArr,$params['page_id']);die;
                 $action = 'catalog/cart/'.$pathInfoArr[0];
                 //var_dump($action);die;
                 return [$action, []];
             }
         }
         if($pathInfoArr[0] === 'stock') {
           return ['catalog/default/stock', []];
         }
       \Yii::$app->catalogFilter->setCategory(CatalogHelper::getModelByAlis($pathInfoArr[0]));
       static::loadFilterFromRequest();
       return ['catalog/default/category', ['alias' => $pathInfoArr[0]]];
     } elseif(count($pathInfoArr) > 1 && $pathInfoArr[1] === 'f') {
       $copyPathInfoArr = $pathInfoArr;
       unset($copyPathInfoArr[0]);
       unset($copyPathInfoArr[1]);
       static::parseFilter(implode('/', $copyPathInfoArr));
       static::loadFilterFromRequest();
       \Yii::$app->catalogFilter->setCategory(CatalogHelper::getModelByAlis($pathInfoArr[0]));
       return ['catalog/default/category', ['alias' => $pathInfoArr[0]]];
     } elseif(count($pathInfoArr) > 1 && $pathInfoArr[0] === 'api') {
       return ['catalog/'.$pathInfoArr[0].'/'.$pathInfoArr[1], []];
     } elseif(count($pathInfoArr) > 1 && $pathInfoArr[0] === 'cart') {
         return ['catalog/'.$pathInfoArr[0].'/'.$pathInfoArr[1], []];
     }
     if(count($pathInfoArr) === 2) {
       return ['catalog/default/view', [
           'productAlias' => $pathInfoArr[1], 
           'categoryAlias' => $pathInfoArr[0]
               ]];
     }
     return false;
  }
  
  /**
   * 
   * @param string $filterPathInfo
   */
  public static function parseFilter($filterPathInfo)
  {
    $filterItems = explode('/', $filterPathInfo);
    foreach($filterItems as $filterItem) {
      $paramAndValues = explode('--', $filterItem);
      if(count($paramAndValues) !== 2) {
        throw new \yii\web\HttpException(404);
      }
      if(strpos($paramAndValues[1], 'from-') !== false || strpos($paramAndValues[1], 'to-')) {
        $from = null;
        $to = null;
        if(preg_match_all('/from\-\d+/iu', $paramAndValues[1], $matches)){
          $from = (int)str_replace('from-', '', $matches[0][0]);
        }
        if(preg_match_all('/to\-\d+/iu', $paramAndValues[1], $matches)){
          $to = (int)str_replace('to-', '', $matches[0][0]);
        }
        \Yii::$app->catalogFilter->setRangeItem($paramAndValues[0], $from, $to);
      } else {
        $values = explode('-or-', $paramAndValues[1]);
        foreach($values as $value) {
          \Yii::$app->catalogFilter->addSelectedItem($paramAndValues[0], $value);
        }
      }
    }
    
  }
  
  public static function loadFilterFromRequest()
  {
    $filter = new \app\modules\catalog\models\CatalogFilter();
    if(is_array(\yii\helpers\ArrayHelper::getValue(\Yii::$app->request->get(), $filter->formName()))) {
        $filter->load(\Yii::$app->request->get());
        \Yii::$app->catalogFilter->loadFromCatalogFilterModel($filter);
    }
  }
  
  /**
   * 
   * @return string
   */
  public static function getFilterRequest()
  {
    $urlArr = ['f'];
    $filterArr = \Yii::$app->catalogFilter->getFilter();
    if(count($filterArr) === 0)
    {
      return '';
    }
    ksort($filterArr, SORT_NATURAL|SORT_FLAG_CASE);
    foreach($filterArr as $key => $values) {
      $type = ParamHelper::getFilterTypeIdByAlis($key);
      if(is_array($values) 
              && ($type == FilterHelper::TYPE_RANGE || in_array($key, ['price']))
              && (isset($values['from']) || isset($values['to']))) {
        $str = isset($values['from']) ? '-from-'.$values['from'] : '';
        $str .= isset($values['to']) ? '-to-'.$values['to'] : '';
        $urlArr[] = $key.'-'.$str;
      } else {
        if(is_array($values)) {
          asort($values, SORT_NATURAL|SORT_FLAG_CASE);
        } else {
          $values = [$values];
        }
        $urlArr[] = $key.'--'.implode('-or-', $values);
      }
    }
    return implode('/', $urlArr);
  }
}