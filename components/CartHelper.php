<?php
/**
 * Created by PhpStorm.
 * User: Master
 * Date: 20.06.2019
 * Time: 18:18
 */

namespace app\modules\catalog\components;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;

class CartHelper extends BaseObject
{
    /**
     * @var string $storageClass
     */
    public $storageClass = 'app\modules\catalog\models\CartSessionStorage';
    /**
     * @var string $calculatorClass
     */
    public $calculatorClass = 'Calculator';
    /**
     * @var array $params Custom configuration params
     */
    public $params = [];
    /**
     * @var array $defaultParams
     */
    private $defaultParams = [
        'key' => 'cart',
        'expire' => 604800,
        'productClass' => '\app\model\Product',
        'offerClass' => '\app\model\Offer',
        'productFieldId' => 'id',
        'offerFieldId' => 'id',
        'productFieldPrice' => 'price',
        'offerFieldPrice' => 'price',
    ];
    /**
     * @var CartItem[]
     */
    private $items;

    private $storage;

    private $calculator;

    public function init()
    {
        parent::init();

        $this->params = array_merge($this->defaultParams, $this->params);
        if (!class_exists($this->params['productClass'])) {
            throw new InvalidConfigException('productClass `' . $this->params['productClass'] . '` not found');
        }
        if (!class_exists($this->params['offerClass'])) {
            throw new InvalidConfigException('productClass `' . $this->params['productClass'] . '` not found');
        }
        if (!class_exists($this->storageClass)) {
            throw new InvalidConfigException('storageClass `' . $this->storageClass . '` not found');
        }
        $this->storage = new \app\modules\catalog\models\CartSessionStorage($this->params);
        $this->calculator = new Calculator();
    }
    /**
     * Add an item to the cart
     * @param object $product
     * @param integer $quantity
     * @return void
     */
    public function add($product, $offer, $quantity)
    {
        $this->loadItems();
        if (isset($this->items[$offer->{$this->params['offerFieldId']}])) {
            $this->plus($offer->{$this->params['offerFieldId']}, $quantity);
        } else {
            $this->items[$offer->{$this->params['offerFieldId']}] = new \app\modules\catalog\models\CartItem($product, $offer, $quantity, $this->params);
            ksort($this->items, SORT_NUMERIC);
            $this->saveItems();
        }
    }
    /**
     * Adding item quantity in the cart
     * @param integer $id
     * @param integer $quantity
     * @return void
     */
    public function plus($id, $quantity)
    {
        $this->loadItems();
        if (isset($this->items[$id])) {
            $this->items[$id]->setQuantity($quantity + $this->items[$id]->getQuantity());
        }
        $this->saveItems();
    }
    /**
     * Change item quantity in the cart
     * @param integer $id
     * @param integer $quantity
     * @return void
     */
    public function change($id, $quantity)
    {
        $this->loadItems();
        if (isset($this->items[$id])) {
            $this->items[$id]->setQuantity($quantity);
        }
        $this->saveItems();
    }
    /**
     * Removes an items from the cart
     * @param integer $id
     * @return void
     */
    public function remove($id)
    {
        $this->loadItems();
        if (array_key_exists($id, $this->items)) {
            unset($this->items[$id]);
        }
        $this->saveItems();
    }
    /**
     * Removes all items from the cart
     * @return void
     */
    public function clear()
    {
        $this->items = [];
        $this->saveItems();
    }
    /**
     * Returns all items from the cart
     * @return CartItem[]
     */
    public function getItems()
    {
        $this->loadItems();
        return $this->items;
    }
    /**
     * Returns an item from the cart
     * @param integer $id
     * @return CartItem
     */
    public function getItem($id)
    {
        $this->loadItems();
        return isset($this->items[$id]) ? $this->items[$id] : null;
    }
    /**
     * Returns ids array all items from the cart
     * @return array
     */
    public function getItemIds()
    {
        $this->loadItems();
        $items = [];
        foreach ($this->items as $item) {
            $items[] = $item->getId();
        }
        return $items;
    }

    public function getTotalCost()
    {
        $this->loadItems();
        return $this->calculator->getCost($this->items);
    }

    public function getTotalCount()
    {
        $this->loadItems();
        return $this->calculator->getCount($this->items);
    }

    private function loadItems()
    {
        if ($this->items === null) {
            $this->items = $this->storage->load();
        }
    }

    private function saveItems()
    {
        $this->storage->save($this->items);
    }
}

class Calculator
{

    public function getCost(array $items)
    {
        $cost = 0;
        foreach ($items as $item) {
            $cost += $item->getCost();
        }
        return $cost;
    }


    public function getCount(array $items)
    {
        $count = 0;
        foreach ($items as $item) {
            $count += $item->getQuantity();
        }
        return $count;
    }
}