<?php
/**
 * Bottom bar template
 *
 * @package vogue
 * @since   1.0.0
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="custom-footer-content">
	<div class="custom-footer-content-row">
		<div class="custom-footer-content-column social-media">
			<a title="Instagram page opens in new window" href="https://www.instagram.com/paperstories.hu/" target="_blank" class="instagram"><span class="soc-font-icon"></span><span class="screen-reader-text">Instagram page opens in new window</span></a>

			<!--a title="Pinterest page opens in new window" href="/" target="_blank" class="pinterest"><span class="soc-font-icon"></span><span class="screen-reader-text">Pinterest page opens in new window</span></a-->

			<a title="Facebook page opens in new window" href="https://www.facebook.com/paperstories.hu" target="_blank" class="facebook"><span class="soc-font-icon"></span><span class="screen-reader-text">Facebook page opens in new window</span></a>
		</div>

		<div class="custom-footer-content-column bordered-item footer-logo"><a href="/"><img class=" preload-me" src="/wp-content/uploads/2022/02/paperstories-logo.png" srcset="/wp-content/uploads/2022/02/paperstories-logo.png 300w" sizes="300px" alt="Paper Stories" width="300" height="78"></a>
		</div>

		<div class="custom-footer-content-column footer-contact">
			<p class="paper_footer_phone mb-2">ÜGYFÉLSZOLGÁLAT | <a href="tel:+3630789963">+3630 789963</a></p>
			<p class="paper_footer_phone mb-0">HÉTFŐ-PÉNTEK | 9.00-17.00</p>
		</div>
	</div>

	<div class="custom-footer-img-row">
		<img src="/wp-content/uploads/2022/02/mastercard-icon.svg" alt="">
		<img src="/wp-content/uploads/2022/02/maestro-icon.svg" alt="">
		<img src="/wp-content/uploads/2022/02/paypal-icon.svg" alt="">
		<img src="/wp-content/uploads/2022/02/barion_logo.svg" alt="">
		<!--img src="/wp-content/uploads/2022/02/simplepay_logo.svg" alt=""-->
	</div>
</div>
<!-- !Bottom-bar -->
<div id="bottom-bar" <?php echo presscore_bottom_bar_class(); ?> role="contentinfo">
    <div class="wf-wrap">
        <div class="wf-container-bottom">

			<?php
			$logo = presscore_get_the_bottom_bar_logo();
			if ( $logo ) {
				echo '<div id="branding-bottom">';
				presscore_display_the_logo( $logo );
				echo '</div>';
			}

			do_action( 'presscore_credits' );

			$config     = presscore_config();
			$copyrights = $config->get( 'template.bottom_bar.copyrights' );
			$credits    = $config->get( 'template.bottom_bar.credits' );

			if ( $copyrights || $credits ) : ?>

                <div class="wf-float-left">

					<?php
					echo do_shortcode( $copyrights );

					if ( $credits ) {
						echo '&nbsp;Dream-Theme &mdash; truly <a href="https://dream-theme.com" target="_blank">premium WordPress themes</a>';
					}
					?>

                </div>

			<?php endif; ?>

            <div class="wf-float-right">

				<?php
				$extended_menu = new The7_Extended_Microwidgets_Menu();
				$extended_menu->add_hooks();

				presscore_nav_menu_list(
					'bottom',
					array(
						'submenu_class' => implode( ' ', presscore_get_primary_submenu_class( 'footer-sub-nav' ) ),
					)
				);

				$extended_menu->remove_hooks();

				$bottom_text = $config->get( 'template.bottom_bar.text' );
				if ( $bottom_text ) {
					echo '<div class="bottom-text-block">' . do_shortcode( shortcode_unautop( wpautop( $bottom_text ) ) ) . '</div>';
				}
				?>

            </div>

        </div><!-- .wf-container-bottom -->
    </div><!-- .wf-wrap -->
</div><!-- #bottom-bar -->
