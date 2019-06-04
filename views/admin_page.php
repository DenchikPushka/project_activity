<?php
	$user = getUser();
	if (empty($user) || $user->user_type != 3) {
		header('location: index.php?view=main_page');
		exit();
	}
?>
<div class="container">
	<center>
		<p>
			<a href="index.php?view=classes" class="btn btn-lg btn-success" style="width: 200px;">
				Ученики
			</a>
		</p>
		<p>
			<a href="index.php?view=teachers" class="btn btn-lg btn-success" style="width: 200px;">
				Учителя
			</a>
		</p>
	</center>
</div>