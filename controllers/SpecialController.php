<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\controllers;

use app\modules\catalog\models\Special;
use yii\web\Controller;
use app\modules\catalog\models\Category;
use app\modules\catalog\components\CatalogHelper;
use app\modules\catalog\components\ProductFilterHelper;
use yii\data\Pagination;
use app\modules\catalog\components\ProductHelper;

class SpecialController extends Controller
{
  public function actionIndex()
  {
    $page = \app\components\SiteHelper::getCurrentPage();
    $specials = Special::find()->published()->all();
    return $this->render('index', [
        'page' => $page,
        'specials' => $specials
    ]);
  }

  
  public function actionView($specialAlias)
  {
    $special = Special::find()->where(['alias'=>$specialAlias])->published()->one();
    if(!$special) {
      throw new \yii\web\HttpException(404);
    }
    $page = \app\components\SiteHelper::getCurrentPage();
    $offers = $special->getOffers()->published()->all();
    $officeParamHelper = new \app\modules\catalog\components\OffersParamHelper($product, $offers, $category);
    return $this->render('view', [
        'special' => $special,
        'offers' => $offers,
    ]);
  }
}