<?php
include("../Includes/db.php");
session_name('agro_farmer');
session_start();
include("../Functions/functions.php");
?>


<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>Farmer Login - AgroCraft</title>
	<script src="https://kit.fontawesome.com/c587fc1763.js" crossorigin="anonymous"></script>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Poppins', sans-serif;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			padding: 20px;
		}

		.login-container {
			background: white;
			border-radius: 20px;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
			overflow: hidden;
			max-width: 1000px;
			width: 100%;
			display: flex;
		}

		.login-left {
			flex: 1;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			padding: 60px 40px;
			display: flex;
			flex-direction: column;
			justify-content: center;
			align-items: center;
			color: white;
			text-align: center;
		}

		.login-left h2 {
			font-size: 2.5rem;
			margin-bottom: 20px;
			font-weight: 600;
		}

		.login-left p {
			font-size: 1.1rem;
			opacity: 0.9;
			line-height: 1.6;
		}

		.login-left i {
			font-size: 5rem;
			margin-bottom: 30px;
			opacity: 0.9;
		}

		.login-right {
			flex: 1;
			padding: 60px 50px;
		}

		.login-header {
			margin-bottom: 40px;
		}

		.login-header h3 {
			font-size: 2rem;
			color: #333;
			font-weight: 600;
			margin-bottom: 10px;
		}

		.login-header p {
			color: #666;
			font-size: 0.95rem;
		}

		.form-group {
			margin-bottom: 25px;
		}

		.form-group label {
			font-weight: 500;
			color: #333;
			margin-bottom: 8px;
			display: block;
		}

		.form-control {
			border: 2px solid #e0e0e0;
			border-radius: 10px;
			padding: 12px 15px;
			font-size: 1rem;
			transition: all 0.3s;
		}

		.form-control:focus {
			border-color: #667eea;
			box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
		}

		.input-group-text {
			background: #f8f9fa;
			border: 2px solid #e0e0e0;
			border-right: none;
			border-radius: 10px 0 0 10px;
			color: #667eea;
		}

		.input-group .form-control {
			border-left: none;
			border-radius: 0 10px 10px 0;
		}

		.btn-login {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			border: none;
			border-radius: 10px;
			padding: 12px 40px;
			font-size: 1.1rem;
			font-weight: 600;
			color: white;
			width: 100%;
			transition: all 0.3s;
			box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
		}

		.btn-login:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
		}

		.login-links {
			margin-top: 25px;
			text-align: center;
		}

		.login-links a {
			color: #667eea;
			text-decoration: none;
			font-weight: 500;
			transition: all 0.3s;
		}

		.login-links a:hover {
			color: #764ba2;
			text-decoration: underline;
		}

		@media (max-width: 768px) {
			.login-container {
				flex-direction: column;
			}

			.login-left {
				padding: 40px 30px;
			}

			.login-right {
				padding: 40px 30px;
			}
		}

	</style>
</head>

<body>
	<div class="login-container">
		<div class="login-left">
			<i class="fas fa-seedling"></i>
			<h2>Welcome Back, Farmer!</h2>
			<p>Connect with buyers directly and grow your business. Manage your products, track orders, and expand your reach.</p>
		</div>
		<div class="login-right">
			<div class="login-header">
				<h3>Farmer Login</h3>
				<p>Sign in to your account to continue</p>
			</div>
			<form name="my-form" action="FarmerLogin.php" method="post">
				<div class="form-group">
					<label for="phone_number"><i class="fas fa-phone-alt mr-2"></i>Phone Number</label>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-phone"></i></span>
						</div>
						<input type="text" id="phone_number" class="form-control" name="phonenumber" placeholder="Enter your phone number" required>
					</div>
				</div>

				<div class="form-group">
					<label for="p1"><i class="fas fa-lock mr-2"></i>Password</label>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fas fa-lock"></i></span>
						</div>
						<input id="p1" class="form-control" type="password" name="password" placeholder="Enter your password" required>
					</div>
				</div>

				<button type="submit" class="btn btn-login" name="login" value="Login">
					Login <i class="fas fa-arrow-right ml-2"></i>
				</button>

				<div class="login-links">
					<p><a href="FarmerForgotPassword.php">Forgot your password?</a></p>
					<p>Don't have an account? <a href="FarmerRegister.php">Create New Account</a></p>
				</div>
			</form>
		</div>
	</div>
</body>

</html>

<?php
if (isset($_POST['login'])) {

	$phonenumber = mysqli_real_escape_string($con, $_POST['phonenumber']);
	$password = mysqli_real_escape_string($con, $_POST['password']);

	$ciphering = "AES-128-CTR";
	$iv_length = openssl_cipher_iv_length($ciphering);
	$options = 0;
	$encryption_iv = '2345678910111211';
	$encryption_key = "DE";
	$encryption = openssl_encrypt(
		$password,
		$ciphering,
		$encryption_key,
		$options,
		$encryption_iv
	);
	// echo $encryption;

	$query = "select * from farmerregistration where farmer_phone = '$phonenumber' and farmer_password = '$encryption'";
	echo $query;
	$run_query = mysqli_query($con, $query);
	$count_rows = mysqli_num_rows($run_query);
	if ($count_rows == 0) {
		echo "<script>alert('Please Enter Valid Details');</script>";
		echo "<script>window.open('FarmerLogin.php','_self')</script>";
	}
	while ($row = mysqli_fetch_array($run_query)) {
		$id = $row['farmer_id'];
	}

	$_SESSION['phonenumber'] = $phonenumber;
	echo "<script>window.open('../FarmerPortal/farmerHomepage.php','_self')</script>";
}

?>