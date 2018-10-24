<?php
class ControllerProjects
{
	function addNewProject() {
		$user = getUser();
		if (empty($user) || $user->user_type != 2) {
			throw new Exception("Forbidden", 403);
		}
		$creator_id = $user->id;
		if (!empty($_POST['name'])) {
			$name = trim(mb_ereg_replace('[^A-Za-zА-ЯЁа-яё\d\_\s]', '', $_POST['name']));
			$description = null;
			if (!empty($_POST['description'])) {
				$description = trim($_POST['description']);
			}
			$model_projects = getModel('projects');
			$project_id = $model_projects->addNewProject($name, $description, $creator_id);

			die(json_encode($project_id));
		} else {
			throw new Exception("Empty input data", 500);
		}
	}

}
?>