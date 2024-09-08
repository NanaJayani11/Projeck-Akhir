<?php
require 'function.php';

// Inisialisasi variabel pesan error
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST['email']) || empty($_POST['password'])) {
    // Jika email atau password kosong, setel pesan error
    $error = 'Harap masukkan email dan password.';
  } else {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Lindungi dari SQL injection
    $email = mysqli_real_escape_string($conn, $email);
    $password = mysqli_real_escape_string($conn, $password);

    // Query ke database
    $cekdatabase = mysqli_query($conn, "SELECT * FROM login WHERE email='$email' AND password='$password'");
    $hitung = mysqli_num_rows($cekdatabase);

    if ($hitung > 0) {
      $_SESSION['log'] = 'True';
      header('Location: index.php');
      exit();
    } else {
      $error = 'Email atau password tidak valid.';
    }
  }
}

// Redirect jika sudah login
if (isset($_SESSION['log']) && $_SESSION['log'] === 'True') {
  header('Location: index.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css?family=Karla:400,700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.materialdesignicons.com/4.8.95/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>
  <main class="d-flex align-items-center min-vh-100 py-3 py-md-0">
    <div class="container">
      <div class="card login-card">
        <div class="row no-gutters">
          <div class="col-md-5">
            <img src="assets/images/jam.jpg" alt="login" class="login-card-img">
          </div>
          <div class="col-md-7">
            <div class="card-body">
              <div class="brand-wrapper">
                <img src="assets/images/logo.svg" alt="logo" class="logo">
              </div>
              <p class="login-card-description">Sign into your account</p>

              <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                  <?php echo htmlspecialchars($error); ?>
                </div>
              <?php endif; ?>

              <form method="POST">
                <div class="form-group">
                  <label class="small mb-1" for="inputEmailAddress">Email</label>
                  <input type="email" name="email" id="inputEmailAddress" class="form-control" placeholder="Enter email address">
                </div>
                <div class="form-group">
                  <label class="small mb-1" for="inputPassword">Password</label>
                  <input class="form-control py-4" id="inputPassword" type="password" name="password" placeholder="Enter password">
                </div>
                <div class="form-group d flex align-items-center justify-content-between mt-4 mb-0">
                  <button type="submit" class="btn btn-info" name="login">Login</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>

</html>