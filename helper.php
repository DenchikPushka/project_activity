<?php

	class CustomMysqli extends mysqli
	{
		function loadObject()
		{
			$result = $this->store_result();
			if ($result === false) {
				die($this->error);
			}
			elseif (empty($result)) {
				return null;
			}
			else {
				return $result->fetch_object();
			}
		}

		function loadObjectsList()
		{
			$result = $this->store_result();
			if ($result === false) {
				die($this->error);
			}
			elseif (empty($result)) {
				return array();
			}
			else {
				$rows = array();
				while ($row = $result->fetch_object()) {
				 	$rows[] = $row;
				}
				return $rows;
			}
		}
	}

	function db_connect()
	{
		$mysqli = new CustomMysqli(db_host, db_user, db_pass, db_name);
		if ($mysqli->connect_error) {
			die('Connect Error (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
		}
		$mysqli->set_charset('utf8');
		return $mysqli;
	}

	function getModel($name) {
		if (preg_match('/^[0-9a-zA-Z\_]+$/', $name)) {
			$filename = "/models/$name.php";
			if (file_exists(DIR.$filename)) {
			    require_once(DIR.$filename);
			    $model_name = 'Model'.$name;
			    $instance = new $model_name();
			    return $instance;
			}
			else {
			    throw new Exception('Model not found', 500);
			}
		}
		else {
			throw new Exception('Invalid modelname', 500);
		}
	}

	function getUser() {
		if (empty($_SESSION['user_id'])) {
			return null;
		}
		$user_id = $_SESSION['user_id'];
		$model = getModel('users');
		$users = $model->getData("`id` = $user_id");
		return $users[0];
	}


?>