<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
	<link rel="stylesheet" type="text/css" href="../Styles/buyer_reg.css">
	<script src="https://kit.fontawesome.com/c587fc1763.js" crossorigin="anonymous"></script>
	<title>Buyer Registration - AgroCraft</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="../portal_files/bootstrap.min.css">
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<style>
	.myfooter {
        background-color: #292b2c;

        color: goldenrod;
        margin-top: 15px;
    }

    .aligncenter {
        text-align: center;
    }

    a {
        color: goldenrod;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    nav {
        background-color: #292b2c;
    }

    .navbar-custom {
        background-color: #292b2c;
    }

    /* change the brand and text color */
    .navbar-custom .navbar-brand,
    .navbar-custom .navbar-text {
        background-color: #292b2c;
    }

    .navbar-custom .navbar-nav .nav-link {
        background-color: #292b2c;
    }

    .navbar-custom .nav-item.active .nav-link,
    .navbar-custom .nav-item:hover .nav-link {
        background-color: #292b2c;
    }


    .mybtn {
        border-color: green;
        border-style: solid;
    }


    .right {
        display: flex;
    }

    .left {
        display: none;
    }

    .cart {
        /* margin-left:10px; */
        margin-right: -9px;
    }

    .profile {
        margin-right: 2px;

    }

    .login {
        margin-right: -2px;
        margin-top: 12px;
        display: none;
    }

    .searchbox {
        width: 60%;
    }

    .lists {
        display: inline-block;
    }

    .moblists {
        display: none;
    }

    .logins {
        text-align: center;
        margin-right: -30%;
        margin-left: 35%;
    }
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        min-height: 100vh;
        padding: 40px 20px;
    }

    .registration-container {
        max-width: 1000px;
        margin: 0 auto;
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }

    .reg-header {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        padding: 40px;
        text-align: center;
    }

    .reg-header h2 {
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-weight: 600;
    }

    .reg-header p {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .reg-body {
        padding: 40px;
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
        border-color: #f5576c;
        box-shadow: 0 0 0 0.2rem rgba(245, 87, 108, 0.25);
    }

    .btn-register {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border: none;
        border-radius: 10px;
        padding: 15px 50px;
        font-size: 1.1rem;
        font-weight: 600;
        color: white;
        width: 100%;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(245, 87, 108, 0.6);
    }

    .login-link {
        text-align: center;
        margin-top: 20px;
    }

    .login-link a {
        color: #f5576c;
        text-decoration: none;
        font-weight: 500;
    }

    .login-link a:hover {
        text-decoration: underline;
    }
	@media only screen and (min-device-width:320px) and (max-device-width:480px) {
        /* .mycarousel {
            display: none;
        }

        .firstimage {
            height: auto;
            width: 90%;
        }

        .card {
            width: 80%;
            margin-left: 10%;
            margin-right: 10%;

        }

        .col {
            margin-top: 20px;
        } */

        .right {
            display: none;
            background-color: #ff5500;
        }

        /* 
            .settings{
            margin-left:79%;
        } */
        .left {
            display: flex;
        }

        .moblogo {
            display: none;
        }

        .logins {
            text-align: center;
            margin-right: 35%;
            padding: 15px;
        }

        .searchbox {
            width: 95%;
            margin-right: 5%;
            margin-left: 0%;
        }

        .moblists {
            display: inline-block;
            text-align: center;
            width: 100%;
        }
        /* .pic{
        height:auto;
    } */
    
    /* .mobtext{
        display:none;
    }
    .destext{
        display:inline-block;
        width:90%;
        margin-left: 5%;
        margin-right: 5%;
    } */
    }
    </style>
</head>

<body>

	<main class="my-form">
		<div class="registration-container">
			<div class="reg-header">
				<i class="fas fa-shopping-cart" style="font-size: 3rem; margin-bottom: 20px;"></i>
				<h2>Buyer Registration</h2>
				<p>Join AgroCraft and discover fresh produce directly from farmers</p>
			</div>
			<div class="reg-body">
				<form name="my-form" action="BuyerRegistration.php" method="post">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="full_name"><i class="fas fa-user mr-2"></i>Full Name</label>
								<input type="text" id="full_name" class="form-control" name="name" placeholder="Enter Your Name" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="phone_number"><i class="fas fa-phone-alt mr-2"></i>Phone Number</label>
								<input type="text" id="phone_number" class="form-control" name="phonenumber" placeholder="Phone Number" required>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="email_address"><i class="far fa-envelope mr-2"></i>E-Mail Address</label>
						<input type="email" id="email_address" class="form-control" name="mail" placeholder="E-Mail ID" required>
					</div>

					<div class="form-group">
						<label for="present_address"><i class="fas fa-home mr-2"></i>Present Address</label>
						<textarea type="text" id="present_address" class="form-control" rows="3" name="address" placeholder="Enter your complete address" required></textarea>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="campany_name"><i class="fas fa-building mr-2"></i>Company Name</label>
								<input type="text" id="campany_name" class="form-control" name="company_name" placeholder="Company name" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="lisence"><i class="fas fa-id-badge mr-2"></i>License</label>
								<input type="text" id="lisence" class="form-control" name="license" placeholder="License number" required>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="account1"><i class="fas fa-university mr-2"></i>Bank Account No.</label>
								<input type="text" id="account1" class="form-control" name="account" placeholder="Bank Account number" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="account2"><i class="fas fa-pencil-alt mr-2"></i>PAN No.</label>
								<input type="text" id="account2" class="form-control" name="pan" placeholder="PAN number" required>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label for="user_name"><i class="fas fa-user mr-2"></i>User Name</label>
						<input type="text" id="user_name" class="form-control" name="username" placeholder="Username" required>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="p1"><i class="fas fa-lock mr-2"></i>Password</label>
								<input id="p1" class="form-control" type="password" name="password" placeholder="Create Password" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="p2"><i class="fas fa-lock mr-2"></i>Confirm Password</label>
								<input id="p2" class="form-control" type="password" name="confirmpassword" placeholder="Confirm Password" required>
							</div>
						</div>
					</div>

					<button type="submit" class="btn btn-register" name="register" value="Register">
						Register <i class="fas fa-arrow-right ml-2"></i>
					</button>

					<div class="login-link">
						<p>Already have an account? <a href="BuyerLogin.php">Login here</a></p>
					</div>
				</form>
			</div>
		</div>
	</main>

</body>

</html>


<?php

include("../Includes/db.php");

if (isset($_POST['register'])) {

	$name = mysqli_real_escape_string($con, $_POST['name']);
	$phonenumber = mysqli_real_escape_string($con, $_POST['phonenumber']);
	$address = mysqli_real_escape_string($con, $_POST['address']);
	$company_name = mysqli_real_escape_string($con, $_POST['company_name']);
	$license = mysqli_real_escape_string($con, $_POST['license']);
	$account = mysqli_real_escape_string($con, $_POST['account']);
	$pan = mysqli_real_escape_string($con, $_POST['pan']);
	$mail = mysqli_real_escape_string($con, $_POST['mail']);
	$username = mysqli_real_escape_string($con, $_POST['username']);
	$password = mysqli_real_escape_string($con, $_POST['password']);
	$confirmpassword = mysqli_real_escape_string($con, $_POST['confirmpassword']);

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

	if (strcmp($password, $confirmpassword) == 0) {

		$query = "insert into buyerregistration (buyer_name,buyer_phone,buyer_addr,buyer_comp,
		buyer_license,buyer_bank,buyer_pan,buyer_mail,buyer_username,buyer_password) 
		values ('$name','$phonenumber','$address','$company_name','$license','$account','$pan',
		'$mail','$username','$encryption')";

		$run_register_query = mysqli_query($con, $query);
		echo "<script>alert('SucessFully Inserted');</script>";
		echo "<script>window.open('BuyerLogin.php','_self')</script>";
	} else if (strcmp($password, $confirmpassword) != 0) {
		echo "<script>
			alert('Password and Confirm Password Should be same');
		</script>";
	}
}


?>