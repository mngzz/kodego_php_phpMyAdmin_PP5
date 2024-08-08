<?php
session_start();
require_once 'config.php';

class Cart {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
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
}

$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if ($userId === null) {
    header('Location: login.php');
    exit();
}

$cart = new Cart($pdo);

$cartItems = $cart->getCartItems($userId);
$total = 0;

foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$orderSuccess = isset($_SESSION['order_success']) ? $_SESSION['order_success'] : false;
unset($_SESSION['order_success']); 

$orderFirst = false;
if (isset($_POST['pay'])) {
    if (empty($cartItems)) {
        $orderFirst = true;
    } else {
        $_SESSION['order_success'] = true;
        header('Location: wallet.php'); 
        exit();
    }
}

if (isset($_POST['order_again'])) {
    $cart->clearCart($userId);
    unset($_SESSION['order_success']);
    header('Location: cart.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Donuts</title>
    <style>
        .hidden { display: none; }
        .message { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="relative navigation-container z-50">
         <?php include 'navigation.php'; ?>
    </div>
    <div class="content-container mx-auto px-4 py-8 flex flex-col items-center">
        <!-- Success Message -->
        <?php if ($orderSuccess): ?>
            <div id="order-success" class="success-message mb-4 text-green-500">Order Success</div>
        <?php endif; ?>

        <!-- "Order First" Message -->
        <?php if ($orderFirst): ?>
            <div class="error-message mb-4 text-red-500">Order first!</div>
        <?php endif; ?>

        <!-- Cart Items -->
        <div class="cart-items mt-8">
            <?php foreach ($cartItems as $item): ?>
                <div class="cart-item flex items-center justify-between">
                    <span><?php echo htmlspecialchars($item['name']); ?> x <?php echo htmlspecialchars($item['quantity']); ?></span>
                    <span> ₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Tottal -->
        <div class="cart-total mt-8 flex items-center justify-center gap-x-3">
            <div class="cart-total-button">Total: ₱<?php echo number_format($total, 2); ?></div>
            <form method="post">
                <button class="pay-order-button" type="submit" name="pay">Pay</button>
            </form>
        </div>

        <!-- Messages -->
        <div id="messages" class="<?php echo $orderSuccess ? '' : 'hidden'; ?>">
            <div class="message" id="deliver-success">Deliver Success</div>
            <div class="message hidden" id="order-again">
                <form method="post">
                    <button type="submit" name="order_again">Order Again</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('order-success')) {
                setTimeout(() => {
                    document.getElementById('order-success').classList.add('hidden');
                    document.getElementById('deliver-success').classList.remove('hidden');
                }, 5000); 

                setTimeout(() => {
                    document.getElementById('deliver-success').classList.add('hidden');
                    document.getElementById('order-again').classList.remove('hidden');
                }, 10000);
            }
        });
    </script>
</body>
</html>
