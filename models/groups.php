<?php
class ModelGroups
{
	public function getData($filter = null) {
		if (empty($filter)) {
			$filter = '';
		}
		else {
			$filter = 'WHERE '.$filter;
		}
		$mysqli = db_connect();

		$mysqli->real_query("SELECT * FROM `groups` $filter");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function getUsersAndGroupsByProjectId($proj_id) {
		$mysqli = db_connect();

		$mysqli->real_query("SELECT `u`.`name` AS `name`, `c`.`name` AS `classname`, `gm`.`group_id` FROM `groups_map` AS `gm`
							INNER JOIN `groups` AS `g` ON `gm`.`group_id` = `g`.`id`
							INNER JOIN `users` AS `u` ON `gm`.`user_id` = `u`.`id`
							INNER JOIN `classes_map` AS `cm` ON `gm`.`user_id` = `cm`.`user_id`
							INNER JOIN `classes` AS `c` ON `cm`.`class_id` = `c`.`id`
							WHERE `g`.`project_id` = $proj_id");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}
}
?>