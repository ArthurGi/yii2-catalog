<?php
/**
 * @link http://alkodesign.ru
 */
namespace app\modules\catalog\models;

class OneClickPurchaseForm extends \yii\base\Model
{  
  public $phone;
  public $name;
  public $product_id;
  public $offer_id;
  public $comment;
  public $formId;
  
  public function attributeLabels()
  {
    return [
        'phone' => \yii::t('modules/catalog/app', 'Phone'),
        'name' => \yii::t('modules/catalog/app', 'Your name'),
        'comment' => \yii::t('modules/catalog/app', 'Comment')
    ];
  }
  
  public function rules()
  {
    return [
        [['phone', 'name'], 'required'],
        [['offer_id', 'product_id', 'comment', 'formId'], 'safe']
    ];
  }
  
  /**
   * 
   * @param boolean $runValidation
   * @return boolean
   */
  public function save($runValidation = true)
  {
    if($runValidation) {
      if(!$this->validate()) {
        return false;
      }
    }
    $model = new OneClickPurchase();
    $model->product_id = (int)$this->product_id;
    $model->offer_id = (int)$this->offer_id;
    $model->name = strip_tags($this->name);
    $model->phone = strip_tags($this->phone);
    $model->comment = strip_tags($model->comment);
    $model->add_date_time = date('Y-m-d H:i:s');
    $model->status = OneClickPurchase::STATUS_NEW;
    $this->senMail($model);
    return $model->save(false);
  }
  
  /**
   * 
   * @param OneClickPurchase $model
   */
  public function senMail($model)
  {
    $mailParams = [
        'NAME' => $model->name,
        'PHONE' => $model->phone,
        'COMMENT' => $model->comment,
        'PRODUCT' => $model->product ? $model->product->getName() : ($model->offer ? $model->offer->getNameWithProduct() : 'Не указан'),
    ];
    $mailer = new \app\components\MailHelper;
    $mailer->to = \app\components\ConstantHelper::getValue('administrator-email', 'q11211121@yandex.ru');
    $mailer->subject = \yii::t('modules/catalog/app', 'One-click buying');
    $mailer->mailTemplate = 'one-click-buying';
    $mailer->params = $mailParams;
    $mailer->Send();
  }
}