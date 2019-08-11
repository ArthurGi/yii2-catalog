<?php

namespace app\modules\catalog\widgets;

use app\modules\catalog\models\Interior;

class InteriorWidget extends \yii\base\Widget
{
    public $code;

    public $product;

    public function run()
    {
        $product = $this->product;
        $interiors = $product->getInteriors()->all();
        //на последний момент было решено отказаться от сниппетов и выводит все интерерьеры, в которых есть товар 19062019
//        $model = Interior::find()->where(['code' => $this->code])->limit(1)->published()->one();
//        if (!$model) {
//            $code = htmlspecialchars($this->code);
//            return "<!--Interior {$code} not found-->";
//        }
        return $this->render('interior/interior', [
            'interiors' => $interiors
        ]);
    }

    protected function findCode()
    {

    }
}
