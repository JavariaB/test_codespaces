<?php

require_once ("../../functions/hospital.php");

session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) header('Location: ../../index.php');

$error = $success = '';

$conn = mysqli_connect('host.docker.internal', 'root', '', 'krankencare');

$availability = ['1', '0'];

try {

	$bed = getBedById($_SESSION['id'], $_GET['id']);


	if ($_SERVER['REQUEST_METHOD'] == 'POST') {

		$errors = [];


		if (isset($_POST['is_available'])) {
			if (!in_array($_POST['is_available'], $availability)) {
				echo 'Invalid option selected.';
			}
		} else {
			$errors['is_available'] = 'Please select availability.';
		}


		if (empty($errors)) {

			updateOrCreateHospitalBed($_SESSION['id'], $bed[0]['bed_name'], $_POST['is_available']);

			$success = 'Bed details updated successfully.';
		}
	}
} catch (Exception $e) {
	$error = $e->getMessage();
} finally {
	$conn->close();
}

$has_errors = !empty($error) || !empty($errors);

?>

<!DOCTYPE html>
<html>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

	<title>KrankenCare - Update Bed</title>

	<base href="../../">

	<!-- favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico" />

	<!-- bootstrap.min css -->
	<link rel="stylesheet" href="plugins/bootstrap/css/bootstrap.min.css">

	<!-- Icon Font Css -->
	<link rel="stylesheet" href="plugins/icofont/icofont.min.css">

	<!-- Slick Slider  CSS -->
	<link rel="stylesheet" href="plugins/slick-carousel/slick/slick.css">
	<link rel="stylesheet" href="plugins/slick-carousel/slick/slick-theme.css">

	<!-- Bootstrap datepicker -->
	<link rel="stylesheet" href="plugins/bs-datepicker/css/bootstrap-datepicker3.standalone.css">

	<!-- Main Stylesheet -->
	<link rel="stylesheet" href="css/style.css">

</head>

<body id="top" Style="background-color: #f9f9f9">

	<header>
		<div class="header-top-bar" Style="background-color: #005073">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-lg-6">
					</div>
					<div class="col-lg-6">
						<div class="text-lg-right top-right-bar mt-2 mt-lg-0">
							<?php if (isset($_SESSION['id']) && !empty($_SESSION['id'])) : ?>
								<a href="admin/hospital_staff_profile.php" class="pr-3"><span>Welcome <?= ucwords($_SESSION['name']) ?>!</span></a>
								<a href="logout.php" class="pr-3"><span>Logout</span></a>
							<?php else : ?>
								<a href="index.php" class="pr-3"><span>Login</span></a>
								<a href="check.php" class="pr-3"><span>Register</span></a>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<nav class="navbar navbar-expand-lg navigation" id="navbar">
			<div class="container">
				<a class="navbar-brand" href="admin/hospital_staff_profile.php">
					<img src="images/logo.jpg" width=100px height=50px alt="" class="img-fluid">
				</a>
			</div>
		</nav>
	</header>

	<section class="section blog-wrap pt-5">
		<div class="container">
			<div class="row">
				<div class="col-lg-3">
					<div class="sidebar-wrap pl-lg-4 mt-5 mt-lg-0">

						<div class="sidebar-widget category mb-3">
							<h5 class="mb-4">Navigation</h5>

							<ul class="list-unstyled">
								<li class="align-items-center">
									<a href="admin/hospital_staff_profile.php">Profile</a>
								</li>
								<li class="align-items-center">
									<a href="admin/hospital/change-password.php">Change Password</a>
								</li>
								<li class="align-items-center">
									<a href="admin/hospital/add.php">Add New Bed</a>
								</li>
								<li class="align-items-center">
									<a href="admin/hospital/list.php">Beds List</a>
								</li>
							</ul>
						</div>

					</div>
				</div>

				<div class="col-lg-9">
					<div class="row">
						<div class="col-lg-12 col-md-12 mb-5">
							<div class="blog-item">
								<div class="blog-item-content">
									<h2 class="mb-3">Update Bed</h2>

									<p class="mb-4">You can use this section to update bed record.</p>

									<?php if (!empty($success)) : ?>
										<div class="row justify-content-md-center mb-3">
											<div class="col-12">
												<div class="alert alert-success contact__msg" role="alert">
													<strong>Success: </strong><?= $success ?>
												</div>
											</div>
										</div>
									<?php endif; ?>

									<?php if (!empty($error)) : ?>
										<div class="row justify-content-md-center mb-3">
											<div class="col-12">
												<div class="alert alert-danger contact__msg" role="alert">
													<strong>Error: </strong><?= $error ?>
												</div>
											</div>
										</div>
									<?php endif; ?>

									<form action="" method="post">

										<div class="row">
											<div class="col-3">
												<label for="bed_name">Bed Name<span class="text-danger">*</span></label>
											</div>
											<div class="col-9">
												<div class="form-group">
												<input id="bed_name" type="text"  class="form-control" value="<?= $bed[0]['bed_name'] ?>" readonly>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-3">
												<label for="is_available">Bed Availability<span class="text-danger">*</span></label>
											</div>
											<div class="col-9">
												<div class="form-group">
													<select name="is_available" id="is_available" class="form-control <?= empty($errors['is_available']) ?: 'invalid-input' ?>" placeholder="Please select availability." value="<?= isset($_POST['is_available']) && $has_errors ? $_POST['is_available'] : '' ?>">
														<option value="1">Yes</option>
														<option value="0">No</option>
													</select>
													<?php if (!empty($errors['is_available'])) : ?>
														<small class="text-danger"><?= $errors['is_available'] ?></small>
													<?php endif; ?>
												</div>
											</div>
										</div>

										<div class="row">
											<div class="col-3"></div>
											<div class="col-9">
												<div>
													<input class="btn btn-main btn-round-full mt-2" name="submit" type="submit" Style="background-color: #005073; border-color: #005073" value="Update"></input>
												</div>
											</div>
										</div>
									</form>
								</div>
							</div>
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

	<!-- Bootstrap datepicker -->
	<script src="plugins/bs-datepicker/js/bootstrap-datepicker.min.js"></script>

	<script>
		$(function() {
			$('#dob').datepicker({
				format: "dd-M-yyyy",
				endDate: "date()",
				autoclose: true,
				todayHighlight: true
			});
		});
	</script>

</body>

</html>