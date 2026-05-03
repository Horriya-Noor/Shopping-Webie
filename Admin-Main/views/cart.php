<?php
$subtotal = 0;
$cart_items = [];
foreach ($_SESSION['cart'] as $pid => $qty) {
    foreach ($db['products'] as $p) {
        if ($p['id'] == $pid) {
            $ep = product_price($p);
            $cart_items[] = ['p' => $p, 'qty' => $qty, 'ep' => $ep, 'sub' => $ep * $qty];
            $subtotal += $ep * $qty;
        }
    }
}
$tax = round($subtotal * 0.08, 2);
$grand = $subtotal + $tax;
?>
<div class="cart-wrap">
  <h2 class="page-title">🛒 Your Cart</h2>

  <?php if (empty($cart_items)): ?>
    <div class="empty-state">
      <div class="empty-state-icon">🛒</div>
      <p class="empty-state-title">Your cart is empty</p>
      <a href="?view=shop" class="btn btn-blue">Continue Shopping</a>
    </div>
  <?php else: ?>

  <table class="cart-table">
    <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($cart_items as $ci): $p = $ci['p']; ?>
      <tr>
        <td><strong><?= htmlspecialchars($p['name']) ?></strong><br><span class="card-cat-muted"><?= $p['category'] ?></span></td>
        <td>$<?= number_format($ci['ep'], 2) ?></td>
        <td>
            <form method="POST" class="inline-form">
            <input type="hidden" name="pid" value="<?= $p['id'] ?>">
            <input type="number" name="qty" value="<?= $ci['qty'] ?>" min="0" class="qty-box" onchange="this.form.submit()">
            <input type="hidden" name="update_qty">
          </form>
        </td>
        <td><strong>$<?= number_format($ci['sub'], 2) ?></strong></td>
        <td>
          <form method="POST">
            <input type="hidden" name="pid" value="<?= $p['id'] ?>">
            <button name="remove_cart" class="btn btn-red btn-sm" onclick="return confirm('Remove item?')">✕</button>
          </form>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="cart-summary">
    <div class="summary-row"><span>Subtotal</span><span>$<?= number_format($subtotal, 2) ?></span></div>
    <div class="summary-row"><span>Tax (8%)</span><span>$<?= number_format($tax, 2) ?></span></div>
    <div class="summary-row"><span>Grand Total</span><span>$<?= number_format($grand, 2) ?></span></div>
  </div>

  <div class="order-form">
    <h3>📋 Order Details</h3>
    <form method="POST" action="?view=cart">
      <div class="form-row2">
        <div class="form-group"><label>Name</label><input type="text" name="c_name" value="<?= htmlspecialchars($_SESSION['cust_name'] ?? '') ?>" required placeholder="Full Name"></div>
        <div class="form-group"><label>Email</label><input type="email" name="c_email" value="<?= htmlspecialchars($_SESSION['cust_email'] ?? '') ?>" required placeholder="Email"></div>
        <div class="form-group"><label>Phone</label><input type="tel" name="c_phone" placeholder="+92 3xx xxxxxxx"></div>
        <div class="form-group"><label>Address</label><input type="text" name="c_addr" placeholder="Delivery Address" required></div>
      </div>
      <div class="button-row">
        <button type="submit" name="place_order" class="btn btn-blue" onclick="return confirm('Confirm your order?')">✅ Place Order & Get Receipt</button>
        <button type="submit" name="clear_cart" class="btn btn-red" onclick="return confirm('Clear all items?')">🗑️ Clear Cart</button>
        <a href="?view=shop" class="btn btn-gray">Continue Shopping</a>
      </div>
    </form>
  </div>
  <?php endif; ?>
</div>
