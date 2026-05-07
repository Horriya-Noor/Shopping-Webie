
/* =====================================================
   STATE / DATABASE
===================================================== */
const DB = {
  products: [
    {id:1,name:'Wireless Headphones',price:79.99,sale_price:59.99,on_sale:true,stock:45,category:'Electronics',img:'https://placehold.co/400x300/1a1a2e/f0a500?text=🎧+Headphones',desc:'Premium noise-cancelling headphones with 30hr battery life.'},
    {id:2,name:'Running Shoes',price:54.99,sale_price:0,on_sale:false,stock:30,category:'Footwear',img:'https://placehold.co/400x300/1a2e1a/22c97a?text=👟+Running+Shoes',desc:'Lightweight cushioned soles for maximum comfort.'},
    {id:3,name:'Smart Watch',price:129.99,sale_price:99.99,on_sale:true,stock:20,category:'Electronics',img:'https://placehold.co/400x300/2e1a1a/ff6b35?text=⌚+Smart+Watch',desc:'Track health, notifications, and fitness all day.'},
    {id:4,name:'Leather Wallet',price:24.99,sale_price:0,on_sale:false,stock:60,category:'Accessories',img:'https://placehold.co/400x300/2e2a1a/f0a500?text=👛+Wallet',desc:'Slim genuine leather wallet with RFID protection.'},
    {id:5,name:'Yoga Mat',price:34.99,sale_price:24.99,on_sale:true,stock:15,category:'Fitness',img:'https://placehold.co/400x300/1a2e2a/22c97a?text=🧘+Yoga+Mat',desc:'Non-slip premium TPE yoga mat, 6mm thick.'},
    {id:6,name:'Sunglasses',price:44.99,sale_price:0,on_sale:false,stock:35,category:'Accessories',img:'https://placehold.co/400x300/1a1a2e/3b7ef8?text=🕶️+Sunglasses',desc:'UV400 polarized lenses in a titanium frame.'},
    {id:7,name:'Coffee Maker',price:89.99,sale_price:69.99,on_sale:true,stock:25,category:'Home & Living',img:'https://placehold.co/400x300/2e1a2a/ff6b35?text=☕+Coffee+Maker',desc:'Brew café-quality coffee at home in minutes.'},
    {id:8,name:'Skincare Set',price:59.99,sale_price:0,on_sale:false,stock:40,category:'Beauty',img:'https://placehold.co/400x300/2e1a2e/f0a500?text=🧴+Skincare',desc:'Complete 5-step skincare routine for glowing skin.'},
  ],
  customers: [
    {id:1,name:'Ali Raza',email:'ali@example.com',pass:'pass123',phone:'+92 300 1234567',orders:5,joined:'2024-10-01',status:'Active'},
    {id:2,name:'Sara Khan',email:'sara@example.com',pass:'pass123',phone:'+92 311 9876543',orders:2,joined:'2024-11-15',status:'Active'},
  ],
  subscribers: [],
  orders: [],
  monthly_sales: {Jan:3200,Feb:4800,Mar:4100,Apr:5500,May:6200,Jun:5800,Jul:7100,Aug:6500,Sep:5900,Oct:7800,Nov:9200,Dec:11000},
  nextId: 9
};

const state = {
  cart: {},         // {productId: qty}
  user: null,       // logged-in customer
  isAdmin: false,
  wishlist: new Set(),
  theme: localStorage.getItem('luxe-theme') || 'dark',
  activeFilter: 'all',
  adminCurrentTab: 'dashboard',
};

/* =====================================================
   THEME
===================================================== */
document.documentElement.setAttribute('data-theme', state.theme);
function toggleTheme() {
  state.theme = state.theme === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', state.theme);
  localStorage.setItem('luxe-theme', state.theme);
  playSound('click');
}

/* =====================================================
   SOUND ENGINE (Web Audio API)
===================================================== */
let audioCtx = null;
function getAudioCtx() {
  if (!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
  return audioCtx;
}
function playTone(freq, type='sine', duration=0.12, vol=0.15) {
  try {
    const ctx = getAudioCtx();
    const osc = ctx.createOscillator();
    const gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.frequency.value = freq; osc.type = type;
    gain.gain.setValueAtTime(vol, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + duration);
    osc.start(); osc.stop(ctx.currentTime + duration);
  } catch(e) {}
}
const soundMap = {
  click:   ()=>playTone(600,'sine',0.06,0.08),
  addCart: ()=>{ playTone(880,'sine',0.1,0.18); setTimeout(()=>playTone(1100,'sine',0.08,0.15),80); },
  remove:  ()=>playTone(300,'triangle',0.15,0.15),
  success: ()=>{ [880,1100,1320].forEach((f,i)=>setTimeout(()=>playTone(f,'sine',0.1),i*90)); },
  error:   ()=>playTone(200,'sawtooth',0.2,0.18),
  order:   ()=>{ [880,1100,1320,1100,880,1320].forEach((f,i)=>setTimeout(()=>playTone(f,'sine',0.12),i*90)); },
  nav:     ()=>playTone(700,'sine',0.07,0.08),
};
function playSound(name) { try { soundMap[name]?.(); } catch(e){} }

/* =====================================================
   TOAST NOTIFICATIONS
===================================================== */
function toast(msg, type='success', duration=3500) {
  const c = document.getElementById('toast-container');
  const el = document.createElement('div');
  el.className = `toast ${type}`;
  const icons = {success:'✅', error:'❌', warn:'⚠️'};
  el.innerHTML = `<span style="font-size:18px">${icons[type]||'ℹ️'}</span><span>${msg}</span>`;
  c.appendChild(el);
  playSound(type === 'success' ? 'success' : 'error');
  setTimeout(()=>{ el.style.animation='toast-out 0.3s ease forwards'; setTimeout(()=>el.remove(),300); }, duration);
}

/* =====================================================
   RIPPLE EFFECT
===================================================== */
function addRipple(e) {
  const el = e.currentTarget;
  if (!el) return;
  const r = document.createElement('div');
  r.className = 'ripple';
  const rect = el.getBoundingClientRect();
  const size = Math.max(rect.width, rect.height);
  r.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX-rect.left-size/2}px;top:${e.clientY-rect.top-size/2}px`;
  el.appendChild(r);
  setTimeout(()=>r.remove(), 600);
}

/* =====================================================
   PAGE NAVIGATION
===================================================== */
function showPage(page) {
  playSound('nav');
  document.getElementById('page-shop').style.display = page === 'shop' ? 'block' : 'none';
  document.getElementById('page-admin').classList.toggle('hidden', page !== 'admin');
  if (page === 'admin') renderAdminTab(state.adminCurrentTab);
  window.scrollTo({top:0, behavior:'smooth'});
}

/* =====================================================
   AUTH FUNCTIONS
===================================================== */
function openAuthModal() { playSound('click'); openModal('auth-modal'); }
function switchTab(tab) {
  document.getElementById('form-login').classList.toggle('hidden', tab !== 'login');
  document.getElementById('form-register').classList.toggle('hidden', tab !== 'register');
  document.getElementById('tab-login').classList.toggle('active', tab === 'login');
  document.getElementById('tab-register').classList.toggle('active', tab === 'register');
}
function doLogin() {
  const email = document.getElementById('l-email').value.trim();
  const pass  = document.getElementById('l-pass').value;
  const user  = DB.customers.find(c=>c.email===email && c.pass===pass);
  if (user) {
    state.user = user; state.isAdmin = false;
    closeModal('auth-modal'); updateNavUser();
    toast(`Welcome back, ${user.name}! 🎉`);
  } else toast('Invalid email or password!','error');
}
function doRegister() {
  const name  = document.getElementById('r-name').value.trim();
  const email = document.getElementById('r-email').value.trim();
  const pass  = document.getElementById('r-pass').value;
  const phone = document.getElementById('r-phone').value.trim();
  if (!name||!email||!pass) { toast('Please fill all fields','error'); return; }
  if (pass.length<6) { toast('Password must be 6+ characters','error'); return; }
  if (DB.customers.find(c=>c.email===email)) { toast('Email already registered!','error'); return; }
  const newUser = {id:DB.nextId++,name,email,pass,phone,orders:0,joined:new Date().toISOString().slice(0,10),status:'Active'};
  DB.customers.push(newUser);
  state.user = newUser; state.isAdmin = false;
  closeModal('auth-modal'); updateNavUser();
  toast(`Account created! Welcome, ${name}! 🎉`);
}
function doLogout() {
  state.user = null; state.isAdmin = false; updateNavUser();
  showPage('shop'); toast('Logged out successfully');
}
function updateNavUser() {
  const greet = document.getElementById('user-greet');
  const accBtn = document.getElementById('account-btn');
  const logBtn = document.getElementById('logout-btn');
  const adminBtn = document.getElementById('admin-nav-btn');
  if (state.user) {
    greet.textContent = `👤 ${state.user.name}`;
    greet.classList.remove('hidden');
    accBtn.classList.add('hidden');
    logBtn.classList.remove('hidden');
  } else {
    greet.classList.add('hidden');
    accBtn.classList.remove('hidden');
    logBtn.classList.add('hidden');
  }
  adminBtn.style.display = state.isAdmin ? '' : 'none';
}

/* Admin login */
function doAdminLogin() { closeModal('auth-modal'); openModal('admin-login-modal'); }
function confirmAdminLogin() {
  const u = document.getElementById('adm-user').value;
  const p = document.getElementById('adm-pass').value;
  if (u==='admin' && p==='admin123') {
    state.isAdmin = true; state.user = null;
    closeModal('admin-login-modal'); updateNavUser();
    showPage('admin'); toast('Welcome, Admin! 👑');
  } else toast('Wrong credentials!','error');
}
function adminLogout() {
  state.isAdmin = false; updateNavUser(); showPage('shop');
  toast('Admin logged out');
}

/* =====================================================
   MODAL HELPERS
===================================================== */
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
// Click outside to close
document.querySelectorAll('.modal-overlay').forEach(m=>{
  m.addEventListener('click',function(e){ if(e.target===this) closeModal(this.id); });
});

/* =====================================================
   NAV HELPERS
===================================================== */
function toggleNav() { document.getElementById('nav-links').classList.toggle('open'); }
function closeNav()  { document.getElementById('nav-links').classList.remove('open'); }

/* =====================================================
   PRODUCTS RENDERING
===================================================== */
function effectivePrice(p) { return p.on_sale && p.sale_price>0 ? p.sale_price : p.price; }
function productImgSrc(p)  { return p.img || `https://placehold.co/400x300/111827/f0a500?text=${encodeURIComponent(p.name)}`; }

function buildFilterChips() {
  const cats = [...new Set(DB.products.map(p=>p.category))];
  const row = document.getElementById('filter-row');
  row.innerHTML = `<button class="filter-chip active" onclick="setFilter('all',this)">All</button>`;
  cats.forEach(c => {
    const b = document.createElement('button');
    b.className = 'filter-chip';
    b.textContent = c;
    b.onclick = function(){ setFilter(c,this); };
    row.appendChild(b);
  });
}

function setFilter(cat, btn) {
  playSound('click');
  state.activeFilter = cat;
  document.querySelectorAll('.filter-chip').forEach(b=>b.classList.remove('active'));
  btn.classList.add('active');
  renderProducts();
}

function applyFilters() { renderProducts(); }

function renderProducts() {
  const q   = (document.getElementById('search-input')?.value||'').toLowerCase();
  const cat = state.activeFilter;
  const products = DB.products.filter(p=>{
    const matchCat = cat==='all' || p.category===cat;
    const matchQ   = !q || p.name.toLowerCase().includes(q) || p.desc.toLowerCase().includes(q) || p.category.toLowerCase().includes(q);
    return matchCat && matchQ;
  });

  const grid = document.getElementById('products-grid');
  if (products.length===0) {
    grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:60px;color:var(--text3);font-size:16px">😕 No products found</div>`;
    return;
  }
  grid.innerHTML = products.map((p,i)=>{
    const price = effectivePrice(p);
    const stockPct = Math.min(100, Math.round(p.stock/60*100));
    const isWished = state.wishlist.has(p.id);
    return `
    <div class="product-card" style="animation-delay:${i*0.05}s">
      ${p.on_sale&&p.sale_price>0 ? `<div class="sale-ribbon">🔥 Sale</div>` : ''}
      <button class="wish-btn" onclick="toggleWishlist(${p.id},this)" title="Wishlist">
        ${isWished?'❤️':'🤍'}
      </button>
      <div class="product-img-wrap" onclick="openDetail(${p.id})" style="cursor:pointer">
        <img src="${productImgSrc(p)}" alt="${p.name}" loading="lazy">
      </div>
      <div class="product-body">
        <div class="product-cat">${p.category}</div>
        <div class="product-name" onclick="openDetail(${p.id})" style="cursor:pointer">${p.name}</div>
        <div class="product-desc">${p.desc}</div>
        <div class="product-price">
          <span class="price-now">$${price.toFixed(2)}</span>
          ${p.on_sale&&p.sale_price>0 ? `<span class="price-old">$${p.price.toFixed(2)}</span>` : ''}
        </div>
        <div style="font-size:11px;color:var(--text3);margin-bottom:8px">📦 ${p.stock} in stock</div>
        <div class="stock-bar"><div class="stock-fill" style="width:${stockPct}%"></div></div>
        <button class="add-cart-btn ${p.stock<=0?'out':''}" onclick="${p.stock>0?`addToCart(${p.id},this);addRipple(event)`:''}" ${p.stock<=0?'disabled':''}>
          ${p.stock>0 ? '🛒 Add to Cart' : '❌ Out of Stock'}
        </button>
      </div>
    </div>`;
  }).join('');
}

/* =====================================================
   PRODUCT DETAIL MODAL
===================================================== */
function openDetail(id) {
  const p = DB.products.find(p=>p.id===id);
  if (!p) return;
  playSound('click');
  const price = effectivePrice(p);
  document.getElementById('detail-img').src = productImgSrc(p);
  document.getElementById('detail-cat').textContent = p.category;
  document.getElementById('detail-name').textContent = p.name;
  document.getElementById('detail-desc').textContent = p.desc;
  document.getElementById('detail-price').innerHTML = `$${price.toFixed(2)} ${p.on_sale&&p.sale_price>0?`<span style="font-size:15px;color:var(--text3);text-decoration:line-through;margin-left:8px">$${p.price.toFixed(2)}</span>`:''}`;
  document.getElementById('detail-stock').textContent = `📦 ${p.stock} in stock`;
  const btn = document.getElementById('detail-add-btn');
  if (p.stock>0) {
    btn.disabled = false; btn.className = 'btn btn-primary ripple-host';
    btn.onclick = (e)=>{ addToCart(p.id, btn); addRipple(e); closeModal('detail-modal'); };
  } else {
    btn.disabled = true; btn.textContent = '❌ Out of Stock';
    btn.className = 'btn btn-secondary'; btn.style.cursor='not-allowed';
  }
  openModal('detail-modal');
}

/* =====================================================
   WISHLIST
===================================================== */
function toggleWishlist(id, btn) {
  playSound('click');
  if (state.wishlist.has(id)) { state.wishlist.delete(id); btn.textContent='🤍'; toast('Removed from wishlist','warn'); }
  else { state.wishlist.add(id); btn.textContent='❤️'; toast('Added to wishlist! ❤️'); }
}

/* =====================================================
   CART
===================================================== */
function cartCount()  { return Object.values(state.cart).reduce((a,b)=>a+b,0); }
function cartTotal()  {
  return Object.entries(state.cart).reduce((acc,[pid,qty])=>{
    const p = DB.products.find(p=>p.id==pid);
    return acc + (p ? effectivePrice(p)*qty : 0);
  },0);
}

function addToCart(pid, btn) {
  const p = DB.products.find(p=>p.id==pid);
  if (!p||p.stock<=0) return;
  state.cart[pid] = (state.cart[pid]||0)+1;
  updateCartBadge();
  renderCart();
  playSound('addCart');
  toast(`${p.name} added to cart! 🛒`);
  // Fly animation
  if (btn) {
    const cartBtn = document.querySelector('.cart-nav-btn');
    if (cartBtn) {
      const dot = document.createElement('div');
      dot.style.cssText = `position:fixed;width:14px;height:14px;border-radius:50%;background:var(--accent);z-index:9999;pointer-events:none;`;
      const sr = btn.getBoundingClientRect(), cr = cartBtn.getBoundingClientRect();
      dot.style.left = (sr.left+sr.width/2)+'px'; dot.style.top = sr.top+'px';
      document.body.appendChild(dot);
      dot.animate([
        {left:`${sr.left+sr.width/2}px`,top:`${sr.top}px`,opacity:1,transform:'scale(1)'},
        {left:`${cr.left+20}px`,top:`${cr.top+13}px`,opacity:0,transform:'scale(0)'}
      ],{duration:600,easing:'cubic-bezier(0.25,0.46,0.45,0.94)'}).onfinish=()=>dot.remove();
    }
  }
}

function updateCartBadge() {
  const el = document.getElementById('cart-badge');
  if (el) { el.textContent = cartCount(); el.style.animation='none'; requestAnimationFrame(()=>el.style.animation=''); }
}

function removeFromCart(pid) {
  delete state.cart[pid]; updateCartBadge(); renderCart(); playSound('remove');
}
function changeQty(pid, delta) {
  const cur = state.cart[pid]||0, next = cur+delta;
  if (next<=0) removeFromCart(pid);
  else { state.cart[pid]=next; updateCartBadge(); renderCart(); playSound('click'); }
}
function clearCart() {
  state.cart={}; updateCartBadge(); renderCart(); playSound('remove'); toast('Cart cleared','warn');
}

function renderCart() {
  const list   = document.getElementById('cart-items-list');
  const footer = document.getElementById('cart-footer');
  const items  = Object.entries(state.cart);
  if (items.length===0) {
    list.innerHTML = `<div class="cart-empty"><span class="cart-empty-icon">🛒</span><span style="font-size:15px;font-weight:600">Your cart is empty</span><span style="font-size:13px">Add some amazing products!</span></div>`;
    footer.style.display='none'; return;
  }
  footer.style.display='block';
  list.innerHTML = items.map(([pid,qty])=>{
    const p = DB.products.find(p=>p.id==pid); if(!p) return '';
    const price = effectivePrice(p);
    return `
    <div class="cart-item">
      <img class="cart-item-img" src="${productImgSrc(p)}" alt="${p.name}">
      <div class="cart-item-info">
        <div class="cart-item-name">${p.name}</div>
        <div class="cart-item-price">$${(price*qty).toFixed(2)}</div>
        <div class="qty-controls">
          <button class="qty-btn" onclick="changeQty(${p.id},-1)">−</button>
          <span class="qty-num">${qty}</span>
          <button class="qty-btn" onclick="changeQty(${p.id},1)">+</button>
        </div>
      </div>
      <button class="cart-item-del" onclick="removeFromCart(${p.id})">✕</button>
    </div>`;
  }).join('');

  const sub = cartTotal(), tax = sub*0.08, grand = sub+tax;
  document.getElementById('cart-summary').innerHTML = `
    <div class="cart-summary-row"><span>Subtotal</span><span class="s-val">$${sub.toFixed(2)}</span></div>
    <div class="cart-summary-row"><span>Tax (8%)</span><span class="s-val">$${tax.toFixed(2)}</span></div>
    <div class="cart-summary-row"><span>Grand Total</span><span class="s-val">$${grand.toFixed(2)}</span></div>`;
}

function openCart()  { document.getElementById('cart-sidebar').classList.add('open'); document.getElementById('cart-overlay').classList.add('open'); renderCart(); playSound('click'); }
function closeCart() { document.getElementById('cart-sidebar').classList.remove('open'); document.getElementById('cart-overlay').classList.remove('open'); }

/* =====================================================
   CHECKOUT & RECEIPT
===================================================== */
function openCheckout() {
  if (cartCount()===0) { toast('Your cart is empty!','error'); return; }
  closeCart();
  // Pre-fill user info
  if (state.user) {
    document.getElementById('ch-name').value  = state.user.name||'';
    document.getElementById('ch-email').value = state.user.email||'';
    document.getElementById('ch-phone').value = state.user.phone||'';
  } else {
    document.getElementById('ch-name').value  = '';
    document.getElementById('ch-email').value = '';
    document.getElementById('ch-phone').value = '';
  }
  // Build checkout summary
  const items = Object.entries(state.cart).map(([pid,qty])=>{
    const p=DB.products.find(p=>p.id==pid); if(!p) return '';
    const ep=effectivePrice(p);
    return `<div style="display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid var(--border);font-size:13px"><span>${p.name} ×${qty}</span><span style="font-weight:700;color:var(--accent)">$${(ep*qty).toFixed(2)}</span></div>`;
  }).join('');
  document.getElementById('checkout-items-list').innerHTML = items;
  const sub=cartTotal(),tax=sub*0.08,grand=sub+tax;
  document.getElementById('checkout-totals').innerHTML=`
    <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px"><span style="color:var(--text2)">Subtotal</span><span>$${sub.toFixed(2)}</span></div>
    <div style="display:flex;justify-content:space-between;font-size:14px;margin-bottom:6px"><span style="color:var(--text2)">Tax (8%)</span><span>$${tax.toFixed(2)}</span></div>
    <div style="display:flex;justify-content:space-between;font-size:17px;font-weight:800;color:var(--accent);border-top:1px solid var(--border);padding-top:10px;margin-top:6px"><span>Grand Total</span><span>$${grand.toFixed(2)}</span></div>`;
  openModal('checkout-modal');
}

function confirmOrder() {
  const name = document.getElementById('ch-name').value.trim();
  const email = document.getElementById('ch-email').value.trim();
  const phone = document.getElementById('ch-phone').value.trim();
  const addr  = document.getElementById('ch-addr').value.trim();
  const payment = document.getElementById('ch-payment').value;
  if (!name||!email||!addr) { toast('Please fill all delivery details!','error'); return; }

  const items=[]; let sub=0;
  Object.entries(state.cart).forEach(([pid,qty])=>{
    const p=DB.products.find(p=>p.id==pid); if(!p) return;
    const ep=effectivePrice(p);
    items.push({name:p.name,qty,price:ep,subtotal:ep*qty});
    sub+=ep*qty;
  });
  const tax=sub*0.08, grand=sub+tax;
  const orderId = DB.nextId++;
  const order = {
    id:orderId, cust:name, email, phone, addr, payment,
    items, subtotal:sub, tax, grand,
    date: new Date().toLocaleString(), status:'Confirmed'
  };
  DB.orders.push(order);
  const mo = new Date().toLocaleString('default',{month:'short'});
  DB.monthly_sales[mo] = (DB.monthly_sales[mo]||0)+grand;
  if (state.user) { const c=DB.customers.find(c=>c.id===state.user.id); if(c) c.orders++; }
  state.cart={}; updateCartBadge(); renderCart();
  closeModal('checkout-modal');
  buildReceipt(order);
  openModal('receipt-modal');
  playSound('order');
}

function buildReceipt(o) {
  document.getElementById('receipt-meta').innerHTML = `
    <strong>Order #${String(o.id).padStart(5,'0')}</strong> &nbsp;·&nbsp; ${o.date}<br>
    Customer: <strong>${o.cust}</strong> &nbsp;|&nbsp; ${o.email}`;
  document.getElementById('receipt-items').innerHTML = o.items.map(it=>`
    <tr>
      <td>${it.name}</td><td>${it.qty}</td>
      <td>$${it.price.toFixed(2)}</td>
      <td style="font-weight:700;color:var(--accent)">$${it.subtotal.toFixed(2)}</td>
    </tr>`).join('');
  document.getElementById('receipt-totals').innerHTML=`
    <div class="rtotal-row"><span>Subtotal</span><span>$${o.subtotal.toFixed(2)}</span></div>
    <div class="rtotal-row"><span>Tax (8%)</span><span>$${o.tax.toFixed(2)}</span></div>
    <div class="rtotal-row grand"><span>Grand Total</span><span>$${o.grand.toFixed(2)}</span></div>
    <p style="font-size:12px;color:var(--text2);margin-top:10px;text-align:center">Payment: ${o.payment==='card'?'💳 Card':o.payment==='cash'?'💵 Cash on Delivery':'📱 Digital Wallet'}</p>`;
}

/* =====================================================
   SUBSCRIBE
===================================================== */
function doSubscribe() {
  const email = document.getElementById('sub-email').value.trim();
  if (!email||!email.includes('@')) { toast('Enter a valid email!','error'); return; }
  if (DB.subscribers.find(s=>s.email===email)) { toast('Already subscribed!','warn'); return; }
  DB.subscribers.push({id:DB.nextId++,email,joined:new Date().toISOString().slice(0,10)});
  document.getElementById('sub-email').value='';
  toast('Subscribed! 🎉 Welcome to the family!');
}

/* =====================================================
   ADMIN — TAB NAVIGATION
===================================================== */
function adminTab(tab, el) {
  state.adminCurrentTab = tab;
  document.querySelectorAll('.sidebar-link').forEach(l=>l.classList.remove('active'));
  if (el) el.classList.add('active');
  renderAdminTab(tab);
}

function renderAdminTab(tab) {
  const main = document.getElementById('admin-main');
  const renders = {
    dashboard: renderDashboard,
    products:  renderAdminProducts,
    orders:    renderAdminOrders,
    users:     renderAdminUsers,
    sales:     renderAdminSales,
    subscribers: renderAdminSubscribers,
  };
  main.innerHTML = '';
  if (renders[tab]) renders[tab](main);
}

/* ── DASHBOARD ── */
function renderDashboard(el) {
  const totalRev = Object.values(DB.monthly_sales).reduce((a,b)=>a+b,0);
  el.innerHTML = `
  <div class="admin-page-title">📊 Dashboard</div>
  <div class="stats-grid">
    ${[
      ['📦','Products',DB.products.length,'Active','badge-blue'],
      ['👥','Customers',DB.customers.length,'Registered','badge-green'],
      ['📋','Orders',DB.orders.length,'All Time','badge-amber'],
      ['💰','Revenue','$'+Math.round(totalRev/1000)+'k','2025','badge-green'],
      ['📧','Subscribers',DB.subscribers.length,'Email List','badge-blue'],
    ].map(([icon,label,val,trend,badge],i)=>`
    <div class="stat-card" style="animation-delay:${i*0.07}s">
      <div class="stat-icon">${icon}</div>
      <div class="stat-label">${label}</div>
      <div class="stat-val">${val}</div>
      <div class="stat-trend">▲ ${trend}</div>
    </div>`).join('')}
  </div>
  <div class="form-panel">
    <h3>📈 Monthly Sales</h3>
    <div class="chart-area">
      <div class="chart-bars">
        ${(()=>{const mx=Math.max(...Object.values(DB.monthly_sales)); return Object.entries(DB.monthly_sales).map(([mo,v])=>`
        <div class="bar-col">
          <div class="bar-amt">$${(v/1000).toFixed(1)}k</div>
          <div class="bar-fill" style="height:${Math.round((v/mx)*140)}px" title="${mo}: $${v.toLocaleString()}"></div>
          <div class="bar-lbl">${mo}</div>
        </div>`).join(''); })()}
      </div>
    </div>
  </div>
  <div class="admin-table-wrap">
    <div class="table-header"><span>🕐 Recent Orders</span></div>
    <table class="at">
      <thead><tr><th>Order#</th><th>Customer</th><th>Total</th><th>Date</th><th>Status</th></tr></thead>
      <tbody>
        ${DB.orders.length===0?`<tr><td colspan="5" style="text-align:center;padding:24px;color:var(--text3)">No orders yet</td></tr>`:
          [...DB.orders].reverse().slice(0,6).map(o=>`
          <tr>
            <td>#${String(o.id).padStart(5,'0')}</td>
            <td>${o.cust}</td>
            <td style="color:var(--green);font-weight:700">$${o.grand.toFixed(2)}</td>
            <td>${o.date}</td>
            <td><span class="badge badge-green">${o.status}</span></td>
          </tr>`).join('')}
      </tbody>
    </table>
  </div>`;
}

/* ── PRODUCTS ── */
function renderAdminProducts(el) {
  el.innerHTML = `
  <div class="admin-page-title">📦 Products Management</div>
  <div class="form-panel">
    <h3>➕ Add New Product</h3>
    <div class="fgrid">
      <div class="form-group"><label>Name</label><input class="form-input" id="ap-name" placeholder="Product Name"></div>
      <div class="form-group"><label>Price ($)</label><input class="form-input" type="number" id="ap-price" step="0.01" placeholder="49.99"></div>
      <div class="form-group"><label>Sale Price ($)</label><input class="form-input" type="number" id="ap-sale" step="0.01" placeholder="0 = none"></div>
      <div class="form-group"><label>Stock</label><input class="form-input" type="number" id="ap-stock" placeholder="Qty"></div>
    </div>
    <div class="fgrid">
      <div class="form-group"><label>Category</label>
        <select class="form-input form-select" id="ap-cat">
          <option>Electronics</option><option>Footwear</option><option>Clothing</option>
          <option>Accessories</option><option>Fitness</option><option>Home & Living</option><option>Beauty</option>
        </select>
      </div>
      <div class="form-group"><label>Image URL</label><input class="form-input" id="ap-img" placeholder="https://..."></div>
      <div class="form-group" style="grid-column:span 2"><label>Description</label><input class="form-input" id="ap-desc" placeholder="Short description"></div>
    </div>
    <div class="checkbox-row"><input type="checkbox" id="ap-sale-chk"><label for="ap-sale-chk">🔥 Mark as On Sale</label></div>
    <button class="btn btn-primary ripple-host" onclick="adminAddProduct();addRipple(event)">Add Product</button>
  </div>
  <div class="admin-table-wrap">
    <div class="table-header"><span>All Products (${DB.products.length})</span></div>
    <table class="at">
      <thead><tr><th>Img</th><th>Name</th><th>Price</th><th>Sale</th><th>Stock</th><th>Category</th><th>Actions</th></tr></thead>
      <tbody>
        ${DB.products.map(p=>`
        <tr>
          <td><img class="img-thumb" src="${productImgSrc(p)}" alt=""></td>
          <td style="font-weight:600">${p.name}</td>
          <td>$${p.price.toFixed(2)}</td>
          <td>${p.on_sale&&p.sale_price>0?`<span class="badge badge-amber">🔥 $${p.sale_price.toFixed(2)}</span>`:'—'}</td>
          <td>${p.stock}</td>
          <td><span class="badge badge-blue">${p.category}</span></td>
          <td style="display:flex;gap:6px;flex-wrap:wrap;padding-top:10px">
            <button class="btn btn-blue btn-sm" onclick="openEditProductAdmin(${p.id})">✏️ Edit</button>
            <button class="btn btn-danger btn-sm" onclick="adminDeleteProduct(${p.id})">🗑️</button>
          </td>
        </tr>`).join('')}
      </tbody>
    </table>
  </div>`;
}

function adminAddProduct() {
  const name=document.getElementById('ap-name')?.value.trim();
  const price=parseFloat(document.getElementById('ap-price')?.value||0);
  const sale=parseFloat(document.getElementById('ap-sale')?.value||0);
  const stock=parseInt(document.getElementById('ap-stock')?.value||0);
  const cat=document.getElementById('ap-cat')?.value;
  const img=document.getElementById('ap-img')?.value.trim();
  const desc=document.getElementById('ap-desc')?.value.trim();
  const on_sale=document.getElementById('ap-sale-chk')?.checked;
  if (!name||!price) { toast('Name and price are required!','error'); return; }
  DB.products.push({id:DB.nextId++,name,price,sale_price:sale,on_sale,stock,category:cat,img,desc});
  toast('✅ Product added!');
  buildFilterChips(); renderProducts();
  renderAdminTab('products');
}

function adminDeleteProduct(id) {
  if (!confirm(`Delete product #${id}?`)) return;
  DB.products = DB.products.filter(p=>p.id!==id);
  toast('🗑️ Product deleted!');
  buildFilterChips(); renderProducts();
  renderAdminTab('products');
}

function openEditProductAdmin(id) {
  const p=DB.products.find(p=>p.id===id); if(!p) return;
  document.getElementById('prod-modal-title').textContent='✏️ Edit Product';
  document.getElementById('prod-edit-id').value=id;
  document.getElementById('prod-name').value=p.name;
  document.getElementById('prod-price').value=p.price;
  document.getElementById('prod-sale').value=p.sale_price;
  document.getElementById('prod-stock').value=p.stock;
  document.getElementById('prod-cat').value=p.category;
  document.getElementById('prod-img').value=p.img||'';
  document.getElementById('prod-desc').value=p.desc||'';
  document.getElementById('prod-on-sale').checked=p.on_sale;
  document.getElementById('prod-save-btn').textContent='Save Changes';
  openModal('product-modal');
}

function saveProduct() {
  const editId=parseInt(document.getElementById('prod-edit-id').value);
  const name=document.getElementById('prod-name').value.trim();
  const price=parseFloat(document.getElementById('prod-price').value||0);
  const sale=parseFloat(document.getElementById('prod-sale').value||0);
  const stock=parseInt(document.getElementById('prod-stock').value||0);
  const cat=document.getElementById('prod-cat').value;
  const img=document.getElementById('prod-img').value.trim();
  const desc=document.getElementById('prod-desc').value.trim();
  const on_sale=document.getElementById('prod-on-sale').checked;
  if (!name||!price) { toast('Name and price are required!','error'); return; }
  if (editId) {
    const p=DB.products.find(p=>p.id===editId);
    if(p){ Object.assign(p,{name,price,sale_price:sale,on_sale,stock,category:cat,img:img||p.img,desc}); }
    toast('✅ Product updated!');
  } else {
    DB.products.push({id:DB.nextId++,name,price,sale_price:sale,on_sale,stock,category:cat,img,desc});
    toast('✅ Product added!');
  }
  closeModal('product-modal');
  buildFilterChips(); renderProducts();
  renderAdminTab('products');
}

/* ── ORDERS ── */
function renderAdminOrders(el) {
  el.innerHTML = `
  <div class="admin-page-title">📋 All Orders</div>
  <div class="admin-table-wrap">
    <div class="table-header"><span>Orders (${DB.orders.length})</span></div>
    <table class="at">
      <thead><tr><th>Order#</th><th>Customer</th><th>Items</th><th>Total</th><th>Payment</th><th>Date</th><th>Status</th></tr></thead>
      <tbody>
        ${DB.orders.length===0?`<tr><td colspan="7" style="text-align:center;padding:28px;color:var(--text3)">No orders placed yet</td></tr>`:
          [...DB.orders].reverse().map(o=>`
          <tr>
            <td>#${String(o.id).padStart(5,'0')}</td>
            <td><div style="font-weight:600">${o.cust}</div><div style="font-size:11px;color:var(--text3)">${o.email}</div></td>
            <td>${o.items.map(i=>`<div style="font-size:11px">${i.name} ×${i.qty}</div>`).join('')}</td>
            <td style="color:var(--green);font-weight:800">$${o.grand.toFixed(2)}</td>
            <td>${o.payment==='card'?'💳':o.payment==='cash'?'💵':'📱'} ${o.payment}</td>
            <td style="font-size:12px">${o.date}</td>
            <td><span class="badge badge-green">${o.status}</span></td>
          </tr>`).join('')}
      </tbody>
    </table>
  </div>`;
}

/* ── USERS/CUSTOMERS ── */
function renderAdminUsers(el) {
  el.innerHTML = `
  <div class="admin-page-title">👥 Customers</div>
  <div class="stats-grid" style="max-width:500px">
    <div class="stat-card"><div class="stat-icon">👥</div><div class="stat-label">Total</div><div class="stat-val">${DB.customers.length}</div></div>
    <div class="stat-card"><div class="stat-icon">✅</div><div class="stat-label">Active</div><div class="stat-val">${DB.customers.filter(c=>c.status==='Active').length}</div></div>
    <div class="stat-card"><div class="stat-icon">📋</div><div class="stat-label">Orders</div><div class="stat-val">${DB.customers.reduce((a,c)=>a+c.orders,0)}</div></div>
  </div>
  <div class="admin-table-wrap">
    <div class="table-header"><span>All Customers</span></div>
    <table class="at">
      <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Phone</th><th>Orders</th><th>Joined</th><th>Status</th></tr></thead>
      <tbody>
        ${DB.customers.map(c=>`
        <tr>
          <td>${c.id}</td>
          <td style="font-weight:600">${c.name}</td>
          <td>${c.email}</td>
          <td>${c.phone||'—'}</td>
          <td>${c.orders}</td>
          <td>${c.joined}</td>
          <td><span class="badge badge-green">${c.status}</span></td>
        </tr>`).join('')}
      </tbody>
    </table>
  </div>`;
}

/* ── SALES ── */
function renderAdminSales(el) {
  const ms=DB.monthly_sales, ts=Object.values(ms).reduce((a,b)=>a+b,0);
  const mx=Math.max(...Object.values(ms)), mn=Math.min(...Object.values(ms));
  const best=Object.keys(ms).find(k=>ms[k]===mx), worst=Object.keys(ms).find(k=>ms[k]===mn), avg=ts/12;
  el.innerHTML=`
  <div class="admin-page-title">💰 Sales Analytics</div>
  <div class="stats-grid">
    <div class="stat-card"><div class="stat-icon">💰</div><div class="stat-label">Total Revenue</div><div class="stat-val">$${ts.toLocaleString()}</div><div class="stat-trend">▲ 2025</div></div>
    <div class="stat-card"><div class="stat-icon">🏆</div><div class="stat-label">Best Month</div><div class="stat-val">${best}</div><div class="stat-trend">$${mx.toLocaleString()}</div></div>
    <div class="stat-card"><div class="stat-icon">📊</div><div class="stat-label">Avg Monthly</div><div class="stat-val">$${Math.round(avg).toLocaleString()}</div></div>
    <div class="stat-card"><div class="stat-icon">📉</div><div class="stat-label">Lowest Month</div><div class="stat-val">${worst}</div><div class="stat-trend" style="color:var(--red)">$${mn.toLocaleString()}</div></div>
  </div>
  <div class="form-panel">
    <h3>📊 Monthly Revenue</h3>
    <div class="chart-area"><div class="chart-bars">
      ${Object.entries(ms).map(([mo,v])=>`
      <div class="bar-col">
        <div class="bar-amt">$${(v/1000).toFixed(1)}k</div>
        <div class="bar-fill" style="height:${Math.round((v/mx)*155)}px"></div>
        <div class="bar-lbl">${mo}</div>
      </div>`).join('')}
    </div></div>
  </div>
  <div class="admin-table-wrap">
    <div class="table-header"><span>Monthly Breakdown</span></div>
    <table class="at">
      <thead><tr><th>Month</th><th>Revenue</th><th>vs Average</th></tr></thead>
      <tbody>
        ${Object.entries(ms).map(([mo,v])=>{ const d=v-avg,pos=d>=0; return `
        <tr>
          <td style="font-weight:600">${mo}</td>
          <td>$${v.toLocaleString()}</td>
          <td style="color:${pos?'var(--green)':'var(--red)'};font-weight:700">${pos?'+':'-'}$${Math.abs(Math.round(d)).toLocaleString()}</td>
        </tr>`;}).join('')}
      </tbody>
    </table>
  </div>`;
}

/* ── SUBSCRIBERS ── */
function renderAdminSubscribers(el) {
  el.innerHTML=`
  <div class="admin-page-title">📧 Subscribers</div>
  <div class="stats-grid" style="max-width:360px">
    <div class="stat-card"><div class="stat-icon">📧</div><div class="stat-label">Total</div><div class="stat-val">${DB.subscribers.length}</div></div>
    <div class="stat-card"><div class="stat-icon">📅</div><div class="stat-label">This Month</div><div class="stat-val">${DB.subscribers.filter(s=>s.joined?.slice(0,7)===new Date().toISOString().slice(0,7)).length}</div></div>
  </div>
  <div class="admin-table-wrap">
    <div class="table-header"><span>Email List</span></div>
    <table class="at">
      <thead><tr><th>#</th><th>Email</th><th>Joined</th><th>Action</th></tr></thead>
      <tbody>
        ${DB.subscribers.length===0?`<tr><td colspan="4" style="text-align:center;padding:24px;color:var(--text3)">No subscribers yet</td></tr>`:
          DB.subscribers.map(s=>`
          <tr>
            <td>${s.id}</td><td>${s.email}</td><td>${s.joined}</td>
            <td><button class="btn btn-danger btn-sm" onclick="removeSub(${s.id})">Remove</button></td>
          </tr>`).join('')}
      </tbody>
    </table>
  </div>`;
}
function removeSub(id) {
  if (!confirm('Remove subscriber?')) return;
  DB.subscribers = DB.subscribers.filter(s=>s.id!==id);
  toast('Subscriber removed','warn');
  renderAdminTab('subscribers');
}

/* =====================================================
   SCROLL HELPER
===================================================== */
function scrollToShop() {
  document.getElementById('shop-section')?.scrollIntoView({behavior:'smooth'});
}

/* =====================================================
   INIT
===================================================== */
function init() {
  buildFilterChips();
  renderProducts();
  updateNavUser();
  updateCartBadge();
  // Admin nav button — only show when admin
  document.getElementById('admin-nav-btn').style.display = 'none';
}

init();
