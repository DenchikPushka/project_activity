<?php
	$user = getUser();
	if (empty($user) || $user->user_type != 3) {
		header('location: index.php?view=main_page');
		exit();
	}
	$model_users = getModel('users');
	$classes = $model_users->getClasses();
?>
<div class="container">
	<button class="btn btn-success" id="btn_add_class" style="float: right;">Создать класс <i class="fas fa-plus"></i></button>
	<br>
	<?php if (!empty($classes)) { ?>
		<div class="row" style="margin-top: 20px;">
			<div class="col-sm-3"></div>
			<div class="col-sm-6">
				<table class="table table-hover">
				<tbody>
				<?php foreach ($classes as $class) { ?>
					<tr class="tr_href" data-href="index.php?view=class&id=<?= $class->id ?>">
						<th><?= $class->name; ?></th>
						<th style="text-align: right;">
							<button class="btn btn-danger btn_delete_group" data-class_id="<?= $class->id; ?>" data-class_name="<?= trim(mb_ereg_replace('[^A-Za-zА-ЯЁа-яё\d\_\s]', '', $class->name)); ?>"><i class="fas fa-trash-alt"></i></button>
						</th>
					</tr>
				<?php } ?>
				</tbody>
				</table>
			</div>
			<div class="col-sm-3"></div>
		</div>
	<?php } else {
		echo '<p>Классы не созданы</p>';
	} ?>
</div>
<script type="text/javascript">
	jQuery(document).ready(function() {
		document.getElementById('page_title').innerHTML = 'Классы';
	});
</script>