<?php
class ControllerUsers
{
	function authorization() {
		if (!empty($_POST['username']) && !empty($_POST['password'])) {
			$username = $_POST['username'];
			$password = $_POST['password'];
			if (mb_ereg('[^a-zA-Z0-9]', $username) || mb_ereg('[^a-zA-Z0-9]', $password)) {
				throw new Exception("Invalid input data", 500);
			}
			$model_users = getModel('users');
			$item = $model_users->authorize($username, $password);

			if (!is_null($item)) {
				$_SESSION['user_id'] = $item->id;
				$_SESSION['user_type'] = $item->user_type;
			}
			die(json_encode($item));
		}
		else {
			throw new Exception("Empty input data", 500);
		}
	}

	function exitUser() {
		unset($_SESSION['user_id'], $_SESSION['user_type']);
		header('location: index.php?view=main_page');
		die();
	}
}
?>