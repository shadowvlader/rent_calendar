<form method="POST" action="<?php echo get_permalink(get_option('reservation_page')); ?>">
    <input type="hidden" name="products" value="<?php echo $options['products']; ?>">
    <input type="hidden" name="lock" value="<?php echo $options['lock']; ?>">
    <input type="submit" class="submit-btn" value="Rezervuoti">
</form>