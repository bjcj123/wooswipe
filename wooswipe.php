<?php
/*
Plugin Name: WooSwipe
Plugin URI: http://thriveweb.com.au/the-lab/wooswipe/
Description: This is a image gallery plugin for WordPress built using wooswipe from Dmitry Semenov <a href="http://photoswipe.com.au/">photoswipe</a> and <a href="http://kenwheeler.github.io/slick/">Slick</a> Carousel</a>.  

Author: Dean Oakley, Eric Jinks
Author URI: http://thriveweb.com.au/
Version: 1.0.5
Text Domain: wooswipe
*/

/*  Copyright 2010  Dean Oakley  (email : dean@thriveweb.com.au)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.
 
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { 
	die('Illegal Entry');  
}

//============================== wooswipe options ========================//
class wooswipe_plugin_options {

	//Defaults
	public static function WooSwipe_getOptions() {
		
		//Pull from WP options database table
		$options = get_option('wooswipe_options');
		
		if (!is_array($options)) {
						
			$options['white_theme'] = false;			
			
			update_option('wooswipe_options', $options);
		}
		
		return $options;
		
		
	}
	
	
	public static function update() {
		
		
		if(isset($_POST['wooswipe_save'])) {
			
			$options = wooswipe_plugin_options::WooSwipe_getOptions();			
			
			if (isset($_POST['white_theme'])) {
				$options['white_theme'] = (bool)true;
			} else {
				$options['white_theme'] = (bool)false;
			} 
			
			update_option('wooswipe_options', $options);

		} else {
			wooswipe_plugin_options::WooSwipe_getOptions();
		}
		

	}
	

	public static function display() {
		
		$options = wooswipe_plugin_options::WooSwipe_getOptions();
		?>
		
		<div id="wooswipe_admin" class="wrap">
		
			<h2>WooSwipe Options</h2>
			
			<p>WooSwipe is a WooCommerce gallery plugin for WordPress built using Photoswipe from  Dmitry Semenov.  <a href="http://photoswipe.com/">Photoswipe</a> and <a href="http://kenwheeler.github.io/slick/"> Slick</a> </p>

			<p>More options coming soon. Edit your image sizes <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=products&section=display', 'http' ); ?> "> here </a></p>
			
			<p style="font-style:italic; font-weight:normal; color:grey " >Please note: Images that are already on the server will not change size until you regenerate the thumbnails. Use <a title="http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/" href="http://wordpress.org/extend/plugins/ajax-thumbnail-rebuild/">AJAX thumbnail rebuild</a> </p>
			
			<form method="post" action="#" enctype="multipart/form-data">						
				
				<div class="ps_border" ></div>
								
				
				<p><label><input name="white_theme" type="checkbox" value="checkbox" <?php if($options['white_theme']) echo "checked='checked'"; ?> /> Use white theme?</label></p>
			

				<div class="ps_border" ></div>				
			
				<p><input class="button-primary" type="submit" name="wooswipe_save" value="Save Changes" /></p>
			
			</form>
			
			
		</div>
		
		<?php
	}  
}


function WooSwipe_getOption($option) {
	global $mytheme;
	return $mytheme->option[$option];
}

function wooswipe_using_woocommerce() {
	return in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) );
}

// register functions
add_action('admin_menu', array('wooswipe_plugin_options', 'update'));

$options = get_option('wooswipe_options');

///////////
//Admin CSS
function wooswipe_register_head() {
    
    $url = plugins_url( 'admin.css', __FILE__ );
    
    echo "<link rel='stylesheet' type='text/css' href='$url' />\n";
}
add_action('admin_head', 'wooswipe_register_head');

///////////
//Sub Menu
function register_my_custom_submenu_page() {
    add_submenu_page( 'woocommerce', 'WooSwipe', 'WooSwipe', 'manage_options', 'my-custom-submenu-page', array('wooswipe_plugin_options', 'display') ); 
}
function my_custom_submenu_page_callback() {
    echo '<h3>My Custom Submenu Page</h3>';
}
add_action('admin_menu', 'register_my_custom_submenu_page',99);


//============================== insert HTML header tag ========================//

function wooswipe_scripts_method() {
	
	$wooswipe_wp_plugin_path =  plugins_url() . '/wooswipe' ;
	$options = get_option('wooswipe_options');
	
	wp_enqueue_style( 'pswp-css', $wooswipe_wp_plugin_path . '/pswp/photoswipe.css'  );
    
    if($options['white_theme']) wp_enqueue_style( 'white_theme', $wooswipe_wp_plugin_path . '/pswp/white-skin/skin.css'  );
    else wp_enqueue_style( 'pswp-skin', $wooswipe_wp_plugin_path . '/pswp/default-skin/default-skin.css'  );
     
    wp_enqueue_style( 'slick-css', $wooswipe_wp_plugin_path . '/slick/slick.css'  );
    wp_enqueue_style( 'slick-theme', $wooswipe_wp_plugin_path . '/slick/slick-theme.css'  );
    
    wp_enqueue_script( 'pswp', $wooswipe_wp_plugin_path . '/pswp/photoswipe.min.js' );
    wp_enqueue_script( 'pswp-ui', $wooswipe_wp_plugin_path . '/pswp/photoswipe-ui-default.min.js' );
    
    wp_enqueue_script( 'wooswipe_main', $wooswipe_wp_plugin_path .'/main.js' );
    wp_enqueue_script( 'slick', $wooswipe_wp_plugin_path .'/slick/slick.min.js' );
}
add_action('wp_enqueue_scripts', 'wooswipe_scripts_method');


///////////////////////
// remove woo lightbox
add_action( 'wp_print_scripts', 'my_deregister_javascript', 100 );
function my_deregister_javascript() {
	wp_deregister_script( 'prettyPhoto' );
	wp_deregister_script( 'prettyPhoto-init' );
}
add_action( 'wp_print_styles', 'my_deregister_styles', 100 );
function my_deregister_styles() {
	wp_deregister_style( 'woocommerce_prettyPhoto_css' );
}


///////////////////////
//override Woo Gallery
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
add_action( 'woocommerce_before_single_product_summary', 'wooswipe_woocommerce_show_product_thumbnails', 20 );


function wooswipe_woocommerce_show_product_thumbnails(){
	
	global $post, $woocommerce, $product;
	
	?>
	<div id="wooswipe" class="images">
	<?php
	
	if ( has_post_thumbnail() ) {
	
		$image_title = esc_attr( get_the_title( get_post_thumbnail_id() ) );
		$image_link  = wp_get_attachment_url( get_post_thumbnail_id() );
		
		$image_src = wp_get_attachment_image_src( get_post_thumbnail_id(), '1000w' );
		$hq = wp_get_attachment_image_src( get_post_thumbnail_id(), '1800w' );
		
		$image       = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
			'title' => '',
			'data-hq' => $hq[0],
			'data-w' => $hq[1],
			'data-h' => $hq[2],
			) );
	
		$attachment_count = count( $product->get_gallery_attachment_ids() );
	
		if ( $attachment_count > 0 ) {
			$gallery = '[product-gallery]';
		} else {
			$gallery = '';
		}
	
		echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '
			<div class="single-product-main-image">
				<a href="%s"  class="woocommerce-main-image zoom" title="%s" >%s</a>
			</div>
			', $image_link, $image_title, $image ), $post->ID );
	
	} else {
	
		echo apply_filters( 'woocommerce_single_product_image_html', sprintf( '<img src="%s" alt="%s" />', wc_placeholder_img_src(), __( 'Placeholder', 'woocommerce' ) ), $post->ID );
	
	}
	
	$attachment_ids = $product->get_gallery_attachment_ids();
	if ( $attachment_ids ) {
		
		$loop 		= 0;
		$columns 	= apply_filters( 'woocommerce_product_thumbnails_columns', 3 );
		?>
		<div class="thumbnails">
	
				<ul class="thumbnail-nav">
					
					<?php /// add main image
						if ( has_post_thumbnail() ) {
	
							$attachment_id 	= get_post_thumbnail_id();
							$image       	= wp_get_attachment_image( $attachment_id, 'shop_thumbnail' );
							$hq       		= wp_get_attachment_image_src( $attachment_id, 'full' );
							$med       		= wp_get_attachment_image_src( $attachment_id, 'shop_single' );
	
							echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '
								<li>
									<div class="thumb" data-hq="%s" data-w="%s" data-h="%s" data-med="%s" data-medw="%s" data-medh="%s">%s</div>
								</li>'
								, $hq[0], $hq[1], $hq[2], $med[0], $med[1], $med[2], $image ), $attachment_id, $post->ID );
	
						}
					?>
	
					<?php
						foreach ( $attachment_ids as $attachment_id ) {
							
							$classes = array( '' );
							
							if ( $loop == 0 || $loop % $columns == 0 )
								$classes[] = '';
							if ( ( $loop + 1 ) % $columns == 0 )
								$classes[] = '';
								
							$image_link = wp_get_attachment_url( $attachment_id );
							if ( ! $image_link )
								continue;
							
							$image	= wp_get_attachment_image( $attachment_id, 'shop_thumbnail' );
							$hq     = wp_get_attachment_image_src( $attachment_id, 'full' );
							$med    = wp_get_attachment_image_src( $attachment_id, 'shop_single' );
							
							$image_class = esc_attr( implode( '', $classes ) );
							$image_title = '';
							echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', sprintf( '
								<li>
									<div class="%s thumb" title="%s" data-hq="%s" data-w="%s" data-h="%s" data-med="%s" data-medw="%s" data-medh="%s">%s</div>
								</li>'
								, $image_class, $image_title, $hq[0], $hq[1], $hq[2], $med[0], $med[1], $med[2], $image ), $attachment_id, $post->ID, $image_class );
							
							$loop++;
						}
					?>
	
	
				</ul>
	
		</div>
		<?php
	}
	
	?>
	</div>
	<?php

	echo'
	<!-- Root element of PhotoSwipe. Must have class pswp. -->
	<div class="pswp" tabindex="-1" role="dialog" aria-hidden="true">
	
	    <!-- Background of PhotoSwipe.
	         Its a separate element as animating opacity is faster than rgba(). -->
	    <div class="pswp__bg"></div>
	
	    <!-- Slides wrapper with overflow:hidden. -->
	    <div class="pswp__scroll-wrap">
	
	        <!-- Container that holds slides.
	            PhotoSwipe keeps only 3 of them in the DOM to save memory.
	            Dont modify these 3 pswp__item elements, data is added later on. -->
	        <div class="pswp__container">
	            <div class="pswp__item"></div>
	            <div class="pswp__item"></div>
	            <div class="pswp__item"></div>
	        </div>
	
	        <!-- Default (PhotoSwipeUI_Default) interface on top of sliding area. Can be changed. -->
	        <div class="pswp__ui pswp__ui--hidden">
	
	            <div class="pswp__top-bar">
	
	                <!--  Controls are self-explanatory. Order can be changed. -->
	
	                <div class="pswp__counter"></div>
	
	                <button class="pswp__button pswp__button--close" title="Close (Esc)"></button>
	
	                <button class="pswp__button pswp__button--share" title="Share"></button>
	
	                <button class="pswp__button pswp__button--fs" title="Toggle fullscreen"></button>
	
	                <button class="pswp__button pswp__button--zoom" title="Zoom in/out"></button>
	
	                <!-- Preloader demo http://codepen.io/dimsemenov/pen/yyBWoR -->
	                <!-- element will get class pswp__preloader--active when preloader is running -->
	                <div class="pswp__preloader">
	                    <div class="pswp__preloader__icn">
	                      <div class="pswp__preloader__cut">
	                        <div class="pswp__preloader__donut"></div>
	                      </div>
	                    </div>
	                </div>
	            </div>
	
	            <div class="pswp__share-modal pswp__share-modal--hidden pswp__single-tap">
	                <div class="pswp__share-tooltip"></div>
	            </div>
	
	            <button class="pswp__button pswp__button--arrow--left" title="Previous (arrow left)">
	            </button>
	
	            <button class="pswp__button pswp__button--arrow--right" title="Next (arrow right)">
	            </button>
	
	            <div class="pswp__caption">
	                <div class="pswp__caption__center"></div>
	            </div>
	
	        </div>
	
	    </div>
	
	</div>
	
	';

		
}
