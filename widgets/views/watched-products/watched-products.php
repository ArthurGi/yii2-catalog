<?php


?>
<section class="sect-new good-width m-catalog watched-products-body">
  <div class="col-12">
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-9 col-xl-9">
          <div class="h2 h2-card">
            <h2><?=\yii::t('modules/catalog/stock', 'You watched');?></h2>
          </div>
        </div>
        <div class="col-12 d-md-block col-md-3 col-xl-3 to_down_fixed">
          <div class="carousel-two__nav-wrap">
            <div class="carousel-two__arrows">
              <div class="my-car-prev">
                <img src="/web/img/arrow_right.svg" alt="alt">
              </div>
              <div class="sect-new__counter">
                <span class="counter__first"></span>
                <span class="counter__second"></span>
              </div>
              <div class="my-car-next">
                  <img src="/web/img/arrow_right.svg" alt="alt">
              </div>
            </div>
          </div>
        </div>
        <div class="col-12">
          <div class="carousel_block">
            <div class="bx-wrapper">
              <div class="bx-viewport">
                <div class="watched-products-carousel">
                  <?php
                    foreach($products as $product) {
                      echo \app\modules\catalog\widgets\ProductListItem::widget(['product' => $product]);
                    }
                    ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>