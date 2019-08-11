<?php

namespace app\modules\catalog\widgets;
use app\modules\catalog\models\Snippet;

class SnippetWidget extends \yii\base\Widget
{
  public $code;
  
  public function run()
  {
    $model = Snippet::find()->where(['code' => $this->code])->limit(1)->published()->one();
    if(!$model) {
      $code = htmlspecialchars($this->code);
      return "<!--Snippet {$code} not found-->";
    }
    return $this->render('snippet/snippet', [
        'products' => $model->products
    ]);
  }
  
  protected function findCode()
  {
    
  }
}
