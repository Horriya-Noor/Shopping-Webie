<?php
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container">
    <div class="page-header">
        <h1>Premium Subscriptions</h1>
        <p>Elevate your shopping experience with our exclusive subscription plans</p>
    </div>

    <div class="subscription-plans">
        <?php foreach ($db['subscription_plans'] as $plan): ?>
            <div class="plan-card <?php echo $plan['popular'] ? 'popular' : ''; ?>">
                <?php if ($plan['popular']): ?>
                    <div class="popular-badge">Most Popular</div>
                <?php endif; ?>

                <h3><?php echo htmlspecialchars($plan['name']); ?></h3>

                <div class="plan-price">
                    <?php if ($plan['price'] == 0): ?>
                        Free
                    <?php else: ?>
                        ₹<?php echo number_format($plan['price']); ?>
                        <span>/<?php echo $plan['period']; ?></span>
                    <?php endif; ?>
                </div>

                <ul class="plan-features">
                    <?php foreach ($plan['features'] as $feature): ?>
                        <li><?php echo htmlspecialchars($feature); ?></li>
                    <?php endforeach; ?>
                </ul>

                <?php
                $current_sub = null;
                if (isset($_SESSION['customer_id'])) {
                    foreach ($db['user_subscriptions'] as $sub) {
                        if ($sub['customer_id'] == $_SESSION['customer_id'] && $sub['status'] == 'active') {
                            $current_sub = $sub;
                            break;
                        }
                    }
                }

                $is_current_plan = $current_sub && $current_sub['plan_id'] == $plan['id'];
                ?>

                <?php if ($is_current_plan): ?>
                    <button class="btn btn-success" disabled>Current Plan</button>
                <?php elseif ($plan['price'] == 0): ?>
                    <button class="btn btn-primary" disabled>Default Plan</button>
                <?php elseif (!isset($_SESSION['customer_id'])): ?>
                    <a href="?view=login" class="btn btn-primary">Subscribe Now</a>
                <?php else: ?>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                        <button type="submit" name="subscribe_plan" class="btn btn-primary">
                            Subscribe Now
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (isset($_SESSION['customer_id'])): ?>
        <div class="current-subscription">
            <h3>Your Current Subscription</h3>
            <?php
            $current_sub = null;
            foreach ($db['user_subscriptions'] as $sub) {
                if ($sub['customer_id'] == $_SESSION['customer_id'] && $sub['status'] == 'active') {
                    $current_sub = $sub;
                    break;
                }
            }

            if ($current_sub):
                $plan = array_filter($db['subscription_plans'], fn($p) => $p['id'] == $current_sub['plan_id']);
                $plan = reset($plan);
            ?>
                <div class="sub-info">
                    <p><strong>Plan:</strong> <?php echo htmlspecialchars($plan['name']); ?></p>
                    <p><strong>Status:</strong> <span class="status-active"><?php echo ucfirst($current_sub['status']); ?></span></p>
                    <p><strong>Start Date:</strong> <?php echo date('M d, Y', strtotime($current_sub['start_date'])); ?></p>
                    <p><strong>End Date:</strong> <?php echo date('M d, Y', strtotime($current_sub['end_date'])); ?></p>
                    <p><strong>Auto Renew:</strong> <?php echo $current_sub['auto_renew'] ? 'Yes' : 'No'; ?></p>
                </div>
            <?php else: ?>
                <p>You don't have an active subscription.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.subscription-plans {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin: 2rem 0;
}

.plan-card {
    background: white;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 2rem;
    text-align: center;
    position: relative;
    transition: all 0.3s ease;
}

.plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.plan-card.popular {
    border-color: #007bff;
    transform: scale(1.05);
}

.popular-badge {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    background: #007bff;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: bold;
}

.plan-price {
    font-size: 2rem;
    font-weight: bold;
    color: #007bff;
    margin: 1rem 0;
}

.plan-price span {
    font-size: 0.8rem;
    color: #666;
}

.plan-features {
    list-style: none;
    padding: 0;
    margin: 1.5rem 0;
    text-align: left;
}

.plan-features li {
    padding: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
}

.plan-features li:before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #28a745;
    font-weight: bold;
}

.btn {
    padding: 0.8rem 1.5rem;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.current-subscription {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 10px;
    margin-top: 2rem;
}

.sub-info {
    background: white;
    padding: 1.5rem;
    border-radius: 5px;
    border: 1px solid #e0e0e0;
}

.status-active {
    color: #28a745;
    font-weight: bold;
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    color: #333;
    margin-bottom: 0.5rem;
}

.page-header p {
    color: #666;
    font-size: 1.1rem;
}
</style>

<?php
include 'includes/footer.php';
?>