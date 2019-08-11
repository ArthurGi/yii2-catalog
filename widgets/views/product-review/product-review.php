<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
foreach($reviews as $review)
{
?>
<div class="cards-tab__rewiev">
  <div class="cards-tab__name">
      <?=$review['name']?>
  </div>
  <div class="cards-tab__stars">
    <?php
    $counter = 0;
    while($counter < $review['rating']) {
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
  <div class="cards-tab__text">
      <?=$review['message']?>
  </div>
</div>
<?php
}
