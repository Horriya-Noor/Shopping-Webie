<div class="hero">
  <h1>Shop <span>Everything</span></h1>
  <p>Fresh deals, best prices — updated by our admin in real time!</p>
  <div class="hero-btns">
    <a href="#products" class="btn btn-blue">Browse Products</a>
    <?php if (!$is_cust): ?><a href="?view=register" class="btn btn-outline">Join Free</a><?php endif; ?>
  </div>
</div>

<div class="section" id="products">
  <div class="section-title">🛍️ All Products</div>
  <div class="filter-bar">
    <button class="filter-btn active" onclick="filterCat('all',this)">All</button>
    <?php $cats = array_unique(array_column($db['products'], 'category')); foreach ($cats as $cat): ?>
      <button class="filter-btn" onclick="filterCat('<?= htmlspecialchars($cat) ?>',this)"><?= htmlspecialchars($cat) ?></button>
    <?php endforeach; ?>
  </div>

  <div class="products-grid" id="pg">
    <?php foreach ($db['products'] as $p): $eff_price = product_price($p); ?>
      <div class="card" data-cat="<?= htmlspecialchars($p['category']) ?>">
        <?php if ($p['on_sale'] && $p['sale_price'] > 0): ?>
          <div class="sale-tag">🔥 Sale</div>
        <?php endif; ?>
        <img src="<?= img_src($p) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
        <div class="card-body">
          <div class="card-cat"><?= htmlspecialchars($p['category']) ?></div>
          <div class="card-name"><?= htmlspecialchars($p['name']) ?></div>
          <div class="card-desc"><?= htmlspecialchars($p['desc']) ?></div>
          <div class="card-price">
            <span class="price-now">$<?= number_format($eff_price, 2) ?></span>
            <?php if ($p['on_sale'] && $p['sale_price'] > 0): ?>
              <span class="price-old">$<?= number_format($p['price'], 2) ?></span>
            <?php endif; ?>
          </div>
          <form method="POST" action="?view=shop">
            <input type="hidden" name="pid" value="<?= $p['id'] ?>">
            <button type="submit" name="add_to_cart" class="add-btn <?= $p['stock'] <= 0 ? 'disabled' : '' ?>" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
              <?= $p['stock'] > 0 ? '🛒 Add to Cart' : 'Out of Stock' ?>
            </button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="sub-bar">
  <h3>📬 Stay in the loop</h3>
  <p>Subscribe for exclusive deals and new arrivals.</p>
  <form method="POST" action="?view=shop" class="sub-form">
    <input type="email" name="sub_email" placeholder="your@email.com" required>
    <button type="submit" name="subscribe">Subscribe</button>
  </form>
</div>

<script>
function filterCat(cat, btn) {
  document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('#pg .card').forEach(c => {
    c.style.display = (cat === 'all' || c.dataset.cat === cat) ? '' : 'none';
  });
}
</script>
