<?php
use yii\helpers\Html;
/* @var $model \app\modules\catalog\models\ReviewForm */
?>
<div class="col-lg-5 no-gutters-left">
  <div class="card-preview-s">
    <div class="card-preview-s__bg">

    </div>
    <div class="card-preview-s__wrap">
      <div class="card-preview-s__left">
          <div class="card-preview-s__ball">
              <b><?=$totalInfo['average']?></b><span>(<?=$totalInfo['total']?>)</span>
          </div>
          <div class="card-preview-s__stars">
            <?php
            $counter = 0;
            $average = (int)round($totalInfo['average']);
            while($counter < $average) {
              $counter++;
             ?>
              <img src="/web/img/star.svg" alt="alt">
              <?php
            }
            ?>
              <?php
            while($counter < 5) {
              $counter++;
             ?>
              <img src="/web/img/star_gray.svg" alt="alt">
              <?php
            }
            ?>
          </div>
      </div>
      <div class="card-preview-s__right">
          <ul>
            <?php
            $i = 5;
            while($i > 0) {
              ?>
              <li>
                <span><?=$i;?></span><img src="/web/img/star_gray.svg" alt="alt">
                <div class="review-progress">
                  <div style="width: <?=$totalInfo['total'] > 0 ? $totalInfo['countByRating'][$i] * 100.0 / $totalInfo['total'] : 0?>%"></div>
                </div>
              </li>
            <?php 
             $i--;      
            }
            ?>
          </ul>
      </div>
      <div class="card-preview-s__btn">
        <a href="" class="btn btn-outline-primary d-flex align-items-center justify-content-center">
            <?=\yii::t('modules/catalog/app', 'Write review')?>
        </a>
      </div>
    </div>
  </div>
</div>


<?php /*
<?=Html::beginForm('', 'POST', ['class' => 'review-form']);?>
<div>Оставить отзыв</div>
<?=Html::activeHiddenInput($model, 'productId')?>
<?=Html::activeDropDownList($model, 'rating', [1 => 1, 2 => 2, 3 => 3, 4 => 4, 5 => 5], ['required' => true])?>
<?=Html::activeTextarea($model, 'message', ['required' => true])?>
<?=Html::submitButton('Отправить');?>
<?=Html::endForm();?>

*/