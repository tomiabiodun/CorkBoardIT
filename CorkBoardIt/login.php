
<?php
// Start the session
session_start();

// Database connection variables
$host = 'localhost';       // Your database host
$db = 'CorkBoardIt';     // Your database name
$user = 'root';    // Your database user
$pass = 'abcd1234';// Your database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize error message
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form data
    $email = $_POST['email'];
    $pin = $_POST['pin'];

    // Basic input validation
    if (empty($email) || empty($pin)) {
        $error = 'Email and PIN are required.';
    } else {
        // Prepare the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name FROM users WHERE email = ? AND pin = ?");
        $stmt->bind_param("ss", $email, $pin);

        // Execute the query
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            // Login successful
            $stmt->bind_result($user_id, $first_name, $last_name);
            $stmt->fetch();

            // Store user information in session
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['last_name'] = $last_name;


            // Redirect to a protected page (e.g., dashboard.php)
            header("Location: dashboard.php");
            exit;
        } else {
            // Invalid email or pin
            $error = 'Invalid email or PIN. Please try again.';
        }

        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://unpkg.com/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <!-- Login 13 - Bootstrap Brain Component -->
<section class="bg-light py-3 py-md-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
          <div class="card border border-light-subtle rounded-3 shadow-sm">
            <div class="card-body p-3 p-md-4 p-xl-5">
              <div class="text-center mb-3">
                <a href="#!">
                  <img src="assets/image/logo.png" alt="CorkBoardIt Logo" width="210" height="130">
                </a>
              </div>
              <h2 class="fs-6 fw-normal text-center text-secondary mb-4">Sign in to your account</h2>
              <form action="login.php" method="POST">
                <div class="row gy-2 overflow-hidden">
                  <div class="col-12">
                    <div class="form-floating mb-3">
                      <input type="email" class="form-control" name="email" id="email" placeholder="name@example.com" required>
                      <label for="email" class="form-label">Email</label>
                    </div>
                  </div>
                  <div class="col-12">
                    <div class="form-floating mb-3">
                      <input type="password" class="form-control" name="pin" id="pin" value="" placeholder="Pin" required>
                      <label for="pin" class="form-label">Pin</label>
                    </div>
                    <?php if (!empty($error)): ?>
                      <p style="color: red;"><?php echo $error; ?></p>
                    <?php endif; ?>
                  </div>
                  <div class="col-12">
                    <div class="d-grid my-3">
                      <button class="btn btn-primary btn-lg" type="submit">Log in</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
    
</body>
</html>