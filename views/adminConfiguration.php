<div class="wrap">
  <h2>Rent Calendar - Configuration</h2>
  
  <?php if ( isset($config_saved) && $config_saved === TRUE ) { ?>
    <div class="updated" id="message">
		  <p><strong>Configuration updated.</strong></p>
		</div>
  <?php } ?>
  <form method="post" action="<?php echo $config->getItem('plugin_configuration_url'); ?>">
    <table class="form-table">
      <tbody>
        <tr class="form-field form-required">
          <th scope="row"><label for="captcha"><strong>Use Google's reCaptcha v2?</strong></label></th>
          <td>
            <select id="captcha" name="captcha">
              <option value="no" <?php echo get_option('reservation_captcha') == 'no' ? 'selected="selected"' : ''; ?>>No</option>
              <option value="yes" <?php echo get_option('reservation_captcha') == 'yes' ? 'selected="selected"' : ''; ?>>Yes</option>
            </select>
          </td>
        </tr>
        <tr class="form-field form-required">
            <th scope="row"><label for="captcha_site_key">Site KEY <span class="description">(required)</span></label></th>
            <td>
                <input type="text" aria-required="true" value="<?php echo get_option('reservation_captcha_site_key'); ?>" id="captcha_site_key" name="captcha_site_key">
                <br>&nbsp;<b>Get your reCaptcha keys here: <a href="https://www.google.com/recaptcha/admin">https://www.google.com/recaptcha/admin</a></b>
            </td>
        </tr>
        <tr class="form-field form-required">
            <th scope="row"><label for="captcha_secret_key">Secret KEY <span class="description">(required)</span></label></th>
            <td>
                <input type="text" aria-required="true" value="<?php echo get_option('reservation_captcha_secret_key'); ?>" id="captcha_secret_key" name="captcha_secret_key">
                <br>&nbsp;<b>Get your reCaptcha keys here: <a href="https://www.google.com/recaptcha/admin">https://www.google.com/recaptcha/admin</a></b>
            </td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" value="Save" class="button-primary" />
    </p>
  </form>
  
  <p>
    <strong>Price calculation settings:</strong>
  </p>
  <form method="post" action="<?php echo $config->getItem('plugin_configuration_url'); ?>">
    <table class="form-table">
      <tbody>
        <tr class="form-field form-required">
          <th scope="row"><label for="price_method">Price calculation principle <span class="description">(required)</span></label></th>
          <td>
              <select aria-required="true" id="price_method" name="price_method">
                  <option value="nights" <?php echo get_option('reservation_price_method') == 'nights' ? 'selected="selected"' : ''; ?>>For each night</option>
                  <option value="days" <?php echo get_option('reservation_price_method') == 'days' ? 'selected="selected"' : ''; ?>>For each day</option>
              </select>
          </td>
          <td>
              <b>For each day:</b> Calculates the price based on the number of days. So if the user decides to reserve something from June 1st and return it on June 2nd, it will show the price for <b>2</b> days. June 1st - June 3rd = <b>3</b> days<br>
              <b>For each night:</b> Calculates the price based on the number of nights. So if the user decides to reserve something from June 1st and return it on June 2nd, it will show the price for <b>1</b> day. June 1st - June 3rd = <b>2</b> days. Picking up and returning products on the same day still counts as <b>1</b> day
          </td>
        </tr>
        <tr class="form-field form-required">
          <th scope="row"><label for="show_prices">Show prices for the user?<span class="description">(required)</span></label></th>
          <td>
              <select id="show_prices" name="show_prices">
                  <option value="yes" <?php echo get_option('reservation_show_prices') == 'yes' ? 'selected="selected"' : ''; ?>>Yes</option>
                  <option value="no" <?php echo get_option('reservation_show_prices') == 'no' ? 'selected="selected"' : ''; ?>>No</option>
              </select>
          </td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" value="Save" class="button-primary" />
    </p>
  </form>
  
  <form method="post" action="<?php echo $config->getItem('plugin_configuration_url'); ?>">
    <table class="form-table">
      <tbody>
        <tr class="form-field">
          <th scope="row"><label for="reservation_page"><strong>Reservation page -<br />if using single reservation form:</strong></label></th>
          <td>
            <?php wp_dropdown_pages(array('name' => 'reservation_page', 'selected' => get_option('reservation_page'), 'show_option_none' => 'None')); ?>
          </td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input type="submit" value="Save" class="button-primary" />
    </p>
  </form>
</div><!-- .wrap -->