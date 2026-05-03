<div class="auth-wrap">
  <div class="auth-card">
    <h2>Create Account 🛒</h2>
    <p>Join ShopZone — it's free!</p>
    <form method="POST" action="?view=register">
      <div class="form-group"><label>Full Name</label><input type="text" name="cname" required placeholder="Your Name"></div>
      <div class="form-group"><label>Email</label><input type="email" name="email" required placeholder="you@email.com"></div>
      <div class="form-group"><label>Password</label><input type="password" name="cpass" required placeholder="Min 6 chars" minlength="6"></div>
      <button type="submit" name="cust_register" class="btn btn-blue btn-full">Create Account</button>
    </form>
    <div class="auth-switch">Already have an account? <a href="?view=login">Login</a></div>
  </div>
</div>
