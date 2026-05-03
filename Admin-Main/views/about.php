<?php
include 'includes/header.php';
include 'includes/navbar.php';

// Load company info if available
$company_info = [];
$company_file = __DIR__ . '/../about/company_info.json';
if (file_exists($company_file)) {
    $company_info = json_decode(file_get_contents($company_file), true);
}
?>

<div class="container">
    <div class="page-header">
        <h1>About <?php echo htmlspecialchars($company_info['company_name'] ?? 'Us'); ?></h1>
        <p>Learn more about our company and mission</p>
    </div>

    <div class="about-content">
        <div class="about-section">
            <h2>Our Story</h2>
            <p>Welcome to <?php echo htmlspecialchars($company_info['company_name'] ?? 'our e-commerce platform'); ?>. We are dedicated to providing high-quality products and exceptional customer service.</p>
            <?php if (isset($company_info['founded'])): ?>
                <p><strong>Founded:</strong> <?php echo htmlspecialchars($company_info['founded']); ?></p>
            <?php endif; ?>
        </div>

        <div class="about-section">
            <h2>Our Mission</h2>
            <p><?php echo htmlspecialchars($company_info['mission'] ?? 'To deliver innovative solutions and create value for our customers through technology and dedication.'); ?></p>
        </div>

        <?php if (isset($company_info['vision'])): ?>
        <div class="about-section">
            <h2>Our Vision</h2>
            <p><?php echo htmlspecialchars($company_info['vision']); ?></p>
        </div>
        <?php endif; ?>

        <?php if (isset($company_info['values']) && is_array($company_info['values'])): ?>
        <div class="about-section">
            <h2>Our Values</h2>
            <ul>
                <?php foreach ($company_info['values'] as $value): ?>
                    <li><?php echo htmlspecialchars($value); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <div class="about-section">
            <h2>Contact Information</h2>
            <?php if (isset($company_info['contact'])): ?>
                <?php if (isset($company_info['contact']['email'])): ?>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($company_info['contact']['email']); ?></p>
                <?php endif; ?>
                <?php if (isset($company_info['contact']['phone'])): ?>
                    <p><strong>Phone:</strong> <?php echo htmlspecialchars($company_info['contact']['phone']); ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p>For inquiries, please reach out to our support team.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.about-content {
    max-width: 800px;
    margin: 2rem auto;
}

.about-section {
    background: white;
    padding: 2rem;
    margin-bottom: 2rem;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.about-section h2 {
    color: #333;
    margin-bottom: 1rem;
    border-bottom: 2px solid #007bff;
    padding-bottom: 0.5rem;
}

.about-section p {
    color: #666;
    line-height: 1.6;
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
}

.page-header h1 {
    color: #333;
    margin-bottom: 0.5rem;
}

.about-section ul {
    list-style: none;
    padding: 0;
}

.about-section ul li {
    padding: 0.5rem 0;
    padding-left: 1.5rem;
    position: relative;
    color: #666;
}

.about-section ul li:before {
    content: '✓';
    position: absolute;
    left: 0;
    color: #28a745;
    font-weight: bold;
}
</style>

<?php
include 'includes/footer.php';
?>