<?php
if($productActionResponse != NULL){
	echo "<div class='". ($productActionResponse->success?"updated":"error") ."' id='message'>
		  <p><strong>". $productActionResponse->message ."</strong></p>
		</div>";
}
?>

<h2>Reservations</h2>

<table class="wp-list-table widefat fixed striped pages">
    <thead>
    <tr>
        <th style="width: 20px;">ID</th>
        <th style="width: 20%;">Name</th>
        <th style="width: 20%;">Phone</th>
        <th style="width: 40%;">Products</th>
        <th style="width: 10%;">Total Price</th>
        <th style="width: 10%;">Period</th>
        <th style="width: 10%;">Status</th>
        <th colspan="2" style="width: 150px;">Actions</th>
    </tr>
    </thead>
    <tbody>

	<?php foreach ($reservations as $reservation): ?>
        <tr>
            <td><?php echo $reservation->id; ?></td>
            <td><?php echo $reservation->name; ?></td>
            <td><?php echo $reservation->phone; ?></td>
            <td>
                <ul>
                <?php $total_price = 0; ?>
                <?php foreach($reservation->products as $product): ?>
                    <li><?php $total_price += $product->price; echo $product->name; ?> (<?php echo number_format($product->price, 2); ?>€)</li>
                <?php endforeach; ?>
                </ul>
            </td>
            <td><?php echo number_format($total_price , 2); ?>€</td>
            <td>
                <b>From: </b><?php echo date("Y-m-d", strtotime($reservation->date_from)); ?><br>
                <b>To: </b><?php echo date("Y-m-d", strtotime($reservation->date_to)); ?>
            </td>
            <td><?php echo strtoupper($reservation->status); ?></td>
            <td>
                <?php if($reservation->status != "pending"): ?>
                    <a href="<?php echo $config->getItem('plugin_reservations_url'); ?>&action=set-pending&id=<?php echo $reservation->id; ?>"><span class="dashicons dashicons-image-rotate"></span> Set pending</a>
                <?php endif; ?>
                <?php if($reservation->status == "pending"): ?>
                    <a href="<?php echo $config->getItem('plugin_reservations_url'); ?>&action=set-confirmed&id=<?php echo $reservation->id; ?>"><span class="dashicons dashicons-editor-spellcheck"></span> Set confirmed</a>
                <?php elseif($reservation->status == "confirmed"): ?>
                    <a href="<?php echo $config->getItem('plugin_reservations_url'); ?>&action=set-completed&id=<?php echo $reservation->id; ?>"><span class="dashicons dashicons-yes"></span> Set completed</a>
                <?php endif; ?>
            </td>
            <td><a href="<?php echo $config->getItem('plugin_reservations_url'); ?>&action=delete&id=<?php echo $reservation->id; ?>">&times; Delete</a></td>
        </tr>
	<?php endforeach; ?>
    </tbody>
</table>