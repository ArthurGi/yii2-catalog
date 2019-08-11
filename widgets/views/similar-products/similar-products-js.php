<?php
$divId = uniqid('similar');
$url = yii\helpers\Url::to(['/catalog/api/get-similar/', 'rand' => time(), 'id' => $productId], true);
?>
<div id="<?=$divId?>"></div>
<script type="text/javascript">
window.addEventListener('load', function(){
  var url = <?=\yii\helpers\Json::encode($url);?>;
  var elemId = <?=\yii\helpers\Json::encode($divId);?>;
  $.get(url, {}, function(data){
    $("#" + elemId).replaceWith(data);
    initSlider();
  });
  
  
  function initSlider()
  {
    $("#" + elemId).find('.product-item').each(function(i, item) {
      var product = new MalinaHomeProduct();
      product.init($(item), function(){
        $('.similar-products-block .similar-products-carousel').slick('setOption', {}, true);
      });
    });
    
    var elem = $('.similar-products-block');
    var slider2 = elem.find('.similar-products-carousel');
    var pages = elem.find('span.counter__first');
    var pages2 = elem.find('span.counter__second');
    
    // Элемент слайдера
    slider2.on('init reInit afterChange', function(event, slick, currentSlide, nextSlide){
       console.log('similar-slider');
       var i = (currentSlide ? currentSlide : 0) + 1;
       i = Math.ceil(i/4);
       if (i<10) {
        i = '0'+i;
       }

        var testZero;
        var slideCount =  Math.ceil(slick.slideCount / 4);
        if(slideCount < 10){
          testZero = '0'+ slideCount;
        } else{
          testZero = slideCount;
        }

       

        pages.text(i);
        pages2.text('|' + testZero);
    });
    
    slider2.slick({
      slidesToShow: 4,
      arrows: false,
      slidesToScroll: 4,
      speed: 1500,
      responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2,
            rows: 2,
            arrows: false,
            slidesToScroll: 2,
            speed: 1500,
          }
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 1,
            rows: 1,
            arrows: false,
            slidesToScroll: 1,
            speed: 1500,
          }
        },
      ]
    });

    elem.find('.my-car-next').click(function(){
        slider2.slick('slickNext');

      });

    elem.find('.my-car-prev').click(function(){
        slider2.slick('slickPrev');
    });
  }
  
});
</script>

