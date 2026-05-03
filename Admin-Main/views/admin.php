<?php
if (!$is_admin) {
    header('Location: ?view=admin_login');
    exit;
}
$total_sales = array_sum($db['monthly_sales']);
?>
<div class="admin-wrap">
  <aside class="sidebar">
    <div class="sidebar-header">Shop<span>Admin</span></div>
    <a href="?view=admin&apage=dashboard" class="<?= $apage==='dashboard'?'act':'' ?>">📊 Dashboard</a>
    <a href="?view=admin&apage=products" class="<?= $apage==='products'?'act':'' ?>">📦 Products</a>
    <a href="?view=admin&apage=subscribers" class="<?= $apage==='subscribers'?'act':'' ?>">📧 Subscribers</a>
    <a href="?view=admin&apage=subscriptions" class="<?= $apage==='subscriptions'?'act':'' ?>">💎 Subscriptions</a>
    <a href="?view=admin&apage=sales" class="<?= $apage==='sales'?'act':'' ?>">💰 Sales</a>
    <a href="?view=admin&apage=users" class="<?= $apage==='users'?'act':'' ?>">👥 Users</a>
    <a href="?view=admin&apage=orders" class="<?= $apage==='orders'?'act':'' ?>">📋 Orders</a>
    <div class="sidebar-footer">
      <a href="?view=shop" class="accent">🛍️ View Shop</a>
      <a href="?alogout=1" class="danger">🚪 Logout</a>
    </div>
  </aside>

  <main class="amain">
    <?php if ($msg): ?>
      <div class="alert <?= $msg_type ?> alert-full" id="amsg">
        <?= $msg ?> <span class="alert-close" onclick="document.getElementById('amsg').remove()">×</span>
    <?php endif; ?>

    <?php if ($apage === 'dashboard'): ?>
      <div class="page-title">Dashboard</div>
      <div class="stats-row">
        <div class="stat-card"><div class="slabel">Products</div><div class="sval"><?= count($db['products']) ?></div><div class="sbadge">▲ Active</div></div>
        <div class="stat-card"><div class="slabel">Subscribers</div><div class="sval"><?= count($db['subscribers']) ?></div><div class="sbadge">▲ Total</div></div>
        <div class="stat-card"><div class="slabel">Subscriptions</div><div class="sval"><?= count(array_filter($db['user_subscriptions'], fn($s) => $s['status'] === 'active')) ?></div><div class="sbadge">▲ Active</div></div>
        <div class="stat-card"><div class="slabel">Revenue</div><div class="sval">$<?= number_format($total_sales) ?></div><div class="sbadge">▲ 2025</div></div>
        <div class="stat-card"><div class="slabel">Users</div><div class="sval"><?= count($db['customers']) ?></div><div class="sbadge">▲ Registered</div></div>
      </div>
      <div class="form-card">
        <h3>📈 Monthly Sales</h3>
        <div class="chart-bars">
          <?php $mx = max($db['monthly_sales']); foreach ($db['monthly_sales'] as $mo => $v): $h = round(($v / $mx) * 130); ?>
            <div class="bar-wrap">
              <div class="bar-val">$<?= number_format($v / 1000, 1) ?>k</div>
              <div class="bar" style="height:<?= $h ?>px"></div>
              <div class="bar-label"><?= $mo ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="table-card">
        <div class="thead"><span>Recent Orders</span></div>
        <table class="at">
          <thead><tr><th>#</th><th>Customer</th><th>Items</th><th>Total</th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach (array_slice(array_reverse($db['orders']), 0, 5) as $o): ?>
              <tr>
                <td><?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($o['cust']) ?></td>
                <td><?= count($o['items']) ?></td>
                <td class="text-success">$<?= number_format($o['grand'], 2) ?></td>
                <td><?= $o['date'] ?></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($db['orders'])): ?>
              <tr><td colspan="5" class="text-muted-alt table-empty">No orders yet</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    <?php elseif ($apage === 'products'): ?>
      <div class="page-title">Products Management</div>
      <div class="form-card">
        <h3>➕ Add New Product</h3>
        <form method="POST" action="?view=admin&apage=products" enctype="multipart/form-data">
          <div class="frow">
            <div><label>Product Name</label><input type="text" name="pname" required placeholder="Name"></div>
            <div><label>Regular Price ($)</label><input type="number" name="pprice" step="0.01" required placeholder="e.g. 49.99"></div>
            <div><label>Sale Price ($)</label><input type="number" name="psale" step="0.01" placeholder="Leave 0 if no sale"></div>
            <div><label>Stock</label><input type="number" name="pstock" required placeholder="Qty"></div>
          </div>
          <div class="frow">
            <div><label>Category</label>
              <select name="pcat">
                <option>Electronics</option><option>Footwear</option><option>Clothing</option>
                <option>Accessories</option><option>Fitness</option><option>Home</option>
              </select>
            </div>
            <div><label>Product Image</label><input type="file" name="pimg" accept="image/*"></div>
            <div class="span-full"><label>Description</label><input type="text" name="pdesc" placeholder="Short product description"></div>
          </div>
          <div class="chk-row">
            <input type="checkbox" name="on_sale" id="os"> <label for="os" class="form-label-alt">🔥 Mark as On Sale</label>
          </div>
          <button type="submit" name="add_product" class="btn btn-admin">Add Product</button>
        </form>
      </div>
      <div class="table-card">
        <div class="thead"><span>All Products (<?= count($db['products']) ?>)</span></div>
        <table class="at">
          <thead><tr><th>IMG</th><th>Name</th><th>Price</th><th>Sale</th><th>Stock</th><th>Cat</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($db['products'] as $p): ?>
              <tr>
                <td><img src="<?= img_src($p) ?>" class="img-thumb" alt=""></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td>$<?= number_format($p['price'], 2) ?></td>
                <td>
                  <?php if ($p['on_sale'] && $p['sale_price'] > 0): ?>
                    <span class="abadge sale">🔥 $<?= number_format($p['sale_price'], 2) ?></span>
                  <?php else: echo '<span class="small-text">—</span>'; endif; ?>
                </td>
                <td><?= $p['stock'] ?></td>
                <td><?= htmlspecialchars($p['category']) ?></td>
                <td>
                  <button onclick='openEdit(<?= htmlspecialchars(json_encode($p), ENT_QUOTES) ?>)' class="btn btn-admin btn-sm">✏️ Edit</button>
                  <form method="POST" class="inline-form" onsubmit="return confirm('Delete this product?')">
                    <input type="hidden" name="del_id" value="<?= $p['id'] ?>">
                    <button name="delete_product" class="btn btn-red btn-sm">🗑️</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="modal-bg" id="edit-modal">
        <div class="modal">
          <h3>✏️ Edit Product</h3>
          <form method="POST" action="?view=admin&apage=products" enctype="multipart/form-data">
            <input type="hidden" name="eid" id="e-id">
            <div class="frow2">
              <div><label>Name</label><input type="text" name="pname" id="e-name" required></div>
              <div><label>Regular Price</label><input type="number" name="pprice" id="e-price" step="0.01" required></div>
              <div><label>Sale Price</label><input type="number" name="psale" id="e-sale" step="0.01"></div>
              <div><label>Stock</label><input type="number" name="pstock" id="e-stock" required></div>
            </div>
            <div class="form-group"><label class="form-label-alt">Category</label>
              <select name="pcat" id="e-cat" class="dark-select">
                <option>Electronics</option><option>Footwear</option><option>Clothing</option>
                <option>Accessories</option><option>Fitness</option><option>Home</option>
              </select>
            </div>
            <div class="form-group"><label class="form-label-alt">Description</label>
              <input type="text" name="pdesc" id="e-desc" class="dark-input">
            </div>
            <div class="form-group"><label class="form-label-alt">New Image (optional)</label>
              <input type="file" name="pimg" accept="image/*" class="dark-input">
            </div>
            <div class="chk-row">
              <input type="checkbox" name="on_sale" id="e-sale-chk">
              <label for="e-sale-chk" class="form-label-alt">🔥 On Sale</label>
            </div>
            <div class="button-row">
              <button type="submit" name="edit_product" class="btn btn-admin">Save Changes</button>
              <button type="button" onclick="closeEdit()" class="btn btn-gray">Cancel</button>
            </div>
          </form>
        </div>
      </div>

    <?php elseif ($apage === 'subscribers'): ?>
      <div class="page-title">Subscribers</div>
      <div class="stats-row compact-stats">
        <div class="stat-card"><div class="slabel">Total</div><div class="sval"><?= count($db['subscribers']) ?></div></div>
        <div class="stat-card"><div class="slabel">This Month</div><div class="sval"><?= count(array_filter($db['subscribers'], fn($s) => substr($s['joined'], 0, 7) === date('Y-m'))) ?></div></div>
      </div>
      <div class="table-card">
        <div class="thead"><span>Email List</span></div>
        <table class="at">
          <thead><tr><th>#</th><th>Email</th><th>Joined</th><th>Action</th></tr></thead>
          <tbody>
            <?php foreach ($db['subscribers'] as $s): ?>
              <tr>
                <td><?= $s['id'] ?></td>
                <td><?= htmlspecialchars($s['email']) ?></td>
                <td><?= $s['joined'] ?></td>
                <td>
                  <form method="POST" onsubmit="return confirm('Remove subscriber?')">
                    <input type="hidden" name="sid" value="<?= $s['id'] ?>">
                    <button name="remove_sub" class="btn btn-red btn-sm">Remove</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php elseif ($apage === 'subscriptions'): ?>
      <div class="page-title">Subscription Management</div>

      <!-- Subscription Plans -->
      <div class="form-card">
        <h3>📋 Subscription Plans</h3>
        <div class="table-card">
          <table class="at">
            <thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Period</th><th>Features</th><th>Popular</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($db['subscription_plans'] as $plan): ?>
                <tr>
                  <td><?= $plan['id'] ?></td>
                  <td><?= htmlspecialchars($plan['name']) ?></td>
                  <td>₹<?= number_format($plan['price']) ?></td>
                  <td><?= $plan['period'] ?></td>
                  <td>
                    <ul style="margin: 0; padding-left: 1rem;">
                      <?php foreach ($plan['features'] as $feature): ?>
                        <li style="font-size: 0.9rem;"><?= htmlspecialchars($feature) ?></li>
                      <?php endforeach; ?>
                    </ul>
                  </td>
                  <td><?= $plan['popular'] ? '⭐ Yes' : 'No' ?></td>
                  <td>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Edit plan?')">
                      <input type="hidden" name="plan_id" value="<?= $plan['id'] ?>">
                      <button name="edit_plan" class="btn btn-blue btn-sm">Edit</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <!-- User Subscriptions -->
      <div class="form-card">
        <h3>👥 Active User Subscriptions</h3>
        <div class="stats-row compact-stats">
          <div class="stat-card"><div class="slabel">Total Active</div><div class="sval"><?= count(array_filter($db['user_subscriptions'], fn($s) => $s['status'] === 'active')) ?></div></div>
          <div class="stat-card"><div class="slabel">Premium</div><div class="sval"><?= count(array_filter($db['user_subscriptions'], fn($s) => $s['status'] === 'active' && $s['plan_id'] == 2)) ?></div></div>
          <div class="stat-card"><div class="slabel">VIP Elite</div><div class="sval"><?= count(array_filter($db['user_subscriptions'], fn($s) => $s['status'] === 'active' && $s['plan_id'] == 3)) ?></div></div>
        </div>
        <div class="table-card">
          <table class="at">
            <thead><tr><th>ID</th><th>Customer</th><th>Plan</th><th>Start Date</th><th>End Date</th><th>Status</th><th>Auto Renew</th><th>Actions</th></tr></thead>
            <tbody>
              <?php foreach ($db['user_subscriptions'] as $sub):
                $customer = array_filter($db['customers'], fn($c) => $c['id'] == $sub['customer_id']);
                $customer = reset($customer);
                $plan = array_filter($db['subscription_plans'], fn($p) => $p['id'] == $sub['plan_id']);
                $plan = reset($plan);
              ?>
                <tr>
                  <td><?= $sub['id'] ?></td>
                  <td><?= htmlspecialchars($customer['name'] ?? 'Unknown') ?> (<?= htmlspecialchars($customer['email'] ?? '') ?>)</td>
                  <td><?= htmlspecialchars($plan['name'] ?? 'Unknown') ?></td>
                  <td><?= date('M d, Y', strtotime($sub['start_date'])) ?></td>
                  <td><?= date('M d, Y', strtotime($sub['end_date'])) ?></td>
                  <td><span class="status-<?= $sub['status'] ?>"><?= ucfirst($sub['status']) ?></span></td>
                  <td><?= $sub['auto_renew'] ? 'Yes' : 'No' ?></td>
                  <td>
                    <?php if ($sub['status'] === 'active'): ?>
                      <form method="POST" style="display: inline;" onsubmit="return confirm('Cancel this subscription?')">
                        <input type="hidden" name="sub_id" value="<?= $sub['id'] ?>">
                        <button name="cancel_subscription" class="btn btn-red btn-sm">Cancel</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php elseif ($apage === 'sales'): ?>
      <?php $ts = array_sum($db['monthly_sales']); $mx = max($db['monthly_sales']); $mn = min($db['monthly_sales']); $best = array_search($mx, $db['monthly_sales']); $worst = array_search($mn, $db['monthly_sales']); ?>
      <div class="page-title">Sales Analytics</div>
      <div class="stats-row">
        <div class="stat-card"><div class="slabel">Yearly Revenue</div><div class="sval">$<?= number_format($ts) ?></div><div class="sbadge">▲ 2025</div></div>
        <div class="stat-card"><div class="slabel">Best Month</div><div class="sval"><?= $best ?></div><div class="sbadge">$<?= number_format($mx) ?></div></div>
        <div class="stat-card"><div class="slabel">Avg Monthly</div><div class="sval">$<?= number_format($ts / 12) ?></div></div>
        <div class="stat-card"><div class="slabel">Lowest Month</div><div class="sval"><?= $worst ?></div><div class="sbadge text-danger">$<?= number_format($mn) ?></div></div>
      </div>
      <div class="form-card">
        <h3>📊 Monthly Breakdown</h3>
        <div class="chart-bars compact-chart">
          <?php foreach ($db['monthly_sales'] as $mo => $v): $h = round(($v / $mx) * 160); ?>
            <div class="bar-wrap">
              <div class="bar-val">$<?= number_format($v / 1000, 1) ?>k</div>
              <div class="bar" style="height:<?= $h ?>px"></div>
              <div class="bar-label"><?= $mo ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="table-card">
        <div class="thead"><span>Monthly Revenue Table</span></div>
        <table class="at">
          <thead><tr><th>Month</th><th>Revenue</th><th>vs Avg</th></tr></thead>
          <tbody>
            <?php $avg = $ts / 12; foreach ($db['monthly_sales'] as $mo => $v): $d = $v - $avg; $sign = $d >= 0 ? '+' : ''; ?>
              <tr>
                <td><?= $mo ?></td>
                <td>$<?= number_format($v, 2) ?></td>
                <td class="<?= $d >= 0 ? 'text-success' : 'text-danger' ?>"><?= $sign ?>$<?= number_format($d, 2) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php elseif ($apage === 'users'): ?>
      <div class="page-title">Site Users</div>
      <div class="stats-row compact-stats-3">
        <div class="stat-card"><div class="slabel">Total</div><div class="sval"><?= count($db['customers']) ?></div></div>
        <div class="stat-card"><div class="slabel">Active</div><div class="sval"><?= count(array_filter($db['customers'], fn($c) => $c['status'] === 'Active')) ?></div></div>
        <div class="stat-card"><div class="slabel">Orders</div><div class="sval"><?= array_sum(array_column($db['customers'], 'orders')) ?></div></div>
      </div>
      <div class="table-card">
        <div class="thead"><span>All Users</span></div>
        <table class="at">
          <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Orders</th><th>Joined</th><th>Status</th></tr></thead>
          <tbody>
            <?php foreach ($db['customers'] as $c): ?>
              <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['name']) ?></td>
                <td><?= htmlspecialchars($c['email']) ?></td>
                <td><?= $c['orders'] ?></td>
                <td><?= $c['joined'] ?></td>
                <td><span class="abadge <?= strtolower($c['status']) ?>"><?= $c['status'] ?></span></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    <?php elseif ($apage === 'orders'): ?>
      <div class="page-title">All Orders</div>
      <div class="table-card">
        <div class="thead"><span>Orders (<?= count($db['orders']) ?>)</span></div>
        <table class="at">
          <thead><tr><th>Order#</th><th>Customer</th><th>Items</th><th>Total</th><th>Date</th><th>Status</th></tr></thead>
          <tbody>
            <?php foreach (array_reverse($db['orders']) as $o): ?>
              <tr>
                <td>#<?= str_pad($o['id'], 5, '0', STR_PAD_LEFT) ?></td>
                <td><?= htmlspecialchars($o['cust']) ?></td>
                <td>
                  <?php foreach ($o['items'] as $it): ?>
                    <div class="small-text"><?= htmlspecialchars($it['name']) ?> ×<?= $it['qty'] ?></div>
                  <?php endforeach; ?>
                </td>
                <td class="text-success">$<?= number_format($o['grand'], 2) ?></td>
                <td><?= $o['date'] ?></td>
                <td><span class="abadge active"><?= $o['status'] ?></span></td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($db['orders'])): ?>
              <tr><td colspan="6" class="text-muted-alt table-empty-lg">No orders placed yet</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </main>
</div>

<script>
function openEdit(p) {
  document.getElementById('e-id').value = p.id;
  document.getElementById('e-name').value = p.name;
  document.getElementById('e-price').value = p.price;
  document.getElementById('e-sale').value = p.sale_price;
  document.getElementById('e-stock').value = p.stock;
  document.getElementById('e-desc').value = p.desc || '';
  document.getElementById('e-sale-chk').checked = p.on_sale;
  var sel = document.getElementById('e-cat');
  for (var i = 0; i < sel.options.length; i++) { if (sel.options[i].value === p.category) { sel.selectedIndex = i; break; } }
  document.getElementById('edit-modal').classList.add('open');
}
function closeEdit() { document.getElementById('edit-modal').classList.remove('open'); }
document.getElementById('edit-modal')?.addEventListener('click', function(e) { if (e.target === this) closeEdit(); });
</script>
