<?php $user = auth_user(); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e((App\Core\Container::get('config')['app']['name'] ?? 'TDM Pro')) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<?php if ($user): ?>
<div class="app-shell">
  <?php require BASE_PATH . '/app/views/partials/sidebar.php'; ?>
  <main class="app-main">
    <?php require BASE_PATH . '/app/views/partials/topbar.php'; ?>
    <div class="container-fluid p-4">
      <?php if ($msg = flash('success')): ?><div class="alert alert-success"><?= e($msg) ?></div><?php endif; ?>
      <?php if ($msg = flash('error')): ?><div class="alert alert-danger"><?= e($msg) ?></div><?php endif; ?>
      <?php require $viewFile; ?>
    </div>
  </main>
</div>
<?php else: ?>
<div class="public-shell"><?php require $viewFile; ?></div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="/assets/js/app.js"></script>
</body>
</html>
