<?php

require_once 'config.php';

class Cart {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function addItem($userId, $productId) {
        $stmt = $this->pdo->prepare(
            "INSERT INTO cart (user_id, product_id, quantity) 
             VALUES (:user_id, :product_id, 1)
             ON DUPLICATE KEY UPDATE quantity = quantity + 1"
        );
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
    }

    public function getCartItems($userId) {
        $stmt = $this->pdo->prepare(
            "SELECT c.product_id, p.name, p.price, c.quantity
             FROM cart c
             JOIN products p ON c.product_id = p.id
             WHERE c.user_id = :user_id"
        );
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function clearCart($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
    }

    public function removeItem($userId, $productId) {
        $stmt = $this->pdo->prepare(
            "DELETE FROM cart WHERE user_id = :user_id AND product_id = :product_id"
        );
        $stmt->execute(['user_id' => $userId, 'product_id' => $productId]);
    }
}

session_start();
require_once 'config.php';
require_once 'Cart.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$cart = new Cart($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'])) {
        $cart->addItem($userId, $_POST['product_id']);
    } elseif (isset($_POST['remove_item'])) {
        $cart->removeItem($userId, $_POST['remove_product_id']);
    } elseif (isset($_POST['order'])) {
        header('Location: wallet.php');
        exit();
    }
}

$cartItems = $cart->getCartItems($userId);
$total = 0;

foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Donuts</title>
</head>
<body>
    <div class="relative navigation-container z-50">
         <?php include 'navigation.php'; ?>
    </div>
    <div class="content-container mx-auto px-4 py-8 flex flex-col items-center">
        <div class="grid gap-x-8 gap-y-8 grid-cols-3 justify-items-center">
            <?php
            $products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($products as $product): ?>
                <div class="donut-container flex flex-col items-center gap-y-4">
                    <img class="w-24" src="img/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <form method="post">
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                        <button class="cart-add-button" type="submit">₱<?php echo htmlspecialchars($product['price']); ?></button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
     
        <!-- Cart Items -->
        <div class="cart-items mt-8">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item flex items-center justify-between">
                    <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo htmlspecialchars($item['quantity']); ?></span>
                    <span> ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    <form method="post" class="remove-item-form">
                        <input type="hidden" name="remove_product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                        <button class="remove-button" type="submit" name="remove_item"> Remove</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Total -->
        <div class="cart-total mt-8 flex items-center justify-center gap-x-3">
            <div class="cart-total-button">Total: ₱<?php echo number_format($total, 2); ?></div>
            <form method="post">
                <button class="pay-order-button" type="submit" name="order">ORDER</button>
            </form>
        </div>
    </div>
</body>
</html>
