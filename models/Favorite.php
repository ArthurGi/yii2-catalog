<?php
/**
 * Created by PhpStorm.
 * User: Acer
 * Date: 10.07.2019
 * Time: 23:56
 */

namespace app\modules\catalog\models;

use \app\components\CommonActiveRecord as ActiveRecord;
use app\modules\catalog\Module;

class Favorite extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%user_favorite}}';
    }

    public function init()
    {
        parent::init();
        Module::registerTranslations();
    }

    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    public static function checkFavoriteForUser($product_id){
        $user = \Yii::$app->user;
        if($user->isGuest){
            return false;
        }
        static $favorites;
        $favorites = self::find()->select('product_id')->where(['user_id' => $user->identity->id])->asArray()->column();
        if(in_array($product_id, $favorites)){
            return true;
        }
        return false;
    }

    public static function issetUserFavorites(){
        $user = \Yii::$app->user;
        if($user->isGuest){
            return false;
        }
        $favorites = self::find()->select('id')->where(['user_id' => $user->identity->id])->asArray()->all();
        if($favorites && count($favorites)>0){
            return true;
        }
        return false;
    }

    public function rules()
    {
        return [
            [['user_id'], 'required', 'on' => ['insert', 'update']],
            [['offer_id', 'product_id', 'user_id'], 'number', 'integerOnly' => true, 'on' => ['insert', 'update']],
        ];
    }
}