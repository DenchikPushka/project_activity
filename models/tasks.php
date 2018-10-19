<?php
class ModelTasks
{
	public function getData($filter = null) {
		if (empty($filter)) {
			$filter = '';
		}
		else {
			$filter = 'WHERE '.$filter;
		}
		$mysqli = db_connect();

		$mysqli->real_query("SELECT * FROM `tasks` $filter");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function getGroupsAndTasksByProjectId($proj_id) {
		$mysqli = db_connect();

		$mysqli->real_query("SELECT `g`.`name` AS `group_name`, `tm`.`task_id` FROM `tasks_map` AS `tm`
							INNER JOIN `tasks` AS `t` ON `tm`.`task_id` = `t`.`id`
							INNER JOIN `groups` AS `g` ON `tm`.`group_id` = `g`.`id`
							WHERE `t`.`project_id` = $proj_id");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function addNewTask($project_id, $name, $description, $data) {
		$mysqli = db_connect();

		$mysqli->real_query("INSERT INTO `tasks` (`name`, `description`, `project_id`) VALUES ('$name', '$description', $project_id)");
		$task_id = $mysqli->insert_id;

		$values = '';
		foreach ($data as $group_id) {
			$group_id = trim($mysqli->real_escape_string($group_id));
			if (!empty($group_id)) {
				if (!empty($values)) {
					$values .= ", ($group_id, $task_id)";
				} else {
					$values .= "($group_id, $task_id)";
				}
			}
		}
		if (!empty($values)) {
			$mysqli->real_query("INSERT INTO `tasks_map` (`group_id`, `task_id`) VALUES $values");
		} else {
			$mysqli->real_query("DELETE FROM `tasks` WHERE `id` = $task_id");
			$task_id = 0;
		}

		$mysqli->close();

		return $task_id;
	}

}
?>