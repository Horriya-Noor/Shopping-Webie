<?php
session_start();

$data_file = __DIR__ . '/../shop_data.json';

function load_data() {
    global $data_file;
    if (!file_exists($data_file)) {
        $default = [
            'products' => [
                ['id'=>1,'name'=>'Wireless Headphones','price'=>79.99,'sale_price'=>59.99,'on_sale'=>true, 'stock'=>45,'category'=>'Electronics','img'=>'','desc'=>'Premium sound quality with noise cancellation.'],
                ['id'=>2,'name'=>'Running Shoes',      'price'=>54.99,'sale_price'=>0,    'on_sale'=>false,'stock'=>30,'category'=>'Footwear',   'img'=>'','desc'=>'Lightweight comfort for daily running.'],
                ['id'=>3,'name'=>'Smart Watch',        'price'=>129.99,'sale_price'=>99.99,'on_sale'=>true,'stock'=>20,'category'=>'Electronics','img'=>'','desc'=>'Track fitness and stay connected.'],
                ['id'=>4,'name'=>'Leather Wallet',     'price'=>24.99,'sale_price'=>0,    'on_sale'=>false,'stock'=>60,'category'=>'Accessories','img'=>'','desc'=>'Slim genuine leather wallet.'],
                ['id'=>5,'name'=>'Yoga Mat',           'price'=>34.99,'sale_price'=>24.99,'on_sale'=>true,'stock'=>15,'category'=>'Fitness',    'img'=>'','desc'=>'Non-slip premium yoga mat.'],
            ],
            'customers' => [
                ['id'=>1,'name'=>'Ali Raza',  'email'=>'ali@example.com',  'pass'=>password_hash('pass123',PASSWORD_DEFAULT),'orders'=>5, 'joined'=>'2024-10-01','status'=>'Active'],
                ['id'=>2,'name'=>'Sara Khan', 'email'=>'sara@example.com', 'pass'=>password_hash('pass123',PASSWORD_DEFAULT),'orders'=>2, 'joined'=>'2024-11-15','status'=>'Active'],
            ],
            'subscribers' => [
                ['id'=>1,'email'=>'ali@example.com',  'joined'=>date('Y-m-d')],
                ['id'=>2,'email'=>'sara@example.com', 'joined'=>date('Y-m-d')],
            ],
            'subscription_plans' => [
                ['id'=>1,'name'=>'Basic Member','price'=>0,'currency'=>'INR','period'=>'forever','features'=>['Browse All Collections','Product Information','Updates & Newsletters'],'popular'=>false],
                ['id'=>2,'name'=>'Premium Member','price'=>99,'currency'=>'INR','period'=>'month','features'=>['10% OFF on Everything','Early Access to New Items','Free Shipping','AI Personal Shopping Brain','Priority Support'],'popular'=>true],
                ['id'=>3,'name'=>'VIP Elite','price'=>499,'currency'=>'INR','period'=>'month','features'=>['20% OFF on Everything','Exclusive Secret Products','VIP Customer Service','Advanced AI Assistant','Monthly Gift'],'popular'=>false],
            ],
            'user_subscriptions' => [],
            'orders' => [],
            'monthly_sales' => ['Jan'=>3200,'Feb'=>4800,'Mar'=>4100,'Apr'=>5500,'May'=>6200,'Jun'=>5800,'Jul'=>7100,'Aug'=>6500,'Sep'=>5900,'Oct'=>7800,'Nov'=>9200,'Dec'=>11000],
            'next_id' => 6
        ];
        file_put_contents($data_file, json_encode($default, JSON_PRETTY_PRINT));
    }
    return json_decode(file_get_contents($data_file), true);
}

function save_data($data) {
    global $data_file;
    file_put_contents($data_file, json_encode($data, JSON_PRETTY_PRINT));
}

function redirect($url) {
    header("Location: $url");
    exit;
}

$db = load_data();
$msg = '';
$msg_type = 'success';

$upload_dir = __DIR__ . '/../uploads/';
if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

$view  = $_GET['view']  ?? 'shop';
$apage = $_GET['apage'] ?? 'dashboard';

$ADMIN = ['user'=>'admin','pass'=>'admin123'];

if (isset($_POST['admin_login'])) {
    if ($_POST['auser'] === $ADMIN['user'] && $_POST['apass'] === $ADMIN['pass']) {
        $_SESSION['admin'] = true;
        redirect('?view=admin');
    }
    $msg = 'Wrong admin credentials!';
    $msg_type = 'error';
}

if (isset($_GET['alogout'])) {
    unset($_SESSION['admin']);
    redirect('?view=shop');
}

$is_admin = !empty($_SESSION['admin']);

if (isset($_POST['cust_register'])) {
    $email = trim($_POST['email']);
    $exists = array_filter($db['customers'], fn($c) => $c['email'] === $email);
    if ($exists) {
        $msg = 'Email already registered!';
        $msg_type = 'error';
    } else {
        $db['customers'][] = [
            'id' => $db['next_id']++, 
            'name' => htmlspecialchars(trim($_POST['cname'])),
            'email' => $email,
            'pass' => password_hash($_POST['cpass'], PASSWORD_DEFAULT),
            'orders' => 0,
            'joined' => date('Y-m-d'),
            'status' => 'Active'
        ];
        save_data($db);
        $msg = 'Registered! Please login.';
        $view = 'login';
    }
}

if (isset($_POST['cust_login'])) {
    $email = trim($_POST['email']);
    foreach ($db['customers'] as $c) {
        if ($c['email'] === $email && password_verify($_POST['cpass'], $c['pass'])) {
            $_SESSION['cust_id'] = $c['id'];
            $_SESSION['cust_name'] = $c['name'];
            $_SESSION['cust_email'] = $c['email'];
            redirect('?view=shop');
        }
    }
    $msg = 'Invalid email or password!';
    $msg_type = 'error';
}

if (isset($_GET['clogout'])) {
    unset($_SESSION['cust_id'], $_SESSION['cust_name'], $_SESSION['cust_email']);
    redirect('?view=shop');
}

$is_cust = !empty($_SESSION['cust_id']);

if (isset($_POST['subscribe'])) {
    $semail = trim($_POST['sub_email']);
    $already = array_filter($db['subscribers'], fn($s) => $s['email'] === $semail);
    if (!$already) {
        $db['subscribers'][] = ['id' => $db['next_id']++, 'email' => $semail, 'joined' => date('Y-m-d')];
        save_data($db);
        $msg = 'Subscribed successfully! 🎉';
        $msg_type = 'success';
    } else {
        $msg = 'Already subscribed!';
        $msg_type = 'error';
    }
}

if (isset($_POST['subscribe_plan'])) {
    if (!isset($_SESSION['customer_id'])) {
        $msg = 'Please login to subscribe to a plan.';
        $msg_type = 'error';
        redirect('?view=login');
    }

    $plan_id = intval($_POST['plan_id']);
    $plan = array_filter($db['subscription_plans'], fn($p) => $p['id'] === $plan_id);
    $plan = reset($plan);

    if (!$plan) {
        $msg = 'Invalid subscription plan.';
        $msg_type = 'error';
    } else {
        // Cancel any existing active subscription
        foreach ($db['user_subscriptions'] as &$sub) {
            if ($sub['customer_id'] == $_SESSION['customer_id'] && $sub['status'] == 'active') {
                $sub['status'] = 'cancelled';
                $sub['end_date'] = date('Y-m-d');
            }
        }

        // Add new subscription
        $start_date = date('Y-m-d');
        $end_date = $plan['period'] === 'forever' ? '2099-12-31' : date('Y-m-d', strtotime('+1 month', strtotime($start_date)));

        $db['user_subscriptions'][] = [
            'id' => $db['next_id']++,
            'customer_id' => $_SESSION['customer_id'],
            'plan_id' => $plan_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => 'active',
            'auto_renew' => true
        ];

        save_data($db);
        $msg = 'Successfully subscribed to ' . $plan['name'] . '! 🎉';
        $msg_type = 'success';
    }
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (isset($_POST['add_to_cart'])) {
    $pid = intval($_POST['pid']);
    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid]++;
    } else {
        $_SESSION['cart'][$pid] = 1;
    }
    $msg = 'Item added to cart! 🛒';
    $msg_type = 'success';
}

if (isset($_POST['remove_cart'])) {
    unset($_SESSION['cart'][intval($_POST['pid'])]);
}

if (isset($_POST['update_qty'])) {
    $pid = intval($_POST['pid']);
    $qty = intval($_POST['qty']);
    if ($qty <= 0) {
        unset($_SESSION['cart'][$pid]);
    } else {
        $_SESSION['cart'][$pid] = $qty;
    }
}

if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    redirect('?view=shop');
}

if (isset($_POST['place_order'])) {
    $items = [];
    $total = 0;
    foreach ($_SESSION['cart'] as $pid => $qty) {
        foreach ($db['products'] as $p) {
            if ($p['id'] == $pid) {
                $price = $p['on_sale'] && $p['sale_price'] > 0 ? $p['sale_price'] : $p['price'];
                $items[] = ['name' => $p['name'], 'qty' => $qty, 'price' => $price, 'subtotal' => $price * $qty];
                $total += $price * $qty;
            }
        }
    }
    $tax = round($total * 0.08, 2);
    $order = [
        'id' => $db['next_id']++,
        'cust' => $_SESSION['cust_name'] ?? 'Guest',
        'email' => $_POST['c_email'] ?? '',
        'phone' => $_POST['c_phone'] ?? '',
        'items' => $items,
        'subtotal' => $total,
        'tax' => $tax,
        'grand' => $total + $tax,
        'date' => date('Y-m-d H:i'),
        'status' => 'Confirmed'
    ];
    $db['orders'][] = $order;

    $mo = date('M');
    $db['monthly_sales'][$mo] = ($db['monthly_sales'][$mo] ?? 0) + ($total + $tax);

    foreach ($db['customers'] as &$c) {
        if ($c['id'] == ($_SESSION['cust_id'] ?? 0)) {
            $c['orders']++;
        }
    }
    unset($c);

    save_data($db);
    $_SESSION['last_order'] = $order;
    $_SESSION['cart'] = [];
    redirect('?view=receipt');
}

if ($is_admin && isset($_POST['add_product'])) {
    $img_name = '';
    if (!empty($_FILES['pimg']['name'])) {
        $ext = pathinfo($_FILES['pimg']['name'], PATHINFO_EXTENSION);
        $img_name = 'prod_' . $db['next_id'] . '.' . $ext;
        move_uploaded_file($_FILES['pimg']['tmp_name'], $upload_dir . $img_name);
    }
    $on_sale = isset($_POST['on_sale']);
    $db['products'][] = [
        'id' => $db['next_id']++,
        'name' => htmlspecialchars($_POST['pname']),
        'price' => floatval($_POST['pprice']),
        'sale_price' => floatval($_POST['psale'] ?? 0),
        'on_sale' => $on_sale,
        'stock' => intval($_POST['pstock']),
        'category' => htmlspecialchars($_POST['pcat']),
        'img' => $img_name,
        'desc' => htmlspecialchars($_POST['pdesc'] ?? '')
    ];
    save_data($db);
    $msg = '✅ Product added!';
    $msg_type = 'success';
}

if ($is_admin && isset($_POST['delete_product'])) {
    $del_id = intval($_POST['del_id']);
    $db['products'] = array_values(array_filter($db['products'], fn($p) => $p['id'] !== $del_id));
    save_data($db);
    $msg = '🗑️ Product deleted!';
    $msg_type = 'success';
}

if ($is_admin && isset($_POST['edit_product'])) {
    $eid = intval($_POST['eid']);
    foreach ($db['products'] as &$p) {
        if ($p['id'] === $eid) {
            $p['name'] = htmlspecialchars($_POST['pname']);
            $p['price'] = floatval($_POST['pprice']);
            $p['sale_price'] = floatval($_POST['psale'] ?? 0);
            $p['on_sale'] = isset($_POST['on_sale']);
            $p['stock'] = intval($_POST['pstock']);
            $p['category'] = htmlspecialchars($_POST['pcat']);
            $p['desc'] = htmlspecialchars($_POST['pdesc'] ?? '');
            if (!empty($_FILES['pimg']['name'])) {
                $ext = pathinfo($_FILES['pimg']['name'], PATHINFO_EXTENSION);
                $img_name = 'prod_' . $eid . '.' . $ext;
                move_uploaded_file($_FILES['pimg']['tmp_name'], $upload_dir . $img_name);
                $p['img'] = $img_name;
            }
        }
    }
    unset($p);
    save_data($db);
    $msg = '✅ Product updated!';
    $msg_type = 'success';
}

if ($is_admin && isset($_POST['remove_sub'])) {
    $sid = intval($_POST['sid']);
    $db['subscribers'] = array_values(array_filter($db['subscribers'], fn($s) => $s['id'] !== $sid));
    save_data($db);
    $msg = 'Subscriber removed.';
    $msg_type = 'success';
}

if ($is_admin && isset($_POST['cancel_subscription'])) {
    $sub_id = intval($_POST['sub_id']);
    foreach ($db['user_subscriptions'] as &$sub) {
        if ($sub['id'] == $sub_id) {
            $sub['status'] = 'cancelled';
            $sub['end_date'] = date('Y-m-d');
            $sub['auto_renew'] = false;
            break;
        }
    }
    save_data($db);
    $msg = 'Subscription cancelled.';
    $msg_type = 'success';
}

$db = load_data();
$cart_count = array_sum($_SESSION['cart']);

function product_price($p) {
    return ($p['on_sale'] && $p['sale_price'] > 0) ? $p['sale_price'] : $p['price'];
}

function img_src($p) {
    $upload_path = __DIR__ . '/../uploads/' . $p['img'];
    if (!empty($p['img']) && file_exists($upload_path)) {
        return 'uploads/' . htmlspecialchars($p['img']);
    }
    $colors = ['Electronics' => '4f7ef8', 'Footwear' => 'f87c4f', 'Fitness' => '48bb78', 'Accessories' => 'f6c90e', 'Clothing' => 'c084fc', 'Home' => '67e8f9'];
    $c = $colors[$p['category']] ?? '94a3b8';
    return "https://placehold.co/300x220/$c/fff?text=" . urlencode($p['name']);
}

