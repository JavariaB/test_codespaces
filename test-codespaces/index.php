<?php

session_start();

if (isset($_SESSION['id']) && !empty($_SESSION['id']));

$error = $success = '';

$conn = mysqli_connect('host.docker.internal', 'root', '', 'krankencare');

try {
	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$errors = [];

		if (!isset($_POST['email']) || empty($_POST['email'])) {
			$errors['email'] = 'Email address is required';
		}

		if (!isset($_POST['password']) || empty($_POST['password'])) {
			$errors['password'] = 'Password is required';
		}


		if (empty($errors)) {

			$sql = $conn->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
			$sql->bind_param('s', $_POST['email']);

			if (!$sql->execute()) throw new Exception($conn->error);

			$result = $sql->get_result();

			if ($result->num_rows <= 0) throw new Exception('');

			$user = $result->fetch_assoc();


			if (!password_verify($_POST['password'], $user['password'])) throw new Exception('');


			$_SESSION = [
				'id' => $user['id'],
				'name' => $user['name'],
				'email' => $user['email'],
				'role' => $user['role'],
				'postal_code' => $user['postal_code'],
			];

			$sql = $conn->prepare('SELECT role FROM users WHERE email = ? LIMIT 1');
			$sql->bind_param('s', $_POST['email']);

			if (!$sql->execute()) throw new Exception($conn->error);

			$result = $sql->get_result();

			if ($result->num_rows <= 0) throw new Exception('');

			$result = $result->fetch_assoc();


			if ($result) {
				$role = $result['role'];

				if ($role === "ambulance_staff") {
					$_SESSION['ambulance_staff'] = true;
					header('Location: admin/ambulance_staff_profile.php');
					exit;

				} elseif ($role === "hospital_staff") {
					$_SESSION['hospital_staff'] = true;
					header('Location: admin/hospital_staff_profile.php');
					exit;

				} else {
					echo 'Invalid username or password.';
				}
			}
		}
	}
} catch (Exception $e) {
	$error = 'These credentials do not match our records.';
} finally {
	$conn->close();
}

$has_errors = !empty($error) || !empty($errors);

?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<title>KrankenCare - Home</title>

	<!-- favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico" />

	<!-- bootstrap.min css -->
	<link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">

	<!-- Icon Font Css -->
	<link rel="stylesheet" href="plugins/icofont/icofont.min.css">

	<!-- Slick Slider  CSS -->
	<link rel="stylesheet" href="plugins/slick-carousel/slick/slick.css">
	<link rel="stylesheet" href="plugins/slick-carousel/slick/slick-theme.css">

	<!-- Main Stylesheet -->
	<link rel="stylesheet" href="css/style.css">

</head>

<body id="top" Style="background-color: #f9f9f9">

	<header>
		<div class="header-top-bar" Style="background-color: #005073 ">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-lg-6">
					</div>
					<div class="col-lg-6">
						<div class="text-lg-right top-right-bar mt-2 mt-lg-0">
							<a href="index.php" class="pr-3"><span>Login</span></a>
							<a href="check.php" class="pr-3"><span>Register</span></a>
						</div>
					</div>
				</div>
			</div>
		</div>
		<nav class="navbar navbar-expand-lg navigation" id="navbar">
			<div class="container">
				<a class="navbar-brand" href="index.php">
					<img src="images/logo.jpg" width=100px height=50px alt="" class="img-fluid">
				</a>
			</div>
		</nav>
	</header>

	<section class="contact-form-wrap section pt-5">
		<div class="container">

			<div class="row justify-content-center">
				<div class="col-lg-6">
					<div class="section-title text-center mb-4">
						<h2 class="text-md mb-2" Style="color: #005073">Welcome to KrankenCare.</h2>
						<div class="divider mx-auto my-4" Style="background-color: #005073"></div>
						<p Style="color: #005073">Enter your credentials to login.</p>
					</div>
				</div>
			</div>

			<?php if (!empty($success)) : ?>
				<div class="row justify-content-md-center">
					<div class="col-8">
						<div class="alert alert-success contact__msg" role="alert">
							<strong>Success: </strong><?= $success ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (!empty($error)) : ?>
				<div class="row justify-content-md-center">
					<div class="col-8">
						<div class="alert alert-danger contact__msg" role="alert">
							<strong>Error: </strong><?= $error ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="row justify-content-md-center mt-3">
				<div class="col-lg-8 col-md-8 col-sm-8">
					<form id="login-form" class="contact__form" method="post" action="">
						<div class="form-group">
							<label for="email">Email Address <span class="text-danger">*</span></label>
							<input name="email" id="email" type="email" class="form-control <?= empty($errors['email']) ?: 'invalid-input' ?>" placeholder="Enter your email address" value="<?= isset($_POST['email']) && $has_errors ? $_POST['email'] : '' ?>">
							<?php if (!empty($errors['email'])) : ?>
								<small class="text-danger"><?= $errors['email'] ?></small>
							<?php endif; ?>
						</div>
						<div class="form-group">
							<label for="password">Password <span class="text-danger">*</span></label>
							<input name="password" id="password" type="password" class="form-control <?= empty($errors['password']) ?: 'invalid-input' ?>" placeholder="Enter password">
							<?php if (!empty($errors['password'])) : ?>
								<small class="text-danger"><?= $errors['password'] ?></small>
							<?php endif; ?>
						</div>
						<div class="text-center">
							<input class="btn btn-main btn-round-full" name="submit" type="submit" Style="background-color: #005073; border-color: #005073" value="Login"></input>
						</div>
					</form>

					<div class="row">
						<div class="col-12 text-center mt-4">
							<p Style="color: #005073">New to Fresh Organic? <a href="check.php">Create Account</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- footer Start -->
	<footer class="footer section bg" style="border-top: 1px solid black;">
		<div class="container">
			<div class="row">
				<div class="col-lg-4 mr-auto col-sm-6">
					<div class="widget mb-5 mb-lg-0">
						<div class="logo mb-4">
							<img src="images/logo.jpg" width=150px height=100px alt="" class="img-fluid">
						</div>

						<p>We are here to do our bit in creating a quicker, friendlier, and more efficient manner for everyone to deliver high-quality healthcare on time.</p>

						<ul class="list-inline footer-socials mt-4">
							<li class="list-inline-item">
								<a href="javascript:void(0);">
									<i class="icofont-facebook"></i>
								</a>
							</li>
							<li class="list-inline-item">
								<a href="javascript:void(0);">
									<i class="icofont-twitter"></i>
								</a>
							</li>
							<li class="list-inline-item">
								<a href="javascript:void(0);">
									<i class="icofont-linkedin"></i>
								</a>
							</li>
						</ul>
					</div>
				</div>

				<div class="col-lg-3 col-md-6 col-sm-6">
					<div class="widget widget-contact mb-5 mb-lg-0">
						<h4 class="text-capitalize mb-3" Style="color: #005073">Get in Touch</h4>
						<div class="divider mb-4" Style="background-color: #005073"></div>

						<div class="footer-contact-block mb-4">
							<div class="icon d-flex align-items-center">
								<i class="icofont-email mr-3"></i>
								<span class="h6 mb-0">Support Available for 24/7</span>
							</div>
							<h4 class="mt-2"><a href="tel:+23-345-67890">support@domain.com</a></h4>
						</div>

						<div class="footer-contact-block">
							<div class="icon d-flex align-items-center">
								<i class="icofont-support mr-3"></i>
								<span class="h6 mb-0">Mon to Fri : 08:30 - 18:00</span>
							</div>
							<h4 class="mt-2"><a href="tel:+23-345-67890">+92-123-4567</a></h4>
						</div>
					</div>
				</div>
			</div>

			<div class="footer-btm py-4 mt-5">
				<div class="row align-items-center justify-content-between">
					<div class="col-lg-6">
						<div class="copyright">
							&copy; Copyright Reserved, <?= date('Y') ?>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-4">
						<a class="backtop js-scroll-trigger" href="#top" Style="background-color: #005073">
							<i class="icofont-long-arrow-up"></i>
						</a>
					</div>
				</div>
			</div>
		</div>
	</footer>

	<!-- Main jQuery -->
	<script src="plugins/jquery/jquery.js"></script>

	<!-- Bootstrap 4.3.2 -->
	<script src="plugins/bootstrap/js/popper.js"></script>
	<script src="plugins/bootstrap/js/bootstrap.min.js"></script>

</body>

</html>