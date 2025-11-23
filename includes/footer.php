<?php
// includes/footer.php
?>
</main>


<footer class="bg-dark text-light mt-5">
  <div class="container py-4">
    <div class="row align-items-center">
      <div class="col-md-4 mb-3 mb-md-0">
        <h5 class="mb-2"><i class="fas fa-hands-helping me-2"></i>TrueCare</h5>
        <p class="small text-muted mb-0">Connecting donors with orphanages in need.</p>
      </div>
      <div class="col-md-4 text-center mb-3 mb-md-0">
        <div class="d-flex justify-content-center gap-2">
          <a href="<?php echo abs_path('index.php'); ?>" class="btn btn-outline-light btn-sm px-4 rounded-pill">
            <i class="fas fa-home me-1"></i>Home
          </a>
          <a href="<?php echo abs_path('register.php'); ?>" class="btn btn-success btn-sm px-4 rounded-pill">
            <i class="fas fa-user-plus me-1"></i>Register
          </a>
        </div>
      </div>
      <div class="col-md-4 text-md-end">
        <p class="small text-muted mb-0">&copy; <?php echo date('Y'); ?> TrueCare. All rights reserved.</p>
      </div>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo abs_path('assets/js/app.js'); ?>"></script>
</body>
</html>
