<?php

// TODO GLS összekötés
// TODO SKU változtatás a Kosárba rakom gomb felett / alatt formátum váltáskor







class WPTurbo
{
    public function __construct()
    {
        add_action('init', [&$this, 'action_init']);
        add_action('rest_api_init', [$this, 'action_rest_api_init']);
        add_action('template_redirect', [&$this, 'action_template_redirect'], 99);
        add_action('woocommerce_after_add_to_cart_button', [&$this, 'action_woocommerce_after_add_to_cart_button'], 100, 0);
        add_action('wp_footer', [&$this, 'action_wp_footer']);

        add_filter('woocommerce_variation_option_name', [&$this, 'filter_woocommerce_variation_option_name'], 10, 1);

        // Variable
        add_filter('woocommerce_product_variation_get_regular_price', [&$this, 'custom_price'], 99, 2 );
        add_filter('woocommerce_product_variation_get_price', [&$this, 'custom_price'], 99, 2 );

        // Variations (of a variable product)
        //add_filter('woocommerce_variation_prices_price', [&$this, 'custom_price'], 99, 3 );
        //add_filter('woocommerce_variation_prices_regular_price', [&$this, 'custom_price'], 99, 3 );

        add_filter( 'woocommerce_available_variation', [$this, 'my_variation'], 100, 3);

        // Variations (of a variable product)
        //add_filter( 'woocommerce_variation_prices_price', [&$this, 'my_variation'], 99, 3);
        //add_filter( 'woocommerce_variation_prices_regular_price', [&$this, 'my_variation'], 99, 3);
        //add_filter( 'woocommerce_available_variation', [&$this, 'my_variation'], 10, 3);
    }

    private function dump(mixed $variable)
    {
        echo '<pre>';
        print_r($variable);
        echo '</pre>';
        //exit;
    }

    public function custom_price( $price, $product) {
        $valasztottMennyiseg = (int) $product->get_attributes()['mennyiseg'];

        return $this->getAr($valasztottMennyiseg)*$valasztottMennyiseg;
    }

    public function my_variation( $data, $product, $variation ) {
        $valasztottMennyiseg = (int) $variation->get_variation_attributes()['attribute_mennyiseg'];

        $data['price_html'] = '<span class="price">'.$variation->get_price_html().'</span>'; //'<span class="price"><span class="woocommerce-Price-amount amount"><bdi>'.wc_price($data['display_price']).'<span class="woocommerce-Price-currencySymbol">&#70;&#116;</span></bdi></span></span>';
        $data['variation_description'] = '<p>'.wc_price($this->getAr($valasztottMennyiseg)).'/db</p>';

        return $data;
    }

    public function action_rest_api_init()
    {
        register_rest_route('alma', 'banan', [
            'methods'   => 'GET',
            'callback'  => [$this, 'alma']
        ]);
    }

    private function sendCurl($url='', $opt=[], $header=[])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt_array($ch, $opt);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // return the transfer as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    private function getPrintBoxToken(): array
    {
        $opt = [
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => [
                'grant_type' => 'client_credentials',
                'client_id' => 'bi3yN91sGdl2zfNtABmTKQjZRPmK1TM8UA7nycY1',
                'client_secret' => 'uSt1ZYoqOzV7zY7Zl3nV2WzzZwizBANmfmMcrT0KKuvHNttgZMSUFYGU4ABLoQkvpeUxpaDEa4CKrZDJwT0g6XJk66za4sUqw1eA0IwNyHRCLvs0IBListlTa8O9TZyk'
            ]
        ];
        $result = $this->sendCurl('https://paperstories-eu-pbx2.getprintbox.com/o/token/', $opt);

        return json_decode($result, true);
    }

    private function getAr(int $mennyiseg)
    {
        $arak = [
            'decreasing' => [
                'leiras' => 'meghívók, ültető- és köszönőkártyák, stb.',
                'db' => [10,15,20,25,30,35],
                'meret' => [
                    '145X145' => [890,890,790,750,750,700],
                    '170X120' => [890,890,790,750,750,700],
                    '175X115' => [730,730,650,615,615,575]
                ]
            ],
            'fix' => [
                'leiras' => 'poszter',
                'db' => [1,2,3,4,5,6],
                'meret' => [
                    '300X400' => [5490,5490,5490,5490,5490,5490],
                    '400X300' => [5490,5490,5490,5490,5490,5490]
                ]
            ],
            '10by10' => [
                'leiras' => 'boriték matrica, ajándék cimke',
                'db' => [10,20,30,40,50,60],
                'meret' => [
                    '45X45' => [130,130,130,130,130,130],
                    '50X70' => [310,275,265,245,240,"234,5"]
                ]
            ],
            '1by1' => [
                'leiras' => 'asztalszám, ültetési rend',
                'db' => [1,2,3,4,5,6],
                'meret' => [
                    '105X145' => [1615, 1517, 1419,1321,1223,1125],
                    '140X205' => [1615, 1517, 1419,1321,1223,1125]
                ]
            ],
        ];
        $mennyisegKey = array_search($mennyiseg, $arak['decreasing']['db']);
        $darabar = $arak['decreasing']['meret']['170X120'][$mennyisegKey];

        return $darabar;
        //return $arak;
    }

    public function alma()
    {
        /*
        $ar = $this->getAr(21);
        echo json_encode($ar, JSON_UNESCAPED_UNICODE);
        exit;
        */

        $result = $this->getPrintBoxToken();

        $header = [];
        $header[] = 'Content-length: 0';
        $header[] = 'Content-type: application/json';
        $header[] = 'Authorization: Bearer '.$result['access_token'];
        $rest = $this->sendCurl('https://paperstories-eu-pbx2.getprintbox.com/api/ec/v4/product-families/', [], $header);

        wp_send_json(json_decode($rest, true), 200);
    }

    public function filter_woocommerce_variation_option_name($termName)
    {
        $term = get_term_by('name', $termName, 'pa_format');

        if (!is_null($term) && !is_null($term->description)) {
            return $termName.' | szerkeszthető: '.$term->description;
        }

        return $termName;
    }

    public function action_init() {
        // maintenance mode = redirect visitors if they are not logged in
        if(! is_admin() && $GLOBALS['pagenow'] !== 'wp-login.php') {
            if (! is_user_logged_in() && 0==get_option( 'blog_public')) {
                wp_redirect( 'http://www.paperstories.eu');
                exit;
            }
        }
    }

    public function action_template_redirect() {
        if ( is_product() ) {
            /** @var WC_Product $productObject  */
            $productObject = wc_get_product(get_queried_object_id());
            $productObjectSKU = $productObject->get_sku();
            if (str_contains($productObjectSKU, '-minta')) {
                $mainProductSKU = str_replace('-minta', '', $productObjectSKU);
                $mainProduct = wc_get_product_id_by_sku($mainProductSKU);
                $sampleProduct = wc_get_product($mainProduct);
                wp_safe_redirect( $sampleProduct->get_slug() );
                exit;
            }
        }
    }

    public function action_wp_footer() {
        ?>
        <style>
            .variations td.value {
                text-align: left;
            }

            #popup {
                background-color: white;
                text-align: center;
                color: black;
                z-index: 10000000;
                position: fixed;
                display: flex;
                width: 50%;
                margin: 10% 25%;
                border: 1px solid lightgray;
            }
            #popup p.exit {
                text-align: right;
                padding: 16px;
            }
            #popup .popup-content {
                padding: 25px 50px 50px 50px;
            }

            @media only screen and (max-width: 800px) {
                #popup {
                    width: 80%;
                    margin: 10%;
                }
            }
        </style>
        <div id="popup" style="display: none;">
            <p class="exit" onclick="document.getElementById('popup').style.display='none';">X</p>
            <div class="popup-content">
                <?php echo get_the_excerpt(1911); ?>
            </div>
        </div>
        <script>
            document.getElementById("page").appendChild(document.getElementById("popup"));
            jQuery(document).ready(function() {
                document.getElementById("showSampleProductPopup").addEventListener("click", function(){
                    document.getElementById("popup").style.display = "block";
                });
                //function OrderSampleOnclickEventListener() {
                let addSampleProductToCartButton = document.getElementById("addSampleProductToCart");
                if(addSampleProductToCartButton!==null && typeof addSampleProductToCartButton !== "undefined") {
                    addSampleProductToCartButton.addEventListener('click', function(){
                        addSampleProductToCartButton.setAttribute("disabled", true);
                            document.body.style.cursor = 'progress';
                            jQuery.ajax({
                          url: "/?add-to-cart=1799&quantity=1",
                        })
                        .done(function( data ) {
                            document.body.style.cursor = 'default';
                            alert("Mintatermék bekerült a kosarába!");
                            addSampleProductToCartButton.removeAttribute("disabled");
                        });
                    });
                }
                //}
            });
        </script>
        <?php
    }

    public function action_woocommerce_after_add_to_cart_button() {
        // define the woocommerce_before_add_to_cart_button callback
        global $product;
        $sampleSKU = $product->get_sku().'-minta';
        $sampleProduct = wc_get_product_id_by_sku($sampleSKU);
        if ($sampleProduct) {
            //echo '<button type="button" id="addSampleProductToCart" class="button custom-btn white-border" style="margin:15px 0 0 0">Mintakártya</button>';
            echo '<button type="button" id="showSampleProductPopup" class="button custom-btn white-border" style="margin:15px 0 0 0">Mintakártya</button>';
        }
    }
}
$WPTurbo = new WPTurbo();









add_action( 'wp_enqueue_scripts', 'theseven_child_scripts');
function theseven_child_scripts() {
   wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/js/scripts.js', array('jquery'), false, true);
   wp_enqueue_script( 'child-filter', get_stylesheet_directory_uri() . '/js/jquery.simpler-sidebar-css3.min.js', array('jquery'), false, true);
}

////////////////////////////////////////////////
//// images
////////////////////////////////////////////////
define('IMAGES', get_stylesheet_directory_uri().'/images/');

////////////////////////////////////////////////
//// ALLOW SVG
////////////////////////////////////////////////
function add_file_types_to_uploads($file_types){
    $new_filetypes = array();
    $new_filetypes['svg'] = 'image/svg+xml';
    $file_types = array_merge($file_types, $new_filetypes );
      return $file_types;
    }
add_filter('upload_mimes', 'add_file_types_to_uploads');

//// ALLOW SVG
function cc_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');
define('ALLOW_UNFILTERED_UPLOADS', true);


////////////////////////////////////////////////
//// Category and attribute name after product title
////////////////////////////////////////////////
add_action( 'woocommerce_after_shop_loop_item', 'bbloomer_show_free_shipping_loop', 5 );
function bbloomer_show_free_shipping_loop() {
   $product = wc_get_product();
   $product_cat_name = $term->name;

   echo '<span class="prod-category">'.$product->get_categories( ', ', '<span class="posted_in">' . _n( '', '', sizeof( get_the_terms( $post->ID, 'product_cat' ) ), 'woocommerce' ) . ' ', '</span>' ).'</span>';
   echo '<span class="prod-attribute">'.$product->get_attribute( 'stilus' ).'</span>';

}
add_filter( 'woocommerce_after_shop_loop_item','wc_reg_for_menus', 5 );


////////////////////////////////////////////////
//// Product attributes
////////////////////////////////////////////////
add_action( 'woocommerce_after_shop_loop_item', 'wc_show_attribute_links', 5 );
// if you'd like to show it on archive page, replace "woocommerce_product_meta_end" with "woocommerce_shop_loop_item_title"

function wc_show_attribute_links() {
	global $post;
	$attribute_names = array( 'pa_surface' ); // Add attribute names here and remember to add the pa_ prefix to the attribute name

	foreach ( $attribute_names as $attribute_name ) {
		$taxonomy = get_taxonomy( $attribute_name );

		if ( $taxonomy && ! is_wp_error( $taxonomy ) ) {
			$terms = wp_get_post_terms( $post->ID, $attribute_name );
			$terms_array = array();

	        if ( ! empty( $terms ) ) {
             echo '<ul class="attribute-list">';
		        foreach ( $terms as $term ) {

			       $full_line = '<li id="' . $term->slug . '"><span>'. $term->name . '</span></li> ';
			       array_push( $terms_array, $full_line );
		        }
		        echo  ' ' . implode( $terms_array );
              echo '</ul>';
	        }
    	}
    }
}





////////////////////////////////////////////////
//// Display category image on category archive
////////////////////////////////////////////////
add_action( 'presscore_get_page_title', 'woocommerce_category_image', 2 );
function woocommerce_category_image() {
    if ( is_product_category() ){
	    global $wp_query;
	    $cat = $wp_query->get_queried_object();
	    $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
	    $image = wp_get_attachment_url( $thumbnail_id );
	    if ( $image ) {
		    echo '<img src="' . $image . '" alt="' . $cat->name . '" />';
		}
	}
}

////////////////////////////////////////////////
//// Product Category > Body CSS Class @ Single Product
////////////////////////////////////////////////
add_filter( 'body_class', 'bbloomer_wc_product_cats_css_body_class' );
function bbloomer_wc_product_cats_css_body_class( $classes ){
  if ( is_singular( 'product' ) ) {
    $current_product = wc_get_product();
    $custom_terms = get_the_terms( $current_product->get_id(), 'product_cat' );
    if ( $custom_terms ) {
      foreach ( $custom_terms as $custom_term ) {
        $classes[] = 'product_cat_' . $custom_term->slug;
      }
    }
  }
  return $classes;
}


////////////////////////////////////////////////
//// Add subcategory to body
////////////////////////////////////////////////

add_filter( 'body_class', 'wc_product_cats_css_body_class' );
function wc_product_cats_css_body_class( $classes ){
if( is_tax( 'product_cat' ) ) {
   $cat = get_queried_object();
   if( 0 < $cat->parent  ) $classes[] = 'subcategory';
	 if (strpos($cat->slug, 'personnalisation') !== false) {
	     $classes[] = 'personnalisation';
	 }
}
return $classes;
}


////////////////////////////////////////////////
//// Display field on "Add new product category" admin page
////////////////////////////////////////////////
// ---------------
// 1.

add_action( 'product_cat_add_form_fields', 'bbloomer_wp_editor_add', 10, 2 );

function bbloomer_wp_editor_add() {
    ?>
    <div class="form-field">
        <label for="seconddesc"><?php echo __( 'Lenti tartalom pl.:MEGHÍVÓK - cím/szöveg/gomb', 'woocommerce' ); ?></label>

      <?php
      $settings = array(
         'textarea_name' => 'seconddesc',
         'quicktags' => array( 'buttons' => 'em,strong,link' ),
         'tinymce' => array(
            'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
            'theme_advanced_buttons2' => '',
         ),
         'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
      );

      wp_editor( '', 'seconddesc', $settings );
      ?>

        <p class="description"><?php echo __( 'Ez a leírás a kategóriaoldalon lent fog megjelenni', 'woocommerce' ); ?></p>
    </div>
    <?php
}

// ---------------
// 2. Display field on "Edit product category" admin page

add_action( 'product_cat_edit_form_fields', 'bbloomer_wp_editor_edit', 10, 2 );

function bbloomer_wp_editor_edit( $term ) {
    $second_desc = htmlspecialchars_decode( get_woocommerce_term_meta( $term->term_id, 'seconddesc', true ) );
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="second-desc"><?php echo __( 'Lenti tartalom pl.:MEGHÍVÓK - cím/szöveg/gomb', 'woocommerce' ); ?></label></th>
        <td>
            <?php

         $settings = array(
            'textarea_name' => 'seconddesc',
            'quicktags' => array( 'buttons' => 'em,strong,link' ),
            'tinymce' => array(
               'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
               'theme_advanced_buttons2' => '',
            ),
            'editor_css' => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>',
         );

         wp_editor( $second_desc, 'seconddesc', $settings );
         ?>

            <p class="description"><?php echo __( 'Ez a leírás a kategóriaoldalon lent fog megjelenni', 'woocommerce' ); ?></p>
        </td>
    </tr>
    <?php
}

// ---------------
// 3. Save field @ admin page

add_action( 'edit_term', 'bbloomer_save_wp_editor', 10, 3 );
add_action( 'created_term', 'bbloomer_save_wp_editor', 10, 3 );

function bbloomer_save_wp_editor( $term_id, $tt_id = '', $taxonomy = '' ) {
   if ( isset( $_POST['seconddesc'] ) && 'product_cat' === $taxonomy ) {
      update_woocommerce_term_meta( $term_id, 'seconddesc', esc_attr( $_POST['seconddesc'] ) );
   }
}

// ---------------
// 4. Display field under products @ Product Category pages

add_action( 'woocommerce_after_shop_loop', 'bbloomer_display_wp_editor_content', 5 );

function bbloomer_display_wp_editor_content() {
   if ( is_product_taxonomy() ) {
      $term = get_queried_object();
      if ( $term && ! empty( get_woocommerce_term_meta( $term->term_id, 'seconddesc', true ) ) ) {
         echo '<div class="term-description description-bottom">' . wc_format_content( htmlspecialchars_decode( get_woocommerce_term_meta( $term->term_id, 'seconddesc', true ) ) ) . '</div>';
      }
   }
}



////////////////////////////////////////////////
//// ACF product category desc top
////////////////////////////////////////////////
add_shortcode('display_acf_desc_top', 'acf_desc_top');

function acf_desc_top ( $atts ) {

   $queried_object = get_queried_object();
   $taxonomy = $queried_object->taxonomy;
   $term_id = $queried_object->term_id;
   $post_id = $taxonomy . '_' . $term_id;

   $top_custom_field = get_field('top_desc', $post_id);


   if (!empty($top_custom_field )) {
      echo '<div class="category-description-top">'. $top_custom_field .'</div>';
   }

}
add_action( 'woocommerce_archive_description', 'acf_desc_top', 1 );

////////////////////////////////////////////////
//// ACF product category gallery
////////////////////////////////////////////////
add_shortcode('display_acf_gallery', 'acf_gallery');

function acf_gallery ( $atts ) {

   $queried_object = get_queried_object();
   $taxonomy = $queried_object->taxonomy;
   $term_id = $queried_object->term_id;

   $images = get_field('slider', $queried_object);

   if( $images ): ?>
          <div class="custom-owl owl-carousel carousel-shortcode dt-owl-carousel-call carousel-shortcode-id-d7f9707ad980870c37aff56a1c10e45a bullets-scale-up reposition-arrows arrows-bg-on dt-arrow-border-on dt-arrow-hover-border-on disable-arrows-hover-bg arrows-hover-bg-on top-slider owl-loaded owl-drag refreshed" data-scroll-mode="1" data-col-num="1" data-wide-col-num="1" data-laptop-col="1" data-h-tablet-columns-num="1" data-v-tablet-columns-num="1" data-phone-columns-num="1" data-auto-height="true" data-col-gap="0" data-stage-padding="0" data-speed="600" data-autoplay="true" data-autoplay_speed="5000" data-arrows="false" data-bullet="true" data-next-icon="icon-ar-017-r" data-prev-icon="icon-ar-017-l">
              <?php foreach( $images as $image ): ?>
                  <li>
                     <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" />
                  </li>
              <?php endforeach; ?>
           </div>
      <?php endif;
   }


add_action( 'presscore_get_page_title', 'acf_gallery', 1 );


////////////////////////////////////////////////
//// ACF product bottom desc another
////////////////////////////////////////////////
add_shortcode('display_acf_desc', 'acf_desc');

function acf_desc ( $atts ) {

   $queried_object = get_queried_object();
   $taxonomy = $queried_object->taxonomy;
   $term_id = $queried_object->term_id;
   $post_id = $taxonomy . '_' . $term_id;

   $custom_field = get_field('bottom_desc_another', $post_id);


   if (!empty($custom_field )) {
      echo '<div class="description-bottom-another">'. $custom_field .'</div>';
   }

}
add_action( 'woocommerce_after_shop_loop', 'acf_desc', 50 );


////////////////////////////////////////////////
//// Product filter
////////////////////////////////////////////////
add_shortcode('display_prod_filter', 'prod_filter');

function prod_filter ( $atts ) {
   echo '<aside class="filter-box"><h3 class="filter-box-title">Szűrés</h3>'.do_shortcode( '[dt_breadcrumbs font_color="#000000" alignment="left"]' ).'<span class="quit-sidebar"><svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512.001 512.001" xmlns:v="https://vecta.io/nano"><path d="M284.286 256.002L506.143 34.144c7.811-7.811 7.811-20.475 0-28.285s-20.475-7.811-28.285 0L256 227.717 34.143 5.859c-7.811-7.811-20.475-7.811-28.285 0s-7.811 20.475 0 28.285l221.857 221.857L5.858 477.859c-7.811 7.811-7.811 20.475 0 28.285 3.905 3.905 9.024 5.857 14.143 5.857a19.94 19.94 0 0 0 14.143-5.857L256 284.287l221.857 221.857c3.905 3.905 9.024 5.857 14.143 5.857a19.94 19.94 0 0 0 14.143-5.857c7.811-7.811 7.811-20.475 0-28.285L284.286 256.002z" fill="#000000"/></svg></span>'.do_shortcode( '[br_filters_group group_id=304]' ).'<div class="bapf_sfilter unselect-box"><div class="bapf_body"><div class="berocket_aapf_widget_selected_area"><div class="berocket_aapf_widget_selected_filter"><ul class="bapf_sfa_unall"><li><a href="#Unselect_all" class="braapf_unselect_all"><i class="fa fa-times"></i></a></li></ul></div></div></div></div></aside>';
}

add_action( 'woocommerce_before_main_content', 'prod_filter', 1 );

////////////////////////////////////////////////
//// Product filter button
////////////////////////////////////////////////
/*
 * TODO
 * 2022.06.14-én Laura és Gergő egyeztetése alapján kérésre a Szűrés gomb lekerül, Gergő javaslatára v2-ben kerül vissza,
 * amikor az adott variáció képe jelenik meg, nem a featured image.
add_shortcode('display_prod_filter_button', 'prod_filter_button');

   function prod_filter_button ( $atts ) {
      echo '<div class="filter-btn-box"><a href="#" class="custom-btn white filter-btn">SZŰRÉS <svg style="width: 32px;vertical-align: middle;" id="Layer_1" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><defs><style>.cls-1{fill:none;stroke:#38383d;stroke-miterlimit:10;stroke-width:4.5px;}</style></defs><title>weboldal ikonok</title><circle class="cls-1" cx="62.02" cy="67.44" r="10.85"/><line class="cls-1" x1="51.16" y1="67.44" x2="40.31" y2="67.44"/><line class="cls-1" x1="72.87" y1="67.44" x2="159.69" y2="67.44"/><circle class="cls-1" cx="89.15" cy="132.56" r="10.85"/><line class="cls-1" x1="78.3" y1="132.56" x2="40.31" y2="132.56"/><line class="cls-1" x1="100" y1="132.56" x2="159.69" y2="132.56"/><circle class="cls-1" cx="137.99" cy="100" r="10.85"/><line class="cls-1" x1="148.84" y1="100" x2="159.69" y2="100"/><line class="cls-1" x1="127.13" y1="100" x2="40.31" y2="100"/></svg></a></div>';
   }
add_action( 'woocommerce_before_shop_loop', 'prod_filter_button', 10 );
*/

////////////////////////////////////////////////
//// Single Product gallery
////////////////////////////////////////////////
add_filter ( 'woocommerce_product_thumbnails_columns', 'bbloomer_change_gallery_columns' );

function bbloomer_change_gallery_columns() {
   return 1;
}


////////////////////////////////////////////////
//// Add to Cart Quantity drop-down - WooCommerce
////////////////////////////////////////////////

function woocommerce_quantity_input_HULYESEG( $args = array(), $product = null, $echo = true ) {

	if ( is_null( $product ) ) {
	   $product = $GLOBALS['product'];
	}

	$defaults = array(
		'input_id' => uniqid( 'quantity_' ),
		'input_name' => 'quantity',
		'input_value' => '10',
		'classes' => apply_filters( 'woocommerce_quantity_input_classes', array( 'input-text', 'qty', 'text' ), $product ),
		'max_value' => apply_filters( 'woocommerce_quantity_input_max', -1, $product ),
		'min_value' => apply_filters( 'woocommerce_quantity_input_min', 10, $product ),
		'step' => apply_filters( 'woocommerce_quantity_input_step', 10, $product ),
		'pattern' => apply_filters( 'woocommerce_quantity_input_pattern', has_filter( 'woocommerce_stock_amount', 'intval' ) ? '[0-9]*' : '' ),
		'inputmode' => apply_filters( 'woocommerce_quantity_input_inputmode', has_filter( 'woocommerce_stock_amount', 'intval' ) ? 'numeric' : '' ),
		'product_name' => $product ? $product->get_title() : '',
	);

	$args = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( $args, $defaults ), $product );

	// Apply sanity to min/max args - min cannot be lower than 0.
	$args['min_value'] = max( $args['min_value'], 10 );
	// Note: change 20 to whatever you like
	$args['max_value'] = 0 < $args['max_value'] ? $args['max_value'] : 100;

	// Max cannot be lower than min if defined.
	if ( '' !== $args['max_value'] && $args['max_value'] < $args['min_value'] ) {
		$args['max_value'] = $args['min_value'];
	}

	$options = '';

	for ( $count = $args['min_value']; $count <= $args['max_value']; $count = $count + $args['step'] ) {

	   // Cart item quantity defined?
	   if ( '' !== $args['input_value'] && $args['input_value'] >= 1 && $count == $args['input_value'] ) {
		  $selected = 'selected';
	   } else $selected = '';

      if( is_product() ) {
         $options .= '<option value="' . $count . '"' . $selected . '>' . $count . ' db | '.$product->get_price().' Ft/db | '.$product->get_price()*$count.' Ft</option>';
      } else {
         $options .= '<option value="' . $count . '"' . $selected . '>' . $count . ' db</option>';
      }


	}

	$string = '<div class="quantity"><span>MENNYISÉG</span><select name="' . $args['input_name'] . '">' . $options . '</select></div>';

	if ( $echo ) {
	   echo $string;
	} else {
	   return $string;
	}

}


////////////////////////////////////////////////
//// Product category on single page
////////////////////////////////////////////////
add_shortcode('woocommerce_product_category_shortcode', 'woocommerce_product_category');
function woocommerce_product_category ( $atts ) {
   $product = wc_get_product();
   $product_cat_name = $term->name;


   echo '<span class="single-prod-category">'.$product->get_categories().'</span><span class="single-prod-sku">'.$product->get_sku().'</span>';
}
add_action( 'woocommerce_single_product_summary', 'woocommerce_product_category', 6 );


////////////////////////////////////////////////
//// Remove mete from product page
////////////////////////////////////////////////
function remove_single_product_elements(){
     remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
}
add_action('woocommerce_before_single_product', 'remove_single_product_elements' );


////////////////////////////////////////////////
//// Single product info
////////////////////////////////////////////////
add_shortcode('woocommerce_product_info_shortcode', 'woocommerce_product_info');

function woocommerce_product_info($atts) {
    /** @var WC_Product_Variable $product*/
    /*
    global $product;
    $variationAttributes = $product->get_variation_attributes();
    $pa_formats = $variationAttributes['pa_format'];
    ?>
    <script>
          document.getElementById('pa_format').addEventListener('change', (event) => {
              alert(event.target.value);
              alert(document.getElementById('pa_format').value);
              //const result = document.querySelector('.result');
              //result.textContent = `You like ${event.target.value}`;
        });
    </script>
    <?php
    */

   $meret = get_field('meret');
   $papir = get_field('papir');
   $oldalak = get_field('szerkesztheto_oldalak');
   $online = get_field('online_szerkesztes');
   $the_content = apply_filters('the_content', get_the_content());

   echo '<div class="prod-info-box"><span>INFORMÁCIÓ</span><ul><!--li>Méret: '. $meret .'</li--><li>Papír: '. $papir .'</li><li>Online szerkesztés díjtalan</li></ul>'.$the_content.'</div>';
}
add_action( 'woocommerce_single_product_summary', 'woocommerce_product_info', 50 );


////////////////////////////////////////////////
//// Remove single product page short desc
////////////////////////////////////////////////
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20);

////////////////////////////////////////////////
//// Price
////////////////////////////////////////////////
add_action( 'woocommerce_single_product_summary', 'woocommerce_total_product_price', 31 );
function woocommerce_total_product_price() {
    global $woocommerce, $product;
    // let's setup our divs
    echo sprintf('<div id="product_total_price" style="margin-bottom:20px;">%s %s</div>',__('Ár:','woocommerce'),'<span class="price">'.$product->get_price().''.get_woocommerce_currency_symbol().'</span>');
    ?>
        <script>
            jQuery(function($){
                var price = <?php echo $product->get_price(); ?>,
                    currency = '<?php echo get_woocommerce_currency_symbol(); ?>';

                $('[name=quantity]').change(function(){
                    if (!(this.value < 1)) {

                        var product_total = parseFloat(price * this.value);

                        $('#product_total_price .price').html( product_total.toFixed(0) + currency);

                    }
                });
            });
        </script>
    <?php
}


////////////////////////////////////////////////
//// Remove tabs from product page
////////////////////////////////////////////////
add_filter( 'woocommerce_product_tabs', 'my_remove_all_product_tabs', 98 );

function my_remove_all_product_tabs( $tabs ) {
  unset( $tabs['description'] );        // Remove the description tab
  unset( $tabs['reviews'] );       // Remove the reviews tab
  unset( $tabs['additional_information'] );    // Remove the additional information tab
  return $tabs;
}


////////////////////////////////////////////////
//// to add a confirm password field on the register form under My Accounts
////////////////////////////////////////////////
function woocommerce_registration_errors_validation($reg_errors, $sanitized_user_login, $user_email) {
	global $woocommerce;
	extract( $_POST );
	if ( strcmp( $password, $password2 ) !== 0 ) {
		return new WP_Error( 'registration-error', __( 'Passwords do not match.', 'woocommerce' ) );
	}
	return $reg_errors;
}
add_filter('woocommerce_registration_errors', 'woocommerce_registration_errors_validation', 10, 3);

function woocommerce_register_form_password_repeat() {
	?>
	<p class="form-row form-row-wide">
		<label for="reg_password2"><?php _e( 'Jelszó megerősítése', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="password" class="input-text" name="password2" id="reg_password2" value="<?php if ( ! empty( $_POST['password2'] ) ) echo esc_attr( $_POST['password2'] ); ?>" />
	</p>
   </div>
	<?php
}
add_action( 'woocommerce_register_form', 'woocommerce_register_form_password_repeat' );


////////////////////////////////////////////////
//// Add First & Last Name to My Account Register Form
////////////////////////////////////////////////

// 1. ADD FIELDS
add_action( 'woocommerce_register_form_start', 'bbloomer_add_name_woo_account_registration' );

function bbloomer_add_name_woo_account_registration() {
    ?>
    <div class="left-side">
    <p class="form-row form-row-first">
    <label for="reg_billing_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_first_name" id="reg_billing_first_name" value="<?php if ( ! empty( $_POST['billing_first_name'] ) ) esc_attr_e( $_POST['billing_first_name'] ); ?>" />
    </p>

    <p class="form-row form-row-last">
    <label for="reg_billing_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
    <input type="text" class="input-text" name="billing_last_name" id="reg_billing_last_name" value="<?php if ( ! empty( $_POST['billing_last_name'] ) ) esc_attr_e( $_POST['billing_last_name'] ); ?>" />
    </p>

    <?php
}

///////////////////////////////
// 2. VALIDATE FIELDS
add_filter( 'woocommerce_registration_errors', 'bbloomer_validate_name_fields', 10, 3 );

function bbloomer_validate_name_fields( $errors, $username, $email ) {
    if ( isset( $_POST['billing_first_name'] ) && empty( $_POST['billing_first_name'] ) ) {
        $errors->add( 'billing_first_name_error', __( '<strong>Error</strong>: First name is required!', 'woocommerce' ) );
    }
    if ( isset( $_POST['billing_last_name'] ) && empty( $_POST['billing_last_name'] ) ) {
        $errors->add( 'billing_last_name_error', __( '<strong>Error</strong>: Last name is required!.', 'woocommerce' ) );
    }
    return $errors;
}

///////////////////////////////
// 3. SAVE FIELDS
add_action( 'woocommerce_created_customer', 'bbloomer_save_name_fields' );

function bbloomer_save_name_fields( $customer_id ) {
    if ( isset( $_POST['billing_first_name'] ) ) {
        update_user_meta( $customer_id, 'billing_first_name', sanitize_text_field( $_POST['billing_first_name'] ) );
        update_user_meta( $customer_id, 'first_name', sanitize_text_field($_POST['billing_first_name']) );
    }
    if ( isset( $_POST['billing_last_name'] ) ) {
        update_user_meta( $customer_id, 'billing_last_name', sanitize_text_field( $_POST['billing_last_name'] ) );
        update_user_meta( $customer_id, 'last_name', sanitize_text_field($_POST['billing_last_name']) );
    }

}


add_action( 'woocommerce_cart_totals_before_shipping', 'display_coupon_form_below_proceed_checkout', 25 );

function display_coupon_form_below_proceed_checkout() {
   ?>
      <form class="woocommerce-coupon-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
         <?php if ( wc_coupons_enabled() ) { ?>
            <div class="coupon under-proceed">
               <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" style="width: 100%" />
               <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>" style="width: 100%"><i class="dt-icon-the7-refresh-07" aria-hidden="true"></i></button>
            </div>
         <?php } ?>
      </form>
   <?php
}





function jeherve_remove_state_field( $fields ) {
	unset( $fields['state'] );

	return $fields;
}
add_filter( 'woocommerce_default_address_fields', 'jeherve_remove_state_field' );
