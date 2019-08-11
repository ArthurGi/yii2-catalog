<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

class ReviewForm extends \yii\base\Model
{
  /**
   *
   * @var int
   */
  public $rating;
  
  /**
   *
   * @var string 
   */
  public $message;
  /**
   *
   * @var int 
   */
  public $productId;
  
  public function rules()
  {
    return [
        [['rating'], 'integer', 'min' => 1, 'max' => 5],
        [['productId'], 'integer', 'min' => 0],
        [['message'], 'required'],
        [['message'], 'validateMessage', 'skipOnEmpty' => false]
    ];
  }
  
  public function attributeLabels()
  {
    return [
        'rating' => \yii::t('modules/catalog/app', 'Rating'),
        'message' => \yii::t('modules/catalog/app', 'Review')
    ];
  }
  
  public function validateMessage($attribute, $params)
  {
    if(is_string($this->{$attribute})) {
      $this->{$attribute} = strip_tags($this->{$attribute});
    }
  }
  
  /**
   * 
   * @return boolean
   */
  public function save()
  {
    $review = new Review();
    $review->user_id = (int)\Yii::$app->user->getId();
    $review->message = $this->message;
    $review->rating = $this->rating;
    $review->product_id = (int)$this->productId;
    $review->add_datetime = date('Y-m-d H:i:s');
    return $review->save();
  }
}