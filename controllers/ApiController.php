<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\controllers;

use yii\web\Controller;
use app\modules\catalog\models\Category;
use app\modules\catalog\components\CatalogHelper;
use app\modules\catalog\components\FilterHelper;
use app\modules\catalog\components\ProductFilterHelper;
use app\modules\catalog\models\Product;

class ApiController extends Controller
{
  public $enableCsrfValidation = false;
  
  public function  actionGetFilterLink()
  {
    FilterHelper::parseFilterFromFormData(\Yii::$app->request->post());
    $productQuery = \app\modules\catalog\components\ProductFilterHelper::buildProductQuery(true);
    $category = \Yii::$app->catalogFilter->getCategory();
    $count = $productQuery->count();
    $link = \yii\helpers\Url::to([
        '/catalog/default/category/', 
        'categoryAlias' => ($category ? $category->alias : ''),
        'page_id' => \app\components\SiteHelper::getPageIdByHandler('catalog/default/index')]);
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    return [
        'count' => $count,
        'link' => $link
    ];
  }
  
  public function actionGetSimilar()
  {
    $product = Product::find()
            ->where(['id' => \Yii::$app->request->get('id')])
            ->published()->one();
    if($product) {
      return \app\modules\catalog\widgets\SimilarProducts::widget(['product' => $product, 'isLoadInJS' => false]);
    }
    return \yii\helpers\Html::tag('DIV', '');
  }
  
  public function actionSaveReview()
  {
    return \app\modules\catalog\widgets\ReviewForm::widget();
  }
  
  public function actionOneClickPurchase()
  {
    return \app\modules\catalog\widgets\OneClickPurchase::widget();
  }
  
  public function actionMenuProducts()
  {
    if(!\Yii::$app->request->isAjax) {
      throw new \yii\web\HttpException(404);
    }
    $categoryId = (int)\Yii::$app->request->get('category');
    $allChilds = CatalogHelper::getAllChildIds($categoryId);
    $orCondition = ['OR', ['category_id' => $categoryId]];
    foreach($allChilds as $id) {
      $orCondition[] = ['category_id' => $id];
    }
    $products = Product::find()
            ->andWhere($orCondition)
            ->published()
            ->orderBy(['RAND()' => SORT_ASC])
            ->limit(3)
            ->all();
    return $this->renderPartial('menu-products', ['products' => $products]);
  }
  
  public function actionGetProductItem()
  {
    $product = Product::find()->where(['id' => \Yii::$app->request->get('product')])->one();
    if(!$product) {
      return '';
    }
    if(\Yii::$app->request->get('set-offer')) {
      $offer = $product->getOffers()->where(['id' => \Yii::$app->request->get('set-offer')])->one();
      if(!$offer) {
        return '';
      }
      return \app\modules\catalog\widgets\ProductListItem::widget(['product' => $product, 'offer' => $offer]);
    }
    if(\Yii::$app->request->get('param') 
            && \Yii::$app->request->get('changed-property-id')) {
      return \app\modules\catalog\widgets\ProductListItem::widget([
          'product' => $product, 
          'offerParams' => \Yii::$app->request->get('param'),
          'changedParam' => \Yii::$app->request->get('changed-property-id')
          ]);
    }
    return '';
  }
}

