<?php

session_start();

if (!isset($_SESSION['id']) || empty($_SESSION['id'])) header('Location: ../../index.php');

$page = isset($_GET['page']) && !empty($_GET['page']) && (int)$_GET['page'] > 0 ? $_GET['page'] : 1;

$error = $success = '';

$key = 0;

$conn = mysqli_connect('host.docker.internal', 'root', '', 'krankencare');

try {
	if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id']) && !empty($_GET['id'])) {
		$sql = $conn->prepare('DELETE FROM beds WHERE `user_id` = ? AND `id` = ?');

		if (!$sql) throw new Exception($conn->error);

		$sql->bind_param(
			'ii',
			$_SESSION['id'],
			$_GET['id'],
		);

		if (!$sql->execute()) throw new Exception($conn->error);

		header('Location: ?delete=true');
		exit;
	}

	if (isset($_GET['delete']) && $_GET['delete'] == 'true') {
		$success = 'Record deleted successfully.';
	}

	if (isset($_GET['delete']) && $_GET['delete'] == 'false') {
		$error = 'Unable to delete the record.';
	}

	$sql = $conn->prepare('SELECT * FROM beds WHERE `user_id` = ? LIMIT 10 OFFSET ?');

	$offset = ($page - 1) * 10;

	$sql->bind_param('ii', $_SESSION['id'], $offset);

	if (!$sql->execute()) throw new Exception($conn->error);

	$result = $sql->get_result();



	$totalRecordsSql = $conn->prepare('SELECT COUNT(*) AS count FROM beds WHERE `user_id` = ?');
	$totalRecordsSql->bind_param('i', $_SESSION['id']);

	if (!$totalRecordsSql->execute()) throw new Exception($conn->error);

	$count = $totalRecordsSql->get_result();
	$count = $count->fetch_assoc();
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

	<title>KrankenCare - Beds List</title>

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
									<h2 class="mb-3">List of beds</h2>

									<p class="mb-4">This is your current list of beds.</p>

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

									<table class="table table-bordered" Style="background-color: #f8f9fa">
										<thead>
											<tr style="background-color: #17a050">
												<th scope="col">#</th>
												<th scope="col">Bed Names</th>
												<th scope="col">Beds Availability</th>
												<th scope="col">Registered</th>
												<th scope="col">Actions</th>
											</tr>
										</thead>
										<tbody>
											<?php if ($result->num_rows > 0) : ?>
												<?php while ($beds = $result->fetch_assoc()) : ?>
													<tr>
														<th scope="row"><?= ++$key + (($page - 1) * 10) ?></th>
														<td><a href="admin/hospital/update.php?id=<?= $beds['id'] ?>" class="text-info"><?= ucwords($beds['bed_name']) ?></a></td>
														<td><?= $beds['is_available'] ? 'Available' : 'Unavailable' ?></td>
														<td><?= date('d-M-Y', strtotime($beds['created_at'])) ?></td>
														<td>
															<a href="admin/hospital/list.php?id=<?= $beds['id'] ?>&action=delete" class="text-danger" onclick="return confirm('Are you sure you want to delete this item?');">Delete</a>
														</td>
													</tr>
												<?php endwhile; ?>
											<?php else : ?>
												<tr>
													<th colspan="7">No Record Found</th>
												</tr>
											<?php endif; ?>
										</tbody>
									</table>

									<nav aria-label="Page navigation example">
										<ul class="pagination">
											<li class="page-item">
												<a class="page-link" Style="background-color: #E5E0F5" href="admin/hospital/list.php?page=<?= isset($_GET['page']) && (int)$_GET['page'] > 1 ? (int)$_GET['page'] - 1 : 1 ?>">Previous</a>
											</li>
											<?php for ($i = 1; $i <= ceil($count['count'] / 10); $i++) : ?>
												<li class="page-item">
													<a class="page-link" Style="background-color: #E5E0F5" href="admin/hospital/list.php?page=<?= $i ?>"><?= $i ?></a>
												</li>
											<?php endfor; ?>
											<li class="page-item">
												<a class="page-link" Style="background-color: #E5E0F5" href="admin/hospital/list.php?page=<?= isset($_GET['page']) && (int)$_GET['page'] < ceil($count['count'] / 10) ? (int)$_GET['page'] + 1 : ceil($count['count'] / 10) ?>">Next</a>
											</li>
										</ul>
									</nav>

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

</body>

</html>