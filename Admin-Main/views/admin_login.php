<div class="auth-wrap">
  <div class="auth-card">
    <h2>Admin Login 🔐</h2>
    <p>ShopZone Admin Panel</p>
    <form method="POST" action="?view=admin_login">
      <div class="form-group"><label>Username</label>
        <input type="text" name="auser" placeholder="admin" required>
      </div>
      <div class="form-group"><label>Password</label>
        <input type="password" name="apass" placeholder="••••••••" required>
      </div>
      <button type="submit" name="admin_login" class="btn btn-admin btn-full">Sign In</button>
    </form>
    <p class="auth-note">admin / admin123</p>
    <div class="auth-switch"><a href="?view=shop">← Back to Shop</a></div>
  </div>
</div>
