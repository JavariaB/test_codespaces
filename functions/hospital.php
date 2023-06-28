<?php

require_once('database.php');

function getHospitalsByPostalCode($postalCode, $areBedsAvailable = true)
{
	return runSelectQuery('
		SELECT users.*, count(beds.id) as available_beds FROM users
		JOIN beds ON users.id = beds.user_id
		WHERE users.postal_code = ? AND beds.is_available = ?
		GROUP BY users.id
	', 'ss', $postalCode, $areBedsAvailable);
}

function getAvailableBedsByHospitalId($hospitalId)
{
	return runSelectQuery('
		SELECT * FROM beds WHERE user_id = ? AND is_available = true
	', 's', $hospitalId);
}

function updateOrCreateHospitalBed($hospitalId, $bedName, $isAvailable = true)
{
	try {
		runSelectQuery('
			SELECT * FROM beds WHERE user_id = ? AND bed_name = ?
		', 'is', $hospitalId, $bedName);

		return runNonSelectQuery('
			UPDATE beds SET is_available = ?
			WHERE user_id = ? AND bed_name = ?
		', 'sis', $isAvailable, $hospitalId, $bedName);
	} catch (Exception $e) {
		if ($e->getCode() == 404) {
			return runNonSelectQuery('
				INSERT INTO beds (user_id, bed_name, is_available)
				VALUES (?, ?, ?)
			', 'iss', $hospitalId, $bedName, $isAvailable);
		}

		throw new Exception($e->getMessage(), $e->getCode());
	}
}

function deleteHospitalBed($hospitalId, $bedName)
{
	return runNonSelectQuery('
		DELETE FROM beds WHERE user_id = ? AND bed_name = ?
	', 'is', $hospitalId, $bedName);
}

function getBedById($hospitalId, $bedId)
{
	return runSelectQuery('
		SELECT * FROM beds WHERE user_id = ? AND id = ?
	', 'ii', $hospitalId, $bedId);
}
