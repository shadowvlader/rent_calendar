<?php
$form_title = "New category";
$hidden_input = NULL;
$submit_button = "new-category";
$pre_fill = [
	'name' => ""
];

if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])){
	$category = $wpdb->get_row("SELECT `name` FROM `". $wpdb->prefix ."rent_calendar_categories` WHERE `id` = '". $wpdb->_real_escape($_GET['id']) ."'");
	if($category) {
		$form_title   = "Edit category";
		$hidden_input = "<input type='hidden' name='id' value='" . $_GET['id'] . "'>";
		$submit_button = "save-category";
		$pre_fill = [
			'name' => $category->name
		];
	}
}

if($categoryActionResponse != NULL){
	echo "<div class='". ($categoryActionResponse->success?"updated":"error") ."' id='message'>
		  <p><strong>". $categoryActionResponse->message ."</strong></p>
		</div>";
}
?>
<h2><?php echo $form_title; ?></h2>
<form method="POST" action="<?php echo $config->getItem('plugin_categories_url'); ?>">
	<?php echo $hidden_input; ?>
	<table class="form-table">
		<tbody>
		<tr class="form-field form-required">
			<th scope="row"><label for="name">Category Name <span class="description">(required)</span></label></th>
			<td>
				<input type="text" aria-required="true" value="<?php echo $pre_fill['name']; ?>" id="name" name="name">
			</td>
		</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" name="<?php echo $submit_button; ?>" value="Save" class="button-primary">
	</p>
</form>

<table class="wp-list-table widefat fixed striped pages">
	<thead>
	<tr>
		<th style="width: 20px;">ID</th>
		<th style="width: 70%;">Name</th>
		<th colspan="2" style="width: 150px;">Actions</th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($categories as $category): ?>
		<tr>
			<td><?php echo $category->id; ?></td>
			<td><?php echo $category->name; ?></td>
			<td><a href="<?php echo $config->getItem('plugin_categories_url'); ?>&action=edit&id=<?php echo $category->id; ?>"><span class="dashicons dashicons-welcome-write-blog"></span> Edit</a></td>
			<td><a href="<?php echo $config->getItem('plugin_categories_url'); ?>&action=delete&id=<?php echo $category->id; ?>">&times; Delete</a></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>