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

	public function getKids() {
		$mysqli = db_connect();

		$mysqli->real_query("SELECT `cm`.`user_id`, `u`.`name` AS `user_name`, `c`.`name` AS `class_name` FROM `classes_map` AS `cm`
			INNER JOIN `users` AS `u` ON `cm`.`user_id` = `u`.`id`
			INNER JOIN `classes` AS `c` ON `cm`.`class_id` = `c`.`id`
			ORDER BY `c`.`name`, `u`.`name`");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

}
?>