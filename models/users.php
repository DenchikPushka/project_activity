<?php
class ModelUsers
{
	public function authorize($username, $password) {
		$mysqli = db_connect();

		$password = md5($password);

		$mysqli->real_query("SELECT `id`,`user_type` FROM `users` WHERE `username` = '$username' AND `password` = '$password'");
		$result = $mysqli->loadObject();

		$mysqli->close();

		return $result;
	}

	public function getData($filter) {
		if (empty($filter)) {
			$filter = '';
		}
		else {
			$filter = 'WHERE '.$filter;
		}
		$mysqli = db_connect();

		$mysqli->real_query("SELECT `id`, `name`, `username`, `user_type` FROM `users` $filter");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

}
?>