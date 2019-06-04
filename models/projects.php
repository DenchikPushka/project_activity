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

		$mysqli->real_query("
			SELECT 	`p`.`id`,
					`p`.`name`,
					`p`.`description`
			  FROM 	`groups_map` AS `gp`
					INNER JOIN `groups` AS `g`
						ON `gp`.`group_id` = `g`.`id`
					INNER JOIN `projects` AS `p`
						ON `g`.`project_id` = `p`.`id`
			  WHERE `user_id` = $kid_id
			  GROUP BY `p`.`id`
		");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function addNewProject($name, $description, $creator_id) {
		$mysqli = db_connect();

		$name = $mysqli->real_escape_string($name);
		$description = $mysqli->real_escape_string($description);

		$mysqli->real_query("
			INSERT INTO `projects` (`name`, `description`, `creator_id`)
			VALUES ('$name', '$description', $creator_id)
		");
		$project_id = $mysqli->insert_id;

		$mysqli->close();

		return $project_id;
	}

	public function deleteProject($project_id) {
		$mysqli = db_connect();

		$mysqli->real_query("DELETE FROM `projects` WHERE `id` = $project_id");

		$mysqli->close();

		return true;
	}

}
?>