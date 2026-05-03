<?php
$order = $_SESSION['last_order'] ?? null;
if (!$order) {
    header('Location: ?view=shop');
    exit;
}
?>
<div class="receipt-wrap">
  <div class="receipt-card" id="receipt-content">
    <div class="receipt-header">
      <div class="receipt-check">✅</div>
      <h2>ShopZone</h2>
      <p class="receipt-note">Order Confirmed!</p>
      <p>Order #<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?> &nbsp;|&nbsp; <?= $order['date'] ?></p>
      <p>Customer: <strong><?= htmlspecialchars($order['cust']) ?></strong></p>
    </div>

    <table class="receipt-table">
      <thead><tr><th>Item</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead>
      <tbody>
        <?php foreach ($order['items'] as $item): ?>
        <tr>
          <td><?= htmlspecialchars($item['name']) ?></td>
          <td><?= $item['qty'] ?></td>
          <td>$<?= number_format($item['price'], 2) ?></td>
          <td>$<?= number_format($item['subtotal'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div class="receipt-total">
      <div class="receipt-row"><span>Subtotal</span><span>$<?= number_format($order['subtotal'], 2) ?></span></div>
      <div class="receipt-row"><span>Tax (8%)</span><span>$<?= number_format($order['tax'], 2) ?></span></div>
      <div class="receipt-row grand"><span>Grand Total</span><span>$<?= number_format($order['grand'], 2) ?></span></div>
    </div>

    <p class="receipt-message">Thank you for shopping with ShopZone! 🎉</p>
  </div>

  <div id="no-print" class="button-row">
    <button class="print-btn">🖨️ Print / Save Receipt</button>
    <a href="?view=shop" class="btn btn-gray">🛍️ Shop More</a>
  </div>
</div>
