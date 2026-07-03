<?php include 'header.php'; ?>

<div class="container py-5">
    <h1 class="display-4 fw-bold text-gold mb-5">Shipping & Delivery</h1>
    
    <!-- Shipping Info -->
    <div class="row mb-5">
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-gold mb-3">
                        <i class="bi bi-truck me-2"></i>Domestic Shipping
                    </h5>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><strong>Standard Delivery:</strong> 3-5 business days</li>
                        <li class="mb-2"><strong>Express Delivery:</strong> 1-2 business days</li>
                        <li class="mb-2"><strong>Coverage:</strong> All over Bangladesh</li>
                        <li class="mb-2"><strong>Tracking:</strong> Real-time order tracking available</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-gold mb-3">
                        <i class="bi bi-globe me-2"></i>International Shipping
                    </h5>
                    <ul class="list-unstyled text-muted">
                        <li class="mb-2"><strong>Available to:</strong> Select countries</li>
                        <li class="mb-2"><strong>Delivery Time:</strong> 7-14 business days</li>
                        <li class="mb-2"><strong>Shipping Fee:</strong> Calculated at checkout</li>
                        <li class="mb-2"><strong>Customs:</strong> Buyer is responsible</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Shipping Costs -->
    <div class="mb-5">
        <h2 class="fw-bold text-gold mb-4">Shipping Costs</h2>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Order Type</th>
                        <th>Standard Shipping</th>
                        <th>Express Shipping</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="fw-bold">Under 1,000 BDT</td>
                        <td>Free</td>
                        <td>60 BDT</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">1,000 - 5,000 BDT</td>
                        <td>Free</td>
                        <td>80 BDT</td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Above 5,000 BDT</td>
                        <td>Free</td>
                        <td>100 BDT</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="text-muted text-sm mt-3">Free standard shipping on all domestic orders. Express delivery charges apply for priority handling.</p>
    </div>

    <!-- How It Works -->
    <div class="mb-5">
        <h2 class="fw-bold text-gold mb-4">How Shipping Works</h2>
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="text-center">
                    <div class="bg-gold rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; color: white; font-size: 24px;">
                        <i class="bi bi-bag-check"></i>
                    </div>
                    <h5 class="fw-bold">1. Place Order</h5>
                    <p class="text-muted small">Complete your purchase and confirm your delivery address.</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="text-center">
                    <div class="bg-gold rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; color: white; font-size: 24px;">
                        <i class="bi bi-box"></i>
                    </div>
                    <h5 class="fw-bold">2. Pack & Verify</h5>
                    <p class="text-muted small">We carefully pack your items and verify the order details.</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="text-center">
                    <div class="bg-gold rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; color: white; font-size: 24px;">
                        <i class="bi bi-send"></i>
                    </div>
                    <h5 class="fw-bold">3. Ship It</h5>
                    <p class="text-muted small">Your order is handed over to our shipping partner with tracking ID.</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="text-center">
                    <div class="bg-gold rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; color: white; font-size: 24px;">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <h5 class="fw-bold">4. Delivery</h5>
                    <p class="text-muted small">Your order arrives safely at your doorstep on time.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tracking -->
    <div class="mb-5">
        <h2 class="fw-bold text-gold mb-4">Order Tracking</h2>
        <div class="card border-0 bg-light">
            <div class="card-body">
                <p class="text-muted mb-3">Once your order ships, you'll receive an email with a tracking number. You can use this number to track your package in real-time on our partner's website.</p>
                <p class="text-muted mb-0">You can also track your order by logging into your Royal Man account and viewing your order history.</p>
            </div>
        </div>
    </div>

    <!-- Delivery Issues -->
    <div class="mb-5">
        <h2 class="fw-bold text-gold mb-4">What if There's an Issue?</h2>
        <ul class="list-unstyled text-muted">
            <li class="mb-3">
                <h5 class="fw-bold text-dark">Delayed Delivery</h5>
                <p>If your order is delayed beyond the promised delivery date, please contact us immediately. We'll investigate and provide updates.</p>
            </li>
            <li class="mb-3">
                <h5 class="fw-bold text-dark">Damaged Package</h5>
                <p>If your package arrives damaged, please refuse the delivery or contact us within 24 hours. We'll arrange a replacement or refund.</p>
            </li>
            <li class="mb-3">
                <h5 class="fw-bold text-dark">Wrong Item</h5>
                <p>If you receive the wrong item, contact us immediately with photo evidence. We'll send the correct item and arrange pickup of the wrong one.</p>
            </li>
            <li>
                <h5 class="fw-bold text-dark">Package Not Received</h5>
                <p>If you haven't received your package after the promised date, we'll file a claim with the shipping partner and investigate the issue.</p>
            </li>
        </ul>
    </div>

    <!-- Returns -->
    <div class="alert alert-info" role="alert">
        <h5 class="alert-heading">Easy Returns</h5>
        <p class="mb-0">We offer hassle-free returns within 7 days of delivery. For more information, visit our <a href="return-policy.php" class="alert-link">return policy page</a> or <a href="contact.php" class="alert-link">contact us</a>.</p>
    </div>
</div>

<?php include 'footer.php'; ?>
