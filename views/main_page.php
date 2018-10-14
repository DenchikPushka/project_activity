<?php
	$user = getUser();
	if (!empty($user)) {
		switch ($user->user_type) {
			case 1:
				header('location: index.php?view=projects_kid');
				break;
			case 2:
				header('location: index.php?view=projects_teacher');
				break;
			case 3:
				header('location: index.php?view=classes');
				break;
			default:
				header('location: index.php?view=main_page');
				break;
		}
		exit();
	}
?>
<div class="container">
	<center><h2>Main page</h2><a class="btn btn-primary" href="index.php?view=authorization">Войти</a></center>
</div>