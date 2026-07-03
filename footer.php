<!-- footer.php -->
<footer class="bg-light border-top mt-5 py-5">
    <div class="container">
        <div class="row text-center text-md-start">
            <div class="col-md-4 mb-4">
                <h4 class="text-gold fw-bold">Royal Man</h4>
                <p class="text-muted">Premium men's fashion from Bangladesh.<br>Elegance meets comfort.</p>
            </div>
            <div class="col-md-4 mb-4">
                <h6 class="fw-bold">Quick Links</h6>
                <ul class="list-unstyled text-muted">
                    <li><a href="about-us.php" class="text-decoration-none text-muted">About Us</a></li>
                    <li><a href="size-guide.php" class="text-decoration-none text-muted">Size Guide</a></li>
                    <li><a href="shipping.php" class="text-decoration-none text-muted">Shipping</a></li>
                    <li><a href="contact.php" class="text-decoration-none text-muted">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-flex flex-column align-items-center align-items-md-end">
                    <h6 class="fw-bold mb-2">Contact</h6>
                    <p class="text-muted mb-1">
                        <i class="bi bi-envelope me-2"></i>
                        <a href="mailto:info@royalman.com" class="text-decoration-none text-muted">info@royalman.com</a>
                    </p>
                    <p class="text-muted mb-2">
                        <i class="bi bi-telephone me-2"></i>
                        <a href="tel:+8801234567890" class="text-decoration-none text-muted">+880 1234 567890</a>
                    </p>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <div class="d-flex justify-content-center justify-content-md-end gap-2 mb-3">
                            <a href="login.php" class="btn btn-outline-dark btn-sm px-4">Login</a>
                            <a href="register.php" class="btn btn-dark btn-sm px-4">Sign Up</a>
                        </div>
                    <?php endif; ?>
                    <div class="d-flex justify-content-center justify-content-md-end gap-3 mt-3">
                        <a href="#" class="text-dark fs-4"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-dark fs-4"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-dark fs-4"><i class="bi bi-tiktok"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets\js\script.js"></script>
</body>
</html>