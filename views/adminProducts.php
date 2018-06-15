<?php
	$form_title = "New product";
	$hidden_input = NULL;
	$submit_button = "new-product";
	$pre_fill = [
		'name' => "",
		'price' => "",
        'category' => ""
	];

	if(isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])){
		$product = $wpdb->get_row("SELECT `price`,`name` FROM `". $wpdb->prefix ."rent_calendar_products` WHERE `id` = '". $wpdb->_real_escape($_GET['id']) ."'");
		if($product) {
			$form_title   = "Edit product";
			$hidden_input = "<input type='hidden' name='id' value='" . $_GET['id'] . "'>";
			$submit_button = "save-product";
			$pre_fill = [
				'name' => $product->name,
				'price' => $product->price,
                'category' => $product->category
			];
		}
	}

	if($productActionResponse != NULL){
		echo "<div class='". ($productActionResponse->success?"updated":"error") ."' id='message'>
		  <p><strong>". $productActionResponse->message ."</strong></p>
		</div>";
	}
?>
<h2><?php echo $form_title; ?></h2>
<form method="POST" action="<?php echo $config->getItem('plugin_products_url'); ?>">
	<?php echo $hidden_input; ?>
	<table class="form-table">
		<tbody>
			<tr class="form-field form-required">
				<th scope="row"><label for="name">Product Name <span class="description">(required)</span></label></th>
				<td>
					<input type="text" aria-required="true" value="<?php echo $pre_fill['name']; ?>" id="name" name="name">
				</td>
			</tr>
			<tr class="form-field form-required">
				<th scope="row"><label for="price">Product Price <span class="description">(required)</span></label></th>
				<td>
					<input type="text" aria-required="true" value="<?php echo $pre_fill['price']; ?>" id="price" name="price">
				</td>
			</tr>
            <tr class="form-field form-required">
                <th scope="row"><label for="category">Category <span class="description">(required)</span></label></th>
                <td>
                    <select id="category" name="category">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category->id; ?>" <?php echo $category->id == $pre_fill['category'] ? "selected" : "" ?>><?php echo $category->name; ?></option>
                        <?php endforeach; ?>
                    </select>
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
			<th style="width: 40%;">Name</th>
            <th style="width: 40%;">Category</th>
			<th style="width: 10%;">Price</th>
			<th colspan="2" style="width: 150px;">Actions</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($products as $product): ?>
			<tr>
				<td><?php echo $product->id; ?></td>
				<td><?php echo $product->name; ?></td>
				<td><?php echo $product->category_name; ?></td>
				<td><?php echo number_format($product->price , 2); ?></td>
				<td><a href="<?php echo $config->getItem('plugin_products_url'); ?>&action=edit&id=<?php echo $product->id; ?>"><span class="dashicons dashicons-welcome-write-blog"></span> Edit</a></td>
				<td><a href="<?php echo $config->getItem('plugin_products_url'); ?>&action=delete&id=<?php echo $product->id; ?>">&times; Delete</a></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>