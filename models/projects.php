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

}
?>