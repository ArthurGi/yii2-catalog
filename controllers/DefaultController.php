<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\controllers;

use yii\web\Controller;
use app\modules\catalog\components\CatalogHelper;
use app\modules\catalog\components\ProductFilterHelper;
use yii\data\Pagination;
use app\modules\catalog\components\ProductHelper;
use app\modules\catalog\models\CatalogFilter;
use app\modules\catalog\components\FirstLevelCategoryHelper;
use app\components\SiteHelper;
use app\modules\catalog\components\FilterHelper;

class DefaultController extends Controller
{
  public function actionIndex()
  {
    $page = SiteHelper::getCurrentPage();
    $categoryTree = CatalogHelper::getCategoriesTree();
    $filter = new CatalogFilter();
    $filter->load(\Yii::$app->request->get());
    $query = FirstLevelCategoryHelper::buildProductQuery($filter);
    $countQuery = clone $query;
    $pagination = new Pagination([
        'totalCount' => $countQuery->count(),
        'pageSizeLimit' => [1, 192]
      ]);
    $pagination->setPageSize($filter->limit);
    $pagination->setPage((int)\Yii::$app->request->get('page') - 1);
    $query->limit($pagination->getLimit())->offset($pagination->getOffset());
    $minMax = FirstLevelCategoryHelper::getMinMaxPrice($filter);
    if($filter->priceFrom < $minMax['min'] || $filter->priceFrom > $minMax['max']) {
      $filter->priceFrom = (int)$minMax['min'];
    }
    if($filter->priceTo < $minMax['min'] 
            || $filter->priceTo > $minMax['max']
            || $filter->priceTo == 0) {
      $filter->priceTo = (int)$minMax['max'];
    }
    $params = [
        'products' => $query->all(),
        'page' => $page,
        'categoryTree' => $categoryTree,
        'filter' => $filter,
        'minMax' => $minMax,
        'pagination' => $pagination,
        'headerBanner' => '',
        'category' => null
    ];
    if(\Yii::$app->request->isAjax) {
      return $this->renderPartial('index', $params);
    } else {
      \Yii::$app->view->registerJsFile('/web/js/stock.js');
      return $this->render('index', $params);
    }
  }
  
  public function actionCategory($alias)
  {
    $category = CatalogHelper::getModelByAlis($alias);
    if(!$category || $category->published == 0) {
      throw new \yii\web\HttpException(404);
    }
    $page = SiteHelper::getCurrentPage();
    if(count($category->children) > 0) {
      $categoryTree = CatalogHelper::getCategoryChilds($category);
      $filter = new CatalogFilter();
      $filter->load(\Yii::$app->request->get());
      $filter->categories = CatalogHelper::getAllChildIds($category->id);
      $filter->categories[] = (int)$category->id;
      $query = FirstLevelCategoryHelper::buildProductQuery($filter);
      $countQuery = clone $query;
      $pagination = new Pagination([
          'totalCount' => $countQuery->count(),
          'pageSizeLimit' => [1, 192]
        ]);
      $pagination->setPageSize($filter->limit);
      $pagination->setPage((int)\Yii::$app->request->get('page') - 1);
      $query->limit($pagination->getLimit())->offset($pagination->getOffset());
      $minMax = FirstLevelCategoryHelper::getMinMaxPrice($filter);
      if($filter->priceFrom < $minMax['min'] || $filter->priceFrom > $minMax['max']) {
        $filter->priceFrom = (int)$minMax['min'];
      }
      if($filter->priceTo < $minMax['min'] 
              || $filter->priceTo > $minMax['max']
              || $filter->priceTo == 0) {
        $filter->priceTo = (int)$minMax['max'];
      }
      $params = [
        'products' => $query->all(),
        'page' => $page,
        'categoryTree' => $categoryTree,
        'filter' => $filter,
        'minMax' => $minMax,
        'pagination' => $pagination,
        'category' => $category,
        'headerBanner' => ''
      ];
      if(\Yii::$app->request->isAjax) {
        return $this->renderPartial('index', $params);
      } else {
         \Yii::$app->view->registerJsFile('/web/js/stock.js');
        $params['headerBanner'] = $this->renderPartial('header-banner', [
            'category' => $category
        ]);
        return $this->render('index', $params);
      }
      
    } else {
      
      $filter = \Yii::$app->catalogFilter->getCatalogFilterModel();
      $totalCountWithoutFilter = ProductFilterHelper::getTotalCount($category);
      $productQuery = ProductFilterHelper::buildProductQuery(false);
      $countProductQuery = ProductFilterHelper::buildProductQuery(true);
      $pagination = new Pagination([
          'totalCount' => $countProductQuery->count(),
      ]);
      $pagination->setPageSize($filter->limit);
      $pagination->setPage((int)\Yii::$app->request->get('page') - 1);
      $productQuery->limit($pagination->getLimit())->offset($pagination->getOffset());
      $products = $productQuery->all();
      $minMax = FilterHelper::getMinMaxPriceByCategory($category->id);
      if($filter->priceFrom < $minMax['min'] || $filter->priceFrom > $minMax['max']) {
        $filter->priceFrom = (int)$minMax['min'];
      }
      if($filter->priceTo < $minMax['min'] 
              || $filter->priceTo > $minMax['max']
              || $filter->priceTo == 0) {
        $filter->priceTo = (int)$minMax['max'];
      }
      $params = [
          'page' => $page,
          'category' => $category,
          'products' => $products,
          'pagination' => $pagination,
          'filter' => $filter,
          'minMax' => $minMax,
          'totalCountWithoutFilter' => $totalCountWithoutFilter
      ];
      if(\Yii::$app->request->isAjax) {
        return $this->renderPartial('goods', $params);
      } else {
        \Yii::$app->view->registerJsFile('/web/js/stock.js');
        return $this->render('goods', $params);
      }
    }
  }
  
  public function actionView($productAlias, $categoryAlias)
  {
    $category = CatalogHelper::getModelByAlis($categoryAlias);
    if(!$category || $category->published == 0) {
      throw new \yii\web\HttpException(404);
    }
    $product = ProductHelper::getModelByAlis($productAlias);
    if(!$product || $product->published == 0) {
      throw new \yii\web\HttpException(404);
    }
    \app\modules\catalog\components\WatchedProductHelper::add($product->id);
    $page = SiteHelper::getCurrentPage();
    $productItem = new \app\modules\catalog\components\ProductItem(['product' => $product]);
    if(!$productItem->getOffer()) {
      throw new \yii\web\HttpException(404);
    }
    if(/*\Yii::$app->request->isAjax 
            && */(\Yii::$app->request->get('param') || \Yii::$app->request->get('set-offer'))) {
        if(\Yii::$app->request->get('set-offer')) {
          $productItem->setOfferById(\Yii::$app->request->get('set-offer'));
        }
        if(\Yii::$app->request->get('param')) {
          $productItem->setOfferByParams(
                  \Yii::$app->request->get('param'), 
                  \Yii::$app->request->get('changed-property-id'));
        }
        return $this->renderPartial('_view-good', [
            'category' => $category,
            'productItem' => $productItem,
            'page' => $page,
        ]);
    }
    
    \Yii::$app->view->registerJsFile('/web/js/product.js');
    return $this->render('view', [
        'category' => $category,
        'productItem' => $productItem,
        'page' => $page,
    ]);
  }
  
  public function actionStock()
  {
    $page = SiteHelper::getCurrentPage();
    $filter = new \app\modules\catalog\models\StockFilter();
    $filter->load(\Yii::$app->request->getQueryParams());
    $query = \app\modules\catalog\components\StockHelper::buildProductQuery($filter);
    $totalCountWithoutFilter = \app\modules\catalog\components\StockHelper::buildProductQuery(new \app\modules\catalog\models\StockFilter())->count();
    $countQuery = clone $query;
    $pagination = new Pagination([
        'totalCount' => $countQuery->count(),
        'pageSizeLimit' => [1, 192]
      ]);
    $pagination->setPageSize($filter->limit);
    $pagination->setPage((int)\Yii::$app->request->get('page') - 1);
    $query->limit($pagination->getLimit())->offset($pagination->getOffset());
    $categoryModels = \app\modules\catalog\components\StockHelper::getCategoryModels();
    $minMax = \app\modules\catalog\components\StockHelper::getMinMaxPrice();
    if($filter->priceFrom < $minMax['min'] || $filter->priceFrom > $minMax['max']) {
      $filter->priceFrom = (int)$minMax['min'];
    }
    if($filter->priceTo < $minMax['min'] 
            || $filter->priceTo > $minMax['max']
            || $filter->priceTo == 0) {
      $filter->priceTo = (int)$minMax['max'];
    }
    $query->with(['offers', 'offers.paramValues']);
    if(\Yii::$app->request->isAjax && (int)\Yii::$app->request->get('page') > 0) { 
      return $this->renderPartial('stock-goods', [
                    'products' => $query->all()
                        ]);
    }
    $viewParams = [
        'products' => $query->all(),
        'categoryModels' => $categoryModels,
        'minMax' => $minMax,
        'filter' => $filter,
        'page' => $page,
        'totalCountWithoutFilter' => $totalCountWithoutFilter,
        'pagination' => $pagination
    ];
    if(\Yii::$app->request->isAjax) {
      return $this->renderPartial('stock', $viewParams);
    } else {
      \Yii::$app->view->registerJsFile('/web/js/stock.js');
      return $this->render('stock', $viewParams);
    }
  }

}