<?php
require __DIR__ . '/includes/app.php';
require __DIR__ . '/includes/header.php';
require __DIR__ . '/includes/navbar.php';
require __DIR__ . '/includes/alert.php';

switch ($view) {
    case 'shop':
        require __DIR__ . '/views/shop.php';
        break;
    case 'login':
        require __DIR__ . '/views/login.php';
        break;
    case 'register':
        require __DIR__ . '/views/register.php';
        break;
    case 'cart':
        require __DIR__ . '/views/cart.php';
        break;
    case 'subscription':
        require __DIR__ . '/views/subscription.php';
        break;
    case 'about':
        require __DIR__ . '/views/about.php';
        break;
    case 'receipt':
        require __DIR__ . '/views/receipt.php';
        break;
    case 'admin_login':
        require __DIR__ . '/views/admin_login.php';
        break;
    case 'admin':
        require __DIR__ . '/views/admin.php';
        break;
    default:
        require __DIR__ . '/views/shop.php';
        break;
}

require __DIR__ . '/includes/footer.php';
