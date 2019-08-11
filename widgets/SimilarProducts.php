<?php
namespace app\modules\catalog\widgets;
use app\modules\catalog\models\Product;

class SimilarProducts extends \yii\base\Widget
{
  /**
   *
   * @var Product 
   */
  public $product;
  
  public $isLoadInJS = false;
  
  public function run()
  {
    if($this->isLoadInJS) {
      return $this->render('similar-products/similar-products-js', [
          'productId' => $this->product->id
      ]);
    }
    $similarProducts = Product::find()
            ->published()
            ->innerJoin('{{%catalog_similar_products}}', '{{%catalog_similar_products}}.product_id2='.Product::tableName().'.id')
            ->andWhere('{{%catalog_similar_products}}.product_id1=:id', [':id' => $this->product->id])
            ->all();
    if(count($similarProducts) === 0) {
       return '';
    }
    
    return $this->render('similar-products/similar-products', [
        'similarProducts' => $similarProducts
    ]);
  }
}