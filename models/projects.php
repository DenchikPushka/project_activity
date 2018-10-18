<?php
class ModelProjects
{
	public function getData($filter = null) {
		if (empty($filter)) {
			$filter = '';
		}
		else {
			$filter = 'WHERE '.$filter;
		}
		$mysqli = db_connect();

		$mysqli->real_query("SELECT * FROM `projects` $filter");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function getProjectsByKid($kid_id) {
		$mysqli = db_connect();

		$mysqli->real_query("SELECT `p`.`id`, `p`.`name`, `p`.`description` FROM `groups_map` AS `gp`
			INNER JOIN `groups` AS `g` ON `gp`.`group_id` = `g`.`id`
			INNER JOIN `projects` AS `p` ON `g`.`project_id` = `p`.`id`
			WHERE `user_id` = $kid_id");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

}
?>