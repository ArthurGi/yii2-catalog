<?php
/**
 * Created by PhpStorm.
 * User: Master
 * Date: 21.06.2019
 * Time: 11:02
 */

namespace app\modules\catalog\controllers;

use app\modules\catalog\models\Product;
use app\modules\catalog\models\Offer;
use app\modules\catalog\models\Favorite;
use yii\web\Controller;


class CartController extends Controller
{

    public function actionIndex()
    {
        $cart = \Yii::$app->cart;
        $page = \app\components\SiteHelper::getCurrentPage();
        return $this->render('index', [
            'cart' => $cart,
            'page' => $page
        ]);
    }

    public function actionGetCartItemsAjax()
    {
        $cart = \Yii::$app->cart;
        return $this->renderPartial('cart-widget-ajax', [
            'cart' => $cart
        ]);
    }

    public function actionAdd()
    {
//        $mutex = new \yii\mutex\FileMutex();
//        $mutexName = \Yii::$app->session->id;
//        if ($mutex->acquire($mutexName, 10)) {
            $request = \Yii::$app->request;
            $cart = \Yii::$app->cart;
            if ($request->post('offer_id')) {
                $offer_id = \Yii::$app->request->post('offer_id');
                $quantity = \Yii::$app->request->post('quantity', 1);
                $offer = Offer::find()->where(['id' => $offer_id])->one();
                $product = $offer->product;
                if ($product && $offer) {
                    $cart->add($product, $offer, $quantity);
                    return true;
                }
            }
            return false;
//        }
//        $mutex->release($mutexName);
    }

    public function actionRemove()
    {
        $request = \Yii::$app->request;
        if ($request->isPost && $request->post('offer_id')) {
            $id = \Yii::$app->request->post('offer_id');
            return \Yii::$app->cart->remove($id);
        }
        return false;
    }

    public function actionClear()
    {
        if (\Yii::$app->request->isPost) {
            $cart = \Yii::$app->cart;
            return $cart->clear();
        }
        return false;
    }

    public function actionChange()
    {
        $mutex = new \yii\mutex\FileMutex();
        $mutexName = \Yii::$app->session->id;
        if ($mutex->acquire($mutexName, 10)) {
            $request = \Yii::$app->request;
            if ($request->isPost && $request->post('offer_id') && $request->post('quantity')) {
                $id = \Yii::$app->request->post('offer_id');
                $quantity = \Yii::$app->request->post('quantity');
                if ($quantity && $quantity > 0) {
                    \Yii::$app->cart->change($id, $quantity);
                }
                return true;
            }
        }
        $mutex->release($mutexName);
        return false;
    }

    /**
    по логике методу не важно какой запрос,
    товар есть - удаляет, нет - добавляет,
    возвращаем count для проверки необходимости
    подсветки сердечка в шапке*
    */
    public function actionAddFavorite()
    {
        $request = \Yii::$app->request;
        $user = \Yii::$app->user;
        if ($request->isPost && !$user->isGuest && $request->post('id')) {
            $user_id = $user->identity->getId();
            $id = $request->post('id');
            if ($favorite = Favorite::find()
                ->where(['user_id' => $user->getId(), 'product_id' => $id])
                ->one()
            ) {
                $favorite->delete();
                return Favorite::find()->where(['user_id' => $user->getId()])->count();
            } else {
                $favorite = new Favorite();
                $favorite->user_id = $user_id;
                $favorite->product_id = $id;
                $favorite->save();
                return Favorite::find()->where(['user_id' => $user->getId()])->count();
            }
        }
        return false;
    }

}