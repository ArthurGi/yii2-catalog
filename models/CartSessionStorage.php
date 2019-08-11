<?php

namespace app\modules\catalog\models;
use yii\db\Query;
use Yii;


class CartSessionStorage
{
    /**
     * @var string $string Table name
     */
    private $table = '{{%cart_items}}';
    /**
     * @var array $params Custom configuration params
     */
    private $params;
    /**
     * @var \yii\db\Connection $db
     */
    private $db;
    /**
     * @var integer $userId
     */
    private $userId;
    /**
     * @var SessionStorage $sessionStorage
     */
    private $sessionId;

    public function __construct(array $params)
    {
        $this->params = $params;
        $this->db = Yii::$app->db;
        $this->userId = Yii::$app->user->id;
        if(!Yii::$app->request->cookies->getValue('cart_session_id')){
            $cart_sess_hash = md5(microtime().random_int(0,26).random_int(0,26).random_int(0,26));//php >7
            $cookies = Yii::$app->response->cookies;
            $cookies->add(new \yii\web\Cookie([
                'name' => 'cart_session_id',
                'value' => $cart_sess_hash,
            ]));
            $this->sessionId = $cart_sess_hash;
        } else {
            $this->sessionId = Yii::$app->request->cookies->getValue('cart_session_id');
        }
    }

    /**
     * @return CartItem[]
     */
    public function load()
    {
        $this->moveItems();
        return $this->loadDb();
    }

    /**
     * @param CartItem[] $items
     * @return void
     */
    public function save(array $items)
    {
            $this->moveItems();
            $this->saveDb($items);
    }

    /**
     *  Moves all items from session storage to database storage
     * @return void
     */
    private function moveItems()
    {
        $items = $this->loadDb();
        $this->saveDb($items);
    }

    /**
     * Load all items from the database
     * @return CartItem[]
     */
    private function loadDb()
    {
        if($this->userId){
            $where = ['or', ['user_id' => $this->userId],['session_id' => $this->sessionId]];
        } else {
            $where = ['session_id' => $this->sessionId];
        }
        $rows = (new Query())
            ->select('*')
            ->from($this->table)
            ->where($where)
            ->all();
        $items = [];
        foreach ($rows as $row) {
            $product = $this->params['productClass']::find()
                ->where([
                    $this->params['productFieldId'] => $row['product_id'],
                ])
                ->limit(1)
                ->one();
            $offer = $this->params['offerClass']::find()
                ->where([
                    $this->params['offerFieldId'] => $row['offer_id'],
                ])
                ->limit(1)
                ->one();
            if ($product) {
                $items[$offer->{$this->params['offerFieldId']}] = new CartItem($product, $offer, $row['quantity'], $this->params);
            }
        }
        return $items;
    }

    /**
     * Save all items to the database
     * @param CartItem[] $items
     * @return void
     */
    private function saveDb(array $items)
    {

        if($this->userId){
            $this->db->createCommand()->delete($this->table, ['or',['user_id' => $this->userId],['session_id' => $this->sessionId]])->execute();
        } else {
            $this->db->createCommand()->delete($this->table, ['session_id' => $this->sessionId])->execute();
        }

        $fields = ['session_id', 'user_id', 'product_id', 'offer_id', 'quantity'];
        $this->db->createCommand()->batchInsert(
            $this->table,
            $fields,
            array_map(function (CartItem $item) {
                return [
                    'session_id' => $this->sessionId,
                    'user_id' => $this->userId,
                    'product_id' => $item->getProductId(),
                    'offer_id' => $item->getOfferId(),
                    'quantity' => $item->getQuantity(),
                ];
            }, $items)
        )->execute();
    }
}