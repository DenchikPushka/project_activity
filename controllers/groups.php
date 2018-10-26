<?php
class ControllerGroups
{
	function addNewGroup() {
		$user = getUser();
		if (empty($user) || $user->user_type != 2) {
			throw new Exception("Forbidden", 403);
		}
		if (!empty($_POST['project_id']) && !empty($_POST['name']) && !empty($_POST['data'])) {
			$data = json_decode($_POST['data']);
			$project_id = mb_ereg_replace('[^\d]', '', $_POST['project_id']);
			$name = trim(mb_ereg_replace('[^A-Za-zА-ЯЁа-яё\d\_\s]', '', $_POST['name']));
			
			$model_groups = getModel('groups');
			$group_id = $model_groups->addNewGroup($project_id, $name, $data);

			die(json_encode($group_id));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}

	function deleteGroup() {
		$user = getUser();
		if (empty($user) || $user->user_type != 2) {
			throw new Exception("Forbidden", 403);
		}
		if (!empty($_POST['group_id'])) {
			$group_id = mb_ereg_replace('[^\d]', '', $_POST['group_id']);
			
			$model_groups = getModel('groups');
			$model_groups->deleteGroup($group_id);

			die(json_encode(true));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}
}
?>