<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see           https://docs.woocommerce.com/document/template-structure/
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       3.9.0
 */

// TODO további termékei
// TODO lábléc menükből
// TODO Minta termék kosárba
// TODO gyik oldalsáv
// TODO https://www.paperstories.hu/termek/em01-0039-c/
// TODO function.php function woocommerce_quantity_input
// TODO számlázz.hu összekötés
// TODO GLS összekötés
// TODO számlázzzhuról levél, automatikus számlázás kell vagy manuális és Agent kulcs kell vagy hozzáférés

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

<section class="related products">
    <?php
    global $product;

    /** @var WC_Product_Query $loop */
    $sameNameProducts = new WC_Product_Query([
        'post_type'      => 'product',
        'post_status' => 'publish',
        'post__not_in' => [$product->get_id()],
        'title'    => $product->get_title(),
        'posts_per_page' => -1,
        'orderby' => 'date',
        'order' => 'ASC',
    ]);
    $sameNameProducts = $sameNameProducts->get_products();
    if (!empty($sameNameProducts)) : ?>
        <h2><span>A kollekció</span> további darabjai</h2>
            <ul class="related-product cart-btn-below-img">
                <?php foreach ( $sameNameProducts as $related_product ) : ?>
                    <li>
                        <?php
                        $product1 = wc_get_product( $related_product->get_id() );
                        if ( $product1->is_on_sale() ) :
                            ?>
                            <span class="onsale"><i class="dt-icon-the7-magn-004-12" aria-hidden="true"></i></span>
                        <?php
                        endif;
                        ?>
                        <a class="product-thumbnail" href="<?php echo esc_url( $product1->get_permalink() ); ?>">
                            <?php echo $product1->get_image(); ?>
                        </a>
                        <div class="product-content">
                            <a class="product-title" href="<?php echo esc_url( $product1->get_permalink() ); ?>">
                                <?php echo $product1->get_name(); ?>
                            </a>

                            <?php
                            $product_cat_name = get_the_terms( $product1->get_id(), 'product_cat' )['0']->name;
                            echo '<div class="prod-cat-box"><span class="prod-category">'.$product1->get_categories().'</span>';
                            echo '<span class="prod-attribute">'.$product1->get_attribute( 'stilus' ).'</span></div>';
                            ?>

                            <span class="price"><?php //echo $product->get_price_html(); ?></span>

                            <?php
                            if ( wc_review_ratings_enabled() ) {
                                echo wc_get_rating_html( $product1->get_average_rating() );
                            }
                            //if ( presscore_config()->get( 'product.related.show_cart_btn' ) ) {
                            //echo '<div class="woo-buttons">' . dt_woocommerce_get_product_add_to_cart_icon() . '</div>';
                            //}
                            ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif;
    wp_reset_postdata();
    ?>
</section>


<?php
if ( $related_products ) : ?>

    <section class="related products">

        <?php
        $heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'the7mk2' ) );

        if ( $heading ) :
            ?>
            <h2><span>Teszthet még</span> hasonló termékek<?php// echo esc_html( $heading ); ?></h2>
        <?php endif; ?>

        <ul class="related-product cart-btn-below-img">

            <?php presscore_config()->set( 'product.preview.icons.show_cart', true ) ?>

            <?php foreach ( $related_products as $related_product ) : ?>
                <li>
                    <?php
                    global $product;

                    $product = wc_get_product( $related_product->get_id() );
                    if ( $product->is_on_sale() ) :
                        ?>
                        <span class="onsale"><i class="dt-icon-the7-magn-004-12" aria-hidden="true"></i></span>
                    <?php
                    endif;
                    ?>
                    <a class="product-thumbnail" href="<?php echo esc_url( $product->get_permalink() ); ?>">
                        <?php echo $product->get_image(); ?>
                    </a>
                    <div class="product-content">
                        <a class="product-title" href="<?php echo esc_url( $product->get_permalink() ); ?>">
                            <?php echo $product->get_name(); ?>
                        </a>

                                <?php
                                $product = wc_get_product();
                               $product_cat_name = $term->name;
                               echo '<div class="prod-cat-box"><span class="prod-category">'.$product->get_categories().'</span>';
                               echo '<span class="prod-attribute">'.$product->get_attribute( 'stilus' ).'</span></div>';
                                ?>

                        <span class="price"><?php //echo $product->get_price_html(); ?></span>

                        <?php
                        if ( wc_review_ratings_enabled() ) {
                            echo wc_get_rating_html( $product->get_average_rating() );
                        }
                        //if ( presscore_config()->get( 'product.related.show_cart_btn' ) ) {
                            //echo '<div class="woo-buttons">' . dt_woocommerce_get_product_add_to_cart_icon() . '</div>';
                        //}
                        ?>
                    </div>
                </li>

            <?php endforeach; ?>

        </ul>

    </section>

<?php endif;

wp_reset_postdata();
