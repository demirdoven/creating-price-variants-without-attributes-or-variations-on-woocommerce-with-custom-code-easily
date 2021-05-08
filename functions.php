<?php
function carpet_details_metabox(){
	add_meta_box( 'carpet_details_metabox_wrapper', 'Details', 'carpet_details_metabox_callback', 'product', 'normal', 'low' );
}
add_action( 'add_meta_boxes' , 'carpet_details_metabox' );

function carpet_details_metabox_callback( $post ){
	wp_nonce_field( basename(__FILE__), 'sample_nonce' );
	?>
	<style>
	ul.details_list li {
		background: #ececec;
		padding: 10px 23px 20px;
		display: inline-block;
		margin-bottom: 20px;
	}
	ul.details_list button {
		background: #3F51B5;
		color: #fff;
		border: 0;
		padding: 5px 15px;
		display: inline-block;
		border-radius: 2px;
		margin-left: 2px;
		cursor: pointer;
	}
	ul.details_list h2 {
		font-size: 26px!important;
		font-weight: bold!important;
		padding-left: 1px!important;
	}
	ul.details_list table.sizes,
	ul.details_list table.underlay_types {
		width: 578px;
	}
	ul.details_list .sizes th,
	ul.details_list .underlay_types th {
		background: #484848;
		color: #fff;
		padding: 9px 10px;
		text-align: center;
	}
	ul.details_list .sizes td,
	ul.details_list .underlay_types td {
		padding: 4px 10px;
		background: #dedede;
	}
	ul.details_list .sizes td input,
	.underlay_types td input {
		width: 100%;
	}
	ul.details_list .remove {
		cursor: pointer;
	}
	</style>
	<ul class="details_list">
		<li>
			<h2>SIZES</h2>
			<table class="sizes">
				<thead>
					<th>Label</th>
					<th>Width (m)</th>
					<th>Length (m)</th>
					<th></th>
				</thead>
				<?php
				$sizes = get_post_meta( $post->ID, 'size_data', true );
				if ( isset($sizes['label']) ){
					for( $i=0; $i<count($sizes['label']); $i++ ){
						?>
						<tr>
							<td><input type="text" name="sizes[label][]" value="<?php echo esc_attr($sizes['label'][$i]); ?>" placeholder="Label"/></td>
							<td><input type="number" min="1" name="sizes[width][]" value="<?php echo esc_attr($sizes['width'][$i]); ?>" placeholder="Width"/></td>
							<td><input type="number" min="1" name="sizes[length][]" value="<?php echo esc_attr($sizes['length'][$i]); ?>" placeholder="Length"/></td>
							<td><span class="remove dashicons dashicons-trash" title="Remove"></span></td>
						</tr>
						<?php
					}
				}
				?>
			</table>
				<div>
					<table>
						<tr class="master" style="display: none;">
							<td><input type="text" name="sizes[label][]" value="" placeholder="Label" autocomplete="off"/></td>
							<td><input type="number" min="1" name="sizes[width][]" value="" placeholder="Width" autocomplete="off"/></td>
							<td><input type="number" min="1" name="sizes[length][]" value="" placeholder="Length" autocomplete="off"/></td>
							<td><span class="remove dashicons dashicons-trash" title="Remove"></span></td>
						</tr>
					</table>
				</div>
			<button type="button" class="add_new_button">Add New</button>
		</li>
		<li>
			<h2>UNDERLAYS</h2>
			<table class="underlay_types">
				<thead>
					<th>Label</th>
					<th>Price per m²</th>
					<th></th>
				</thead>
				<?php
				$underlay_types = get_post_meta( $post->ID, 'underlay_data', true );
				if (isset($underlay_types['label'])){
					for( $i=0; $i<count($underlay_types['label']); $i++ ){
						?>
						<tr>
							<td><input type="text" name="underlay_types[label][]" value="<?php echo esc_attr($underlay_types['label'][$i]); ?>" placeholder="Label"/></td>
							<td><input type="number" min="0" name="underlay_types[price][]" value="<?php echo esc_attr($underlay_types['price'][$i]); ?>" placeholder="Price per m2"/></td>
							<td><span class="remove dashicons dashicons-trash" title="Remove"></span></td>
						</tr>
						<?php
					}
				}
				?>
			</table>
			<div>
				<table>
					<tr class="master" style="display: none;">
						<td><input type="text" name="underlay_types[label][]" value="" placeholder="Label" autocomplete="off"/></td>
						<td><input type="number" min="0" name="underlay_types[price][]" value="" placeholder="Price per m2" autocomplete="off"/></td>
						<td><span class="remove dashicons dashicons-trash" title="Remove"></span></td>
					</tr>
				</table>
			</div>
			<button type="button" class="add_new_button">Add New</button>
		</li>
		
	</ul>
	<script>
	jQuery(function ($){
		
		$(document).on('click', '.add_new_button', function(){
			var master = $(this).parent().find('.master').html();
			$(this).siblings('table').append('<tr>'+master+'</tr>');
		});
		$(document).on('click', '.remove', function(){
			$(this).parent().parent().remove();
		});
		
	});
	</script>
	<?php
}
function carpet_details_metabox_save( $post_id ) {
	
	$is_autosave = wp_is_post_autosave($post_id);
	$is_revision = wp_is_post_revision($post_id);
	$is_valid_nonce = ( isset($_POST['sample_nonce']) && wp_verify_nonce($_POST['sample_nonce'], basename( __FILE__ )) ) ? 'true' : 'false';
	if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
		return;
	}
	if ( 'product' != $_POST['post_type'] ){
		return;
	}
	if ( $_POST['sizes'] ){
		$size_data = array();
		for ($i = 0; $i < count( $_POST['sizes']['label'] ); $i++ ){
			if ( '' != $_POST['sizes']['label'][$i]){
				$size_data['label'][] = $_POST['sizes']['label'][ $i ];
				$size_data['width'][] = $_POST['sizes']['width'][ $i ];
				$size_data['length'][] = $_POST['sizes']['length'][ $i ];
			}
		}
		if ( $size_data ){
			update_post_meta( $post_id, 'size_data', $size_data );
		}else{
			delete_post_meta( $post_id, 'size_data' );
		}
	}else{
		delete_post_meta( $post_id, 'size_data' );
	}
	if ( $_POST['underlay_types'] ){
		$underlay_data = array();
		for ($i = 0; $i < count( $_POST['underlay_types']['label'] ); $i++ ){
			if ( '' != $_POST['underlay_types']['label'][$i]){
				$underlay_data['label'][]  = $_POST['underlay_types']['label'][ $i ];
				$underlay_data['price'][]  = $_POST['underlay_types']['price'][ $i ];
			}
		}
		if ( $underlay_data ){
			update_post_meta( $post_id, 'underlay_data', $underlay_data );
		}else{
			delete_post_meta( $post_id, 'underlay_data' );
		}
	}else{
		delete_post_meta( $post_id, 'underlay_data' );
	}
	
}
add_action( 'save_post', 'carpet_details_metabox_save' );

function add_calculator_to_single_product_page() {
	?>
	<style>
	.calculator_wrapper {
		background: #f0f0f0;
		padding: 8px;
		display: inline-block;
	}
	table.calculator_table {
		border: 0;
		margin: 0;
		width: auto;
	}
	.calculator_table tr {
		border: 0;
	}
	.calculator_table td {
		border: 0;
		padding: .8em 1.2em .2em;
	}
	.calculator_table tr:last-child td {
		padding: .8em 1.2em .8em;
	}
	.custom_size_inputs_wrapper > label {
		background: #e7e7e7;
		padding: 10px;
		display: flex;
		justify-content: space-between;
		margin-bottom: 4px;
		border-radius: 4px;
		align-items: center;
	}
	.custom_size_inputs input {
		line-height: 1;
		height: 30px;
		font-size: 16px;
		width: 90px;
		background-color: #fff;
	}  
	.custom_size_inputs input:focus {
		background-color: #fff;
	}
	table.calculator_table tr td select {
		width: 100%;
	}
	</style>
	<?php
	global $product;
	$product_id = $product->get_id();
	$sizes = get_post_meta( $product_id, 'size_data', true );
	$underlay_types = get_post_meta( $product_id, 'underlay_data', true );

	if ( $sizes && !empty($sizes) ){
		echo '<div class="calculator_wrapper">';
		echo '<table class="calculator_table">';
		echo '<tr>';
		echo '<td><label>Size</label></td>';
		echo '<td><select name="size_label">';
		echo '<option value="0">Choose size</option>';
		for( $i = 0; $i < count( $sizes['label'] ); $i++ ){
			$width = (float)$sizes['width'][$i];
			$length = (float)$sizes['length'][$i];
			$meter_sq = $width * $length;
			?>
			<option value="<?php echo esc_attr($meter_sq); ?>"><?php echo esc_attr($sizes['label'][$i]); ?> (<?php echo esc_attr($sizes['width'][$i]); ?> x <?php echo esc_attr($sizes['length'][$i]); ?> )</option>
			<?php
		}
		echo '<option value="custom_size">Custom size</option>';
		echo '</select></td>';
		echo '</tr>';
		?>
		<tr class="custom_size_inputs" style="display: none;">
			<td colspan="2">
				<div class="custom_size_inputs_wrapper">
					<label for="width_inp">
						Width (m)
						<input type="number" id="width_inp" name="width_inp" value="" min="1"/>
					</label>
					<label for="length_inp">
						Length (m)
						<input type="number" id="length_inp" name="length_inp" value="" min="1"/>
					</label>
					<label for="total_m2">
						Total (&#13217;)
						<input type="number" id="total_m2" name="total_m2" value="" min="1"/>
					</label>
				</div>
			</td>
		</tr>
		<?php
		
		if ( $underlay_types && !empty( $underlay_types ) ){
			echo '<tr>';
			echo '<td><label>Underlay</label></td>';
			echo '<td><select name="underlay_type">';
			echo '<option value="">No underlay</option>';
			for( $i = 0; $i < count( $underlay_types['label'] ); $i++ ){
				$price = (float)$underlay_types['price'][$i];
				?>
				<option value="<?php echo esc_attr($price); ?>"><?php echo esc_attr($underlay_types['label'][$i]); ?></option>
				<?php
			}
			echo '</select></td>';
			echo '</tr>';
		}
		
		echo '</table>';
		echo '</div>';
		echo sprintf('<div id="product_total_price">%s %s</div>', 'Total price:', '<span class="price">'.get_woocommerce_currency_symbol().'0</span>');	
	}
	?>
	<script>
	jQuery(function ($){	
		var productPrice = '<?php echo $product->get_price(); ?>';
		var currency = '<?php echo get_woocommerce_currency_symbol(); ?>';
		
		$('input[name="width_inp"], input[name="length_inp"]').on('keyup change', function(){
			if( $('select[name="size_label"]').val() == 'custom_size' ){
				var width = $('input[name="width_inp"]').val();
				var length = $('input[name="length_inp"]').val();
				if( width == '' ){
					width = 1;
				}
				if( length == '' ){
					length = 1;
				}
				width = parseFloat(width);
				length = parseFloat(length);
				var top = width*length;
				$('input[name="total_m2"]').val(width*length);
				var underlayPrice = 0;
				var underlayPricePerSq = $('select[name="underlay_type"]').val();
				if( underlayPricePerSq != '' ){
					underlayPrice = width*length*parseFloat(underlayPricePerSq);
				}
				var newPrice = (width*length*productPrice)+underlayPrice;
				$('#product_total_price .price').html( currency + newPrice.toFixed(2));
				$('input[name="final_price"]').val(newPrice.toFixed(2));
				$('input[name="size_total_metersq"]').val( width*length );
				$('input[name="length"]').val( length );
				$('input[name="width"]').val( width );
			}
		});
		
		$('input[name="total_m2"]').on('keyup change', function(){
			$('input[name="width_inp"]').val('');
			$('input[name="length_inp"]').val('');
			var topm2 = $('input[name="total_m2"]').val();
			topm2 = parseFloat(topm2);
			var underlayPrice = 0;
			var underlayPricePerSq = $('select[name="underlay_type"]').val();
			if( underlayPricePerSq != '' ){
				underlayPrice = topm2*parseFloat(underlayPricePerSq);
			}
			var newPrice = (topm2*productPrice)+underlayPrice;
			$('#product_total_price .price').html( currency + newPrice.toFixed(2));
			$('input[name="final_price"]').val(newPrice.toFixed(2));
			$('input[name="size_total_metersq"]').val( topm2 );
		});
		
		$('select[name="underlay_type"]').on('change', function(){
			var total_m2 = '';
			if( $('select[name="size_label"]').val() == 'custom_size' ){
				total_m2 = $('input[name="total_m2"]').val();
			}else{
				total_m2 = $('select[name="size_label"]').val();
			}
			total_m2 = parseFloat(total_m2);
			var underlayPrice = 0;
			var underlayPricePerSq = $('select[name="underlay_type"]').val();
			if( underlayPricePerSq != '' ){
				underlayPrice = total_m2*parseFloat(underlayPricePerSq);
			}
			var newPrice = (total_m2*productPrice)+underlayPrice;
			$('#product_total_price .price').html( currency + newPrice.toFixed(2));
			$('input[name="final_price"]').val(newPrice.toFixed(2));
			$('input[name="underlay"]').val($('select[name="underlay_type"] option:selected').text());
			
		});
		
		$('select[name="size_label"]').on('change', function(){
			if( $(this).val() == 'custom_size' ){
				$('.custom_size_inputs').show();
				$('#product_total_price .price').html( currency + '0');
				$('input[name="final_price"]').val(0);
			}else if( $(this).val() == '0' ){
				$('#product_total_price .price').html( currency + '0');
				$('input[name="final_price"]').val(0);
			}else{
				$('.custom_size_inputs').hide();
				$('input[name="width_inp"]').val('');
				$('input[name="length_inp"]').val('');
				$('input[name="total_m2"]').val('');
				var total_m2 = $(this).val();
				total_m2 = parseFloat(total_m2);
				var underlayPrice = 0;
				var underlayPricePerSq = $('select[name="underlay_type"]').val();
				if( underlayPricePerSq != '' ){
					underlayPrice = total_m2*parseFloat(underlayPricePerSq);
				}
				var newPrice = (total_m2*productPrice)+underlayPrice;
				$('#product_total_price .price').html( currency + newPrice.toFixed(2));
				$('input[name="final_price"]').val(newPrice.toFixed(2));
			}

			var selectedSizeLabel = $('select[name="size_label"] option:selected').text();
			$('input[name="size_label"]').val(selectedSizeLabel);
			$('input[name="size_total_metersq"]').val( total_m2 );
		});
	});
	</script>
	<?php	
}
add_action('woocommerce_single_product_summary', 'add_calculator_to_single_product_page', 15);


function add_m2_to_prices( $price, $product ){
	return $price.' / m²';
}
add_filter( 'woocommerce_get_price_html', 'add_m2_to_prices', 100, 2 );

function modify_add_to_cart_button_for_shop_and_archive( $button, $product  ) {
	// You can add conditions as you wish. For ex. for spesific products or categories only
    if( $product->is_type('variable') ){
		return $button;
	}else{
		return '<a class="button" href="'.$product->get_permalink().'">View product</a>';
	}
}
add_filter( 'woocommerce_loop_add_to_cart_link', 'modify_add_to_cart_button_for_shop_and_archive', 10, 2 );

function add_hidden_inputs_to_single_product() {
	$inputs = array('final_price', 'size_label', 'width', 'length', 'size_total_metersq', 'underlay' );
	for( $i=0; $i<count($inputs); $i++ ){
		$input = $inputs[$i];
		echo '<input type="hidden" id="'.esc_attr($input).'" name="'.esc_attr($input).'" class="'.esc_attr($input).'" value="">';
	}
}
add_action( 'woocommerce_before_add_to_cart_button', 'add_hidden_inputs_to_single_product', 11, 0 );

function set_the_final_price( $cart ) {
    foreach ($cart->get_cart() as $cart_item){
        if( isset($cart_item['final_price']) ){
            $cart_item['data']->set_price($cart_item['final_price']);
        }
    }
}
add_action( 'woocommerce_before_calculate_totals', 'set_the_final_price', 30, 1 );

function add_cart_item_data_from_values( $cart_item_meta, $product_id ) {
	
	$custom_data = array();
	$post_array = array('size_label', 'width', 'length', 'size_total_metersq', 'underlay' );
	
	for( $i=0; $i<count($post_array); $i++ ){
		$p = $post_array[$i];
		$custom_data[$p] = isset($_POST[$p]) ? sanitize_text_field($_POST[$p]) : '' ;
	}
	
	$cart_item_meta['custom_data'] = $custom_data ;
	
	if( isset( $_POST['final_price'] ) && ! empty( $_POST['final_price'] )  ) {
        $cart_item_meta['final_price'] = (float) sanitize_text_field( $_POST['final_price'] );
        $cart_item_meta['unique_key'] = md5( microtime().rand() );
    }

	return $cart_item_meta;
}
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data_from_values', 25, 2 );

function display_custom_data_on_cart_and_checkout ( $other_data, $cart_item ) {
	
	if ( isset($cart_item['custom_data'] ) ) {
		
		$custom_data = $cart_item['custom_data'];
		
		$other_data[] = array(
			'name' => 'Size',
			'display' => $custom_data['size_label'] 
		);
		if( $custom_data['width'] != '' ){
			$other_data[] = array( 
				'name' => 'Width',
				'display' => $custom_data['width'].' m'
			);
		}
		if( $custom_data['length'] != '' ){
			$other_data[] = array( 
				'name' => 'Length',
				'display' => $custom_data['length'].' m'
			);
		}
		$other_data[] = array( 
			'name' => 'Total',
			'display' => $custom_data['size_total_metersq'].' m2'
		);
		$other_data[] = array( 
			'name' => 'Underlay',
			'display' => $custom_data['underlay']
		);
		
	}
	return $other_data;
}
add_filter( 'woocommerce_get_item_data', 'display_custom_data_on_cart_and_checkout' , 25, 2 );

function add_metadata_to_the_items_on_the_order( $item, $cart_item_key, $values, $order ) {
	$custom_data = $values['custom_data'];

	if( $custom_data && !empty($custom_data)){
		foreach( $custom_data as $key=>$value ){
			$label = $key;
			if($key=='size_label'){
				$label = 'Size';
			}
			if($key=='size_total_metersq'){
				$label = 'Total (m2)';
			}
			if($key=='underlay'){
				$label = 'Underlay';
			}
			if($key=='width'){
				$label = 'Width (m)';
			}
			if($key=='length'){
				$label = 'Length (m)';
			}
			$item->update_meta_data( $label, $value );
		}
	}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'add_metadata_to_the_items_on_the_order', 20, 4 );
?>
