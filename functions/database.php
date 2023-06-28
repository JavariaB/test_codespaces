<?php

function connectDatabase()
{
	$connection = mysqli_connect('host.docker.internal', 'root', '', 'krankencare');

	if (!$connection) {
		throw new Exception('Unable to connect to the database.');
	}

	return $connection;
}

function disconnectDatabase($connection)
{
	if ($connection) $connection->close();
}

function runSelectQuery($sql, $bindings = null, ...$params)
{
	$rows = [];

	$conn = connectDatabase();

	$sql = $conn->prepare($sql);

	if (!$sql) throw new Exception($conn->error);

	if (!empty($bindings)) {
		$sql->bind_param($bindings, ...$params);
	}

	if (!$sql->execute()) throw new Exception($conn->error);

	$result = $sql->get_result();

	if ($result->num_rows <= 0) throw new Exception('No record found.', 404);

	while ($row = $result->fetch_assoc()) {
		$rows[] = $row;
	}

	disconnectDatabase($conn);

	return $rows;
}

function runNonSelectQuery($sql, $bindings = null, ...$params)
{
	$conn = connectDatabase();

	$sql = $conn->prepare($sql);

	if (!$sql) throw new Exception($conn->error);

	if (!empty($bindings)) {
		$sql->bind_param($bindings, ...$params);
	}

	if (!$sql->execute()) throw new Exception($conn->error);

	disconnectDatabase($conn);

	return true;
}
