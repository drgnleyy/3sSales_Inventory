<?php
session_start();
include 'config/database.php';

$error = '';

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = MD5($_POST['password']);

    $query = "SELECT * FROM users 
              WHERE username='$username' 
              AND password='$password'";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {

        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header("Location: dashboard.php");
        exit();

    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #0f172a, #172554, #1d4ed8);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f172a;
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 34px;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.28);
        }

        .login-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .login-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 14px;
            border-radius: 16px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .login-header h2 {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 6px;
        }

        .login-header p {
            font-size: 14px;
            color: #64748b;
        }

        .error-message {
            background: #fee2e2;
            color: #991b1b;
            border-radius: 10px;
            padding: 12px;
            font-size: 14px;
            margin-bottom: 18px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 7px;
            color: #334155;
            font-size: 14px;
            font-weight: 700;
        }

        .form-group input {
            width: 100%;
            height: 46px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0 14px;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
        }

        .form-group input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16);
        }

        .login-btn {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 10px;
            background: #2563eb;
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 6px;
        }

        .login-btn:hover {
            background: #1d4ed8;
        }

        .login-footer {
            margin-top: 20px;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="login-header">
            <div class="login-icon">🔐</div>
            <h2>Login</h2>
            <p>Sign in to access the sales system</p>
        </div>

        <?php if (!empty($error)) { ?>
            <div class="error-message">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <form action="" method="POST">

            <div class="form-group">
                <label>Username</label>
                <input
                    type="text"
                    name="username"
                    placeholder="Enter your username"
                    required
                >
            </div>

            <div class="form-group">
                <label>Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >
            </div>

            <button type="submit" name="login" class="login-btn">
                Login
            </button>

        </form>

        <div class="login-footer">
            Sales System Admin Panel
        </div>

    </div>

</div>

</body>
</html>