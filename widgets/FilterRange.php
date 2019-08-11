<?php

namespace app\modules\catalog\widgets;

class FilterRange extends \yii\base\Widget
{
  public $info;
  
  public $model;
  
  public function run()
  {
    return $this->render('filter/range', [
       'info' => $this->info 
    ]);
  }
}