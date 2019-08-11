<?php

namespace app\modules\catalog\widgets;

class FilterChecboxList extends \yii\base\Widget
{
  public $info;
  
  public $model;
  
  public function run()
  {
    return $this->render('filter/checkboxList', [
      'info' => $this->info,
      'model' => $this->model
    ]);
  }
}