<nav>
  <a href="?view=shop" class="brand">Shop<span>Zone</span></a>
  <div class="nav-links">
    <a href="?view=shop">🏠 Shop</a>
    <a href="?view=subscription">💎 Subscriptions</a>
    <a href="?view=about">ℹ️ About</a>
    <?php if ($is_cust): ?>
      <a href="?view=shop" class="accent">👤 <?= htmlspecialchars($_SESSION['cust_name']) ?></a>
      <a href="?clogout=1">Logout</a>
    <?php else: ?>
      <a href="?view=login">Login</a>
      <a href="?view=register">Register</a>
    <?php endif; ?>
    <?php if ($is_admin): ?>
      <a href="?view=admin" class="accent">⚙ Admin</a>
      <a href="?alogout=1" class="danger">Admin Logout</a>
    <?php else: ?>
      <a href="?view=admin_login" class="small">Admin</a>
    <?php endif; ?>
    <a href="?view=cart" class="cart-btn">🛒 Cart <span class="badge-pill"><?= $cart_count ?></span></a>
  </div>
</nav>
