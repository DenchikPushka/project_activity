<?php
class ModelDatabase
{
	public function getDataTypes() {
		$mysqli = db_connect();

		$mysqli->real_query("SELECT * FROM `db_types`");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function getTables($filter = null) {
		if (empty($filter)) {
			$filter = '';
		}
		else {
			$filter = 'WHERE '.$filter;
		}
		$mysqli = db_connect();

		$mysqli->real_query("SELECT * FROM `db_tables` $filter");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function addNewTable($project_id, $name, $data) {
		$mysqli = db_connect();

		$mysqli->real_query("INSERT INTO `db_tables` (`name`, `project_id`) VALUES ('$name', $project_id)");
		$table_id = $mysqli->insert_id;

		$values = '';
		foreach ($data as $item) {
			$name = trim($item->name);
			$type = $item->type;
			$notnull = (int)$item->notnull;
			if (!empty($name) && !empty($type)) {
				if (!empty($values)) {
					$values .= ", ($table_id, '$name', $type, $notnull)";
				} else {
					$values .= "($table_id, '$name', $type, $notnull)";
				}
			}
		}
		if (!empty($values)) {
			$mysqli->real_query("INSERT INTO `db_attributes` (`table_id`, `name`, `type_id`, `not_null`) VALUES $values");
		} else {
			$mysqli->real_query("DELETE FROM `db_tables` WHERE `id` = $table_id");
			$table_id = 0;
		}

		$mysqli->close();

		return $table_id;
	}

	public function getAttributes($filter = null) {
		if (empty($filter)) {
			$filter = '';
		}
		else {
			$filter = 'WHERE '.$filter;
		}
		$mysqli = db_connect();

		$mysqli->real_query("SELECT * FROM `db_attributes` $filter");
		$result = $mysqli->loadObjectsList();

		$mysqli->close();

		return $result;
	}

	public function getDataFromTable($filter = null) {

	}

}
?>