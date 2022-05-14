<?php
/**
 * Product quantity inputs
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/quantity-input.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;


if ( $max_value && $min_value === $max_value ) {
	?><div class="quantity hidden"><input type="hidden" id="<?php echo esc_attr( $input_id ); ?>" class="qty" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $min_value ); ?>" /></div><?php
} elseif ( ! empty( $max_value ) && ! empty( $step ) ) {

	global $woocommerce, $product;


	?><div class="quantity">


		<div class="tooltip qty-tooltip">
			<span class="tooltiptext" style="width: 160px;">Rendelési minimum 20 darab. Áraing az ÁFÁ-t tartalmazzák.</span>
		</div>


		<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></label>
		<select name="<?php echo esc_attr( $input_name ); ?>" class="qty" id="<?php echo esc_attr( $input_id ); ?>"><?php
			for ( $i = $min_value; $i <= $max_value; $i = $i + $step ) :
				?><option value="<?php echo absint( $i ); ?>" <?php selected( $input_value, absint( $i ) ); ?>>

					<?php
					echo sprintf( _n( '%d db', '%d db | ', $i, 'woocommerce' ), $i );
					echo $product->get_price();
					echo sprintf( _n( ' db', ' Ft/db | ', $i, 'woocommerce' ), $i );
					echo $product->get_price()* $i.' Ft';
				 	?>
				</option>

				<?php

				if ( $i == 1 ) $i--;
			endfor;
		?></select>
	</div><?php
} else {
	?><div class="quantity">
		<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>"><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></label>
		<input type="number" id="<?php echo esc_attr( $input_id ); ?>" class="input-text qty text" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( 0 < $max_value ? $max_value : '' ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'woocommerce' ) ?>" size="4" pattern="<?php echo esc_attr( $pattern ); ?>" inputmode="<?php echo esc_attr( $inputmode ); ?>" aria-labelledby="<?php echo ! empty( $args['product_name'] ) ? sprintf( esc_attr__( '%s quantity', 'woocommerce' ), $args['product_name'] ) : ''; ?>" />
	</div><?php
}
