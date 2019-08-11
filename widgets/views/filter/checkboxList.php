<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use app\modules\catalog\models\CatalogFilter;
/* @var $info array */
/* @var $model CatalogFilter */
?>
<div class="category__m-menu">
  <div class="mini-menu">
    <ul>
      <li class="sub">
        <div class="category_left_menu__title <?=count($info['value']) > 0 ? 'actived' : ''?>"><?=$info['name']?> </div>
        <div class="block_sub_child">
          <div class="control-group">
            <?php
            foreach($info['values'] as $value => $label) {
              $id = uniqid('f');
              ?>
              <label class="control control-checkbox" for="<?=$id;?>">
                <?=Html::checkbox(
                        Html::getInputName($model, 'params')."[{$info['alias']}][]",
                        in_array($value, $info['value']),
                        [
                            'id' => $id,
                            'value' => $value,
                            'class' => 'listen-input-change'
                        ]);?>
                <div class="control_indicator"></div>
                <?=$label?>
              </label>
            <?php
            }
            ?>
          </div>
        </div>
      </li>
    </ul>
  </div>
</div>