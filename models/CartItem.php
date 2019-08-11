<?php

namespace app\modules\catalog\models;
class CartItem
{
    /**
     * @var object $product
     */
    private $product;
    /**
     * @var object $offer
     */
    private $offer;
    /**
     * @var integer $quantity
     */
    private $quantity;
    /**
     * @var array $params Custom configuration params
     */
    private $params;

    public function __construct($product, $offer, $quantity, array $params)
    {
        $this->product = $product;
        $this->offer = $offer;
        $this->quantity = $quantity;
        $this->params = $params;
    }

    /**
     * Returns the id of the item
     * @return integer
     */
    public function getId()
    {
        if ($this->offer->{$this->params['offerFieldId']}) {
            return $this->offer->{$this->params['offerFieldId']};
        } else {
            return $this->product->{$this->params['productFieldId']};
        }
    }

    public function getProductId()
    {
        return $this->product->{$this->params['productFieldId']};
    }

    public function getOfferId()
    {
        return $this->offer->{$this->params['offerFieldId']};
    }

    /**
     * Returns the price of the item
     * @return integer|float
     */
    public function getPrice()
    {
        if($this->offer->{$this->params['offerFieldId']}){
            return $this->offer->{$this->params['offerFieldPrice']};
        } else {
            return $this->product->{$this->params['productFieldPrice']};
        }
    }

    /**
     * Returns the product, AR model
     * @return object
     */
    public function getProduct()
    {
        return $this->product;
    }
    /**
     * Returns the offer, AR model
     * @return object
     */
    public function getOffer()
    {
        return $this->offer;
    }
    /**
     * Returns the cost of the item
     * @return integer|float
     */
    public function getCost()
    {
        return ceil($this->getPrice() * $this->quantity);
    }

    /**
     * Returns the quantity of the item
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Sets the quantity of the item
     * @param integer $quantity
     * @return void
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
}

?>