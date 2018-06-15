<?php
$errors = []; $success = false;
if(isset($_POST['reserve']) && !defined("RESERVATION_HANDLED")){
    function checkForErrors(){
        global $wpdb;
        $errors = [];
        if(!isset($_POST['vardas']) || empty($_POST['vardas'])) $errors['name'] = "Įveskite savo vardą ir pavardę";
        if(!isset($_POST['phone']) || empty($_POST['phone'])) $errors['phone'] = "Įveskite telefono numerį";
        if(!isset($_POST['pickupDate']) || empty($_POST['pickupDate'])) $errors['pickupDate'] = "Pasirinkite pasiėmimo datą";
        if(!isset($_POST['returnDate']) || empty($_POST['returnDate'])) $errors['returnDate'] = "Pasirinkite grąžinimo datą";
        if(!isset($_POST['products']) || empty($_POST['products']) || (count($_POST['products']) == 1 && $_POST['products'][0] < 0)) $errors['products'] = "Pasirinkite bent vieną produktą";

        if(!isset($errors['pickupDate']) && !isset($errors['returnDate']) && strtotime($_POST['pickupDate']) > strtotime($_POST['returnDate']))
            $errors['returnDate'] = "Grąžinimo data negali būti ankščiau nei paėmimo";

        $checked = []; $productai = [];
        foreach($_POST['products'] as $product){
            if(in_array($product, $checked)) $errors['products'] = "Negalima rezervuoti dviejų vienodų prekių";
            array_push($checked, $product);

            //Tikriname ar nėra rezervacijų pasirinktomis dienomis
            $product = $wpdb->_real_escape($product);
            $pickUpDate = $wpdb->_real_escape($_POST['pickupDate']);
            $returnDate = $wpdb->_real_escape($_POST['returnDate']);
            $reservation = $wpdb->get_row("SELECT `date_from`,`date_to` FROM `". $wpdb->prefix . "rent_calendar` WHERE (`item_ids` = '{$product}' OR `item_ids` LIKE '{$product},%' OR `item_ids` LIKE '%,{$product}' OR `item_ids` LIKE '%,{$product},%') AND (`date_from` BETWEEN '{$pickUpDate}' AND '{$returnDate}' OR `date_to` BETWEEN '{$pickUpDate}' AND '{$returnDate}')");

            if($reservation){
                $product = $wpdb->get_var("SELECT `name` FROM `". $wpdb->prefix . "rent_calendar_products` WHERE `id` = '{$product}'");
                array_push($productai, $product);
            }
        }

        if(count($productai) > 0) {
	        $productai = implode( ", ", $productai );

	        $pos = strrpos( $productai, "," );
	        if ( $pos !== false ) {
		        $productai = substr_replace( $productai, " ir", $pos, strlen( "," ) );
	        }

	        $errors['products'] = "Apgailestaujame, bet <b>" . $productai . "</b> jūsų pasirinktomis datomis jau yra rezervuotas(-a)";
        }

        if(get_option("reservation_captcha") == "yes"){
            $ch = curl_init("https://www.google.com/recaptcha/api/siteverify");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                "secret" => get_option("reservation_captcha_secret_key"),
                "response" => isset($_POST['g-recaptcha-response'])?$_POST['g-recaptcha-response']:""
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if($response && $json = json_decode($response)){
                if($json->success == false)
                    $errors['captcha'] = "Nepavyko patvirtinti";
            }
        }

        return $errors;
    }

    $errors = checkForErrors();
    if(empty($errors)){
        $wpdb->insert($wpdb->prefix .'rent_calendar', [
            'date_from' => $_POST['pickupDate'],
            'date_to' => $_POST['returnDate'],
            'name' => $_POST['vardas'],
            'phone' => $_POST['phone'],
            'item_ids' => implode(",", $_POST['products']),
            'item_type' => 'single',
            'status' => 'pending'
        ]);
        if($wpdb->last_error == ""){
	        echo "<span class='success'>Jūsų rezervacija sėkmingai atlikta! Artimiausiu metu su jumis susisieksime</span>";
	        $success = true;

        } else {
            echo "<span class='has-error'>Įvyko klaida saugant duomenų bazėje. Susisiekite telefonu!</span>";
        }
    }

    define("RESERVATION_HANDLED", true);
}

if(!$success):
?>

<form method="POST" action="<?php echo get_permalink(get_option('reservation_page')); ?>">
    <?php $addClass = isset($errors['name'])?"has-error":""; ?>
    <label for="name" class="<?php echo $addClass; ?>">Vardas Pavardė<span class="required">*</span> :</label>
    <input id="name" type="text" name="vardas" value="<?php echo isset($_POST['vardas'])?$_POST['vardas']:""; ?>">
    <?php echo isset($errors['name'])?"<span class='has-error'>". $errors['name'] ."</span>":""; ?>

	<?php $addClass = isset($errors['phone'])?"has-error":""; ?>
    <label for="phone" class="<?php echo $addClass; ?>">Telefono nr.<span class="required">*</span> :</label>
    <input id="phone" type="text" name="phone" value="<?php echo isset($_POST['phone'])?$_POST['phone']:""; ?>">
	<?php echo isset($errors['phone'])?"<span class='has-error'>". $errors['phone'] ."</span>":""; ?>

    <div class="row">
        <div class="col-50">
	        <?php $addClass = isset($errors['pickupDate'])?"has-error":""; ?>
            <label for="pickupDate" class="<?php echo $addClass; ?>">Pasiėmimo data<span class="required">*</span> :</label>
            <input id="pickupDate" type="text" value="<?php echo isset($_POST['pickupDate'])?$_POST['pickupDate']:""; ?>" name="pickupDate" data-beatpicker-id="pickupPicker" data-beatpicker="true" data-beatpicker-disable="{from:[<?php echo date("Y"); ?>,<?php echo date("n"); ?>,<?php echo date("j"); ?>],to:'<'}" data-beatpicker-module="today,clear,footer">
	        <?php echo isset($errors['pickupDate'])?"<span class='has-error'>". $errors['pickupDate'] ."</span>":""; ?>
        </div>
        <div class="col-50">
	        <?php $addClass = isset($errors['returnDate'])?"has-error":""; ?>
            <label for="returnDate" class="<?php echo $addClass; ?>">Grąžinimo data<span class="required">*</span> :</label>
            <input id="returnDate" type="text"  value="<?php echo isset($_POST['returnDate'])?$_POST['returnDate']:""; ?>" name="returnDate" data-beatpicker-id="returnPicker" data-beatpicker="true" data-beatpicker-disable="{from:[<?php echo date("Y"); ?>,<?php echo date("n"); ?>,<?php echo date("j"); ?>],to:'<'}" data-beatpicker-module="today,clear,footer">
	        <?php echo isset($errors['returnDate'])?"<span class='has-error'>". $errors['returnDate'] ."</span>":""; ?>
        </div>
    </div>

	<?php $addClass = isset($errors['products'])?"has-error":""; ?>
    <?php if(isset($options['lock']) && $options['lock'] == '1' && isset($options['products']) && !empty($options['products'])): ?>
        <?php 
            if(is_array($options['products'])) $productsToSelect = $options['products'];
            else $productsToSelect = explode(",", $options['products']);
        ?>
        <?php foreach ($productsToSelect as $productToSelect): ?>
            <input type="hidden" name="products[]" value="<?php echo $productToSelect; ?>">
        <?php endforeach; ?>
    <?php else: ?>
    <label for="products" class="<?php echo $addClass; ?>">Įranga:</label>
    <div class="input-group equipment-list">
        <?php
            $productsToSelect = NULL;
            if(isset($options['products']) && !empty($options['products'])){
                if(is_array($options['products'])) $productsToSelect = $options['products'];
                else $productsToSelect = explode(",", $options['products']);
            };

            if($productsToSelect) {
	            foreach ( $productsToSelect as $key => $productToSelect ) {
		            $last = $key == count( $productsToSelect ) - 1 ? true : false;
		            generateProductLine($categories, $products, $productToSelect, $last );
	            }
            } else {
                generateProductLine($categories, $products);
            }
        ?>
    </div>
	    <?php echo isset($errors['products'])?"<span class='has-error'>". $errors['products'] ."</span>":""; ?>
    <?php endif; ?>

    <?php if(get_option("reservation_captcha") == "yes"): ?>
	    <?php $addClass = isset($errors['captcha'])?"has-error":""; ?>
        <br>
        <div class="g-recaptcha" data-sitekey="<?php echo get_option("reservation_captcha_site_key"); ?>"></div>
	    <?php echo isset($errors['captcha'])?"<span class='has-error'>". $errors['captcha'] ."</span>":""; ?>
    <?php endif; ?>

    <br>
    <button type="submit" name="reserve">Rezervuoti</button>
    <?php if(get_option("reservation_show_prices") == "yes"): ?>
        Preliminari kaina: <b id="final-price">0.00€</b>
    <?php endif; ?>
</form>


<script>
    $(document).ready(function () {
        init_date_listeners();
        init_listeners();
        calculate_price();
    });

    var temp;

    function init_date_listeners(){
        pickupPicker.on("select", function (data) {
            $("input[data-beatpicker-id=returnPicker]").data('beatpicker-disable', "{from:["+data.dateObj.getFullYear()+","+(data.dateObj.getMonth()+1)+","+data.dateObj.getDate()+"],to:'<'}");

            if(returnPicker.getSelectedStartDate() === null || returnPicker.getSelectedStartDate().getTime() < data.dataObj.getTime())
                returnPicker.selectDate(data.dateObj);

            initializeBitCal();
            init_date_listeners();
            calculate_price();
        });

        returnPicker.on("select", calculate_price);
    }

    function init_listeners() {
        $(".add-row-btn").unbind().click(function (e) {
            $(this).parent().parent().clone().appendTo($(".equipment-list"));
            $(".equipment-list .input-row:not(:last-child)").find(".add-row-btn").after("<button type='submit' class='remove-row-btn'>-</button>").remove();
            e.preventDefault();
            init_listeners();
        });

        $(".remove-row-btn").unbind().click(function (e) {
            $(this).parent().parent().remove();
            calculate_price();
            e.preventDefault();
        });

        $(".productsDropdown").unbind().change(calculate_price);
    }

    function calculate_price(){
        let final_price = 0;
        $(".productsDropdown").each(function(){
            let price = $(this).find('option:selected').data('price');

            if(typeof price !== "undefined")
                final_price += parseInt(price.toString());
        });

        let oneDay = 24*60*60*1000; // hours*minutes*seconds*milliseconds
        let diffDays = 0;
        if($("#pickupDate").val() !== "" && $("#returnDate").val() !== "") {
            let firstDate = new Date($("#pickupDate").val().toString());
            let secondDate = new Date($("#returnDate").val().toString());

            diffDays = Math.round(Math.abs((firstDate.getTime() - secondDate.getTime()) / (oneDay)));

	        <?php if(get_option("reservation_price_method") == "days"): ?>
                diffDays += 1;
	        <?php endif; ?>
        }

        if(diffDays === 0) diffDays = 1;

        final_price *= diffDays;

        $("#final-price").html(final_price.toFixed(2)+"€");
    }
</script>

<?php
endif;
//Helper functions

/**
 * @param array $categories
 * @param array $products
 * @param int $selectedProduct
 * @param bool $last
 *
 * Generates a dropdown with specified product selected and  "+" / "-" button depending on $last parameter
 */
function generateProductLine($categories = [], $products = [], $selectedProduct = 0, $last = true){
    echo '<div class="input-row">';
    echo '<select name="products[]" class="productsDropdown">';
    echo '<option value="-1">-- Pasirinkite --</option>';
    foreach($categories as $category) {
        echo '<optgroup label="'. $category->name .'">';
	    foreach ( $products as $product ) {
		    $price = NULL;
	        if(get_option("reservation_show_prices") == "yes") {
		        $price = number_format( $product->price, 2 );
		        $priceText = " (". $price ."€)";
	        }

		    if($product->category == $category->id) echo '<option value="' . $product->id . '" ' . ( $selectedProduct == $product->id ? 'selected' : '' ) . ' data-price="'. $price .'">' . $product->name . $priceText .'</option>';
	    }
	    echo '</optgroup>';
    }
    echo '</select>';

    if($last){
        echo '<span class="input-group-addon">
                <button class="add-row-btn" type="submit">+</button>
            </span>';
    } else {
	    echo '<span class="input-group-addon">
                <button class="remove-row-btn" type="submit">-</button>
            </span>';
    }
    echo '</div>';
}
?>