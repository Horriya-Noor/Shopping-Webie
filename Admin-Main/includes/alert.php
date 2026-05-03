<?php if ($msg): ?>
<div class="alert <?= $msg_type ?>" id="main-alert">
  <span><?= $msg ?></span>
  <span class="close-x" onclick="document.getElementById('main-alert').remove()">×</span>
</div>
<?php endif; ?>
