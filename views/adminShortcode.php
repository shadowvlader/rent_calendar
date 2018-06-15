<div class="wrap">
  <h2>Rent Calendar - Shortcode</h2>

    <p>
      You can easily add reservation capabilities to your pages with shortcodes. Here you can see the shortcode options.<br />
    </p>
    
    <h3>Example #1</h3>
    <p>
      [reservation-button]
    </p>
    <p>
      This will add a button that redirects the user to the reservation page (if one is set). When setting up a single reservation page, do not use any options. The options will be overriden by the button's options
    </p>

    <h3>Example #2</h3>
    <p>
        [reservation]
    </p>
    <p>
        This shortcode will display the reservation form. You can choose to have this on a single page with all products linking to it with the reservation-button. Or you can have this on multiple pages paired with one or more products.
    </p>

    <h3>Example #3</h3>
    <p>
      [reservation-button products=1]<br>
      [reservation products=1]
    </p>
    <p>
        If you want to automatically pre-select the product that goes into the reservation, use the option <b>products</b> with the corresponding product ID
    </p>
    
    <h3>Example #4</h3>
    <p>
        [reservation-button products=1,2,3]
        [reservation products=1,2,3]
    </p>
    <p>
        If you want to automatically pre-select multiple product that go into the reservation, use the option <b>products</b> with the corresponding product IDs separated by comma.<br>
    </p>

    <h3>Example #5</h3>
    <p>
        [reservation-button products=1,2,3 lock=1]
        [reservation products=1,2,3 lock=1]
    </p>
    <p>
        <b>lock</b> option only works with at least 1 product specified. It will not show the product selection dropdown and will automatically assign specified products to this reservation.
    </p>
    
    <p><b><u>All product IDs are available at the product list page on this plugin's sub-menu</u></b></p>
</div><!-- .wrap -->