<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}


/*
* Coupon Creator Inserter Class
*
*/


class Cctor__Coupon__Admin__Inserter {

	/*
	* Construct
	*
	*/
	public function __construct() {

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_search' ), 20 );

		//Add Button for Coupons in Editor
		add_action( 'media_buttons', array( $this, 'add_cc_coupon_button' ) );

	}

	/*
	* Add Coupon Inserter Button Above WordPress Editor
	*
	*/
	public function add_cc_coupon_button( $context ) {

		$screen = get_current_screen();

		if (
			'cctor_coupon' === $screen->id
			|| 'cctor_coupon_page_coupon-options' == $screen->id
		) {
			return;
		}

			// Add Content for inline popup for Coupon Inserter.
			add_action( 'admin_footer', array( $this, 'add_coupon_inline_popup' ) );

			//path to coupon icon
			$img = pngx( 'cctor' )->resource_url . 'images/cctor-icon.svg';
			//the id of the container I want to show in the popup
			$container_id = 'coupon_container';
			//our popup's title
			$title = '<h3>' . esc_html__( 'Insert Coupon Creator Shortcode', 'coupon-creator' ) . '</h3>';

			// display ui button for 3.5 and greater
			$button = "<style>.select2-container--open{z-index:100100;}.cctor_insert_icon{
								background:url('{$img}') no-repeat top left;
								display: inline-block;
								height: 16px;
								margin: 2px 2px 0 0;
								vertical-align: text-top;
								width: 16px;
							}
							.wp-core-ui a.cctor_insert_link{
								padding-left: 0.4em;
							}
							#TB_title h3 {
								margin: 0;
								color: #2a5a8a;
								text-shadow: 0 1px 0 #fff;
							}
							.cctor-inserter-section-bt,
							.cctor-inserter-section {
								padding:15px;
								overflow:hidden;
							}
							.cctor-inserter-section-row {
								line-height: 28px;
								margin: 0 0 10px;
								overflow:hidden;
							}
							.cctor-inserter-section label{
								font-weight: 700;
								width: 50%;
								float:left;
							}
							.cctor-inserter-section select{
								width: 48%;
								float:right;
							}
							#coupon-submit {
								float:right;
							}
							.cctor-inserter-upgrade-pro {
								width: 48%;
								float:left;
							}
							.cctor-inserter-upgrade-pro a {
								color: #f32323;
							}
							@media only screen and (max-width: 500px) {
								.cctor-inserter-upgrade-pro,
								.cctor-inserter-section select,
								.cctor-inserter-section label{
									width: 100%;
									float:none;
									display:block;
								}
								.cctor-inserter-upgrade-pro {
									margin: 0 0 10px;
								}
							}
					 </style>";

		$button .= "<a class='thickbox button cctor_insert_link' id='add_cctor_shortcode'  title='{$title}' href='#TB_inline?width=783&height=400&inlineId={$container_id}'><span class='cctor_insert_icon'></span>Add Coupon</a>";

		echo $button;

	} //End Insert Icon Creation

	/*
	* Coupon Inserter Popup Coding and Script
	* @version 1.00
	*/
	public function add_coupon_inline_popup() { ?>
		<!--Script to insert Coupon ShortCode Into Editor -->
		<script>
			//Insert Shortcode into Editor
			function InsertCoupon() {
				var $coupon_align = jQuery( "#coupon_align" );
				var $coupon_select = jQuery( "#coupon_select" );
				var coupon_id = $coupon_select.val();
				var coupon_name, coupon_shortcode, coupon_align, coupon_category, coupon_orderby = '';

				if ( coupon_id == "loop" ) {
					coupon_shortcode = "coupon";
					coupon_category = jQuery( "#coupon_category_select" ).val();
					if ( coupon_category ) {
						coupon_category = " category=\"" + coupon_category + "\" ";
					}

					coupon_orderby = jQuery( "#coupon_orderby" ).val();
					coupon_orderby = " couponorderby=\"" + coupon_orderby + "\" ";
				} else {
					coupon_shortcode = "coupon";
					coupon_category = "";
					coupon_orderby = "";
				}

				coupon_name = $coupon_select.find( "option[value='" + coupon_id + "']" ).text().replace( /[\[\]]/g, '' );
				var cctor_align = $coupon_align.val();
				coupon_align = $coupon_align.find( "option[value='" + cctor_align + "']" ).text().replace( /[\[\]]/g, '' );
				window.send_to_editor( "[" + coupon_shortcode + " couponid=\"" + coupon_id + "\"" + coupon_category + coupon_orderby + " coupon_align=\"" + cctor_align + "\" name=\"" + coupon_name + "\"]" );
			}

			//Resize Thickbox for Coupon Inserter
			function cctor_resize_thickbox() {
				jQuery( function ( $ ) {

					var $tb_window = $( "#TB_window" );

					if ( $tb_window.find( ".cctor-inserter-section" ).length > 0 ) {

						var coupon_thickbox_height = $( '.cctor-inserter-section' ).outerHeight() + $( '.cctor-inserter-section-bt' ).outerHeight() + $( '#TB_title' ).outerHeight();

						var $tb_ajax_content = $( "#TB_ajaxContent" );

						$tb_window.height( ( coupon_thickbox_height + 10 ) );
						$tb_ajax_content.height( ( coupon_thickbox_height  ) );
						$tb_ajax_content.css( {
							'width': '100%',
							'padding': '0'
						} );
					}
				} );
			}

			//Toggle Category Input when Loop Selected
			function show_category() {
				var coupon_select = document.getElementById( "coupon_select" );

				//Do not set var if select cleared
				if ( jQuery( "#coupon_select" ).prop( 'selectedIndex' ) >= 0 ) {
					var coupon_selection = coupon_select.options[coupon_select.selectedIndex].value;
				}
				var category_select = document.getElementById( "coupon_category_select_container" );
				var orderby_select = document.getElementById( "coupon_orderby_select_container" );

				if ( category_select && orderby_select ) {
					if ( coupon_selection == "loop" ) {
						category_select.style.visibility = "visible";
						orderby_select.style.visibility = "visible";
					} else {
						category_select.style.visibility = "hidden";
						orderby_select.style.visibility = "hidden";
					}
				}
				cctor_resize_thickbox();
			}

			jQuery( function ( $ ) {
				//Run On Load to Show Correct Fields
				show_category();

				//On Open of Thickbox Resize
				$( '#add_cctor_shortcode' ).on( 'click', function () {

					setTimeout( function () {
						cctor_resize_thickbox();
					}, 200 ); // delay 500 ms

				} );

				//On Page resize or Load with resize Thickbox
				$( window ).on( 'resize load', function () {
					cctor_resize_thickbox();
				} );

			} );

		</script>

		<style>
			#coupon_orderby_select_container,
			#coupon_category_select_container {
				visibility: hidden;
				margin-top: 15px;
			}
		</style>

		<!--Start Thickbox Popup -->
		<div id="coupon_container" style="display:none;">
			<?php
			$querycoupon = new WP_Query( array( 'post_type' => 'cctor_coupon', 'post_status' => 'publish', 'posts_per_page' => 1, 'no_found_rows' => true ) );
			// The Coupon Loop
			if ( $querycoupon->have_posts() ) {
				?>
				<div class="cctor-inserter-section">
					<!--Create a Select Box with Coupon Titles -->
					<div class="cctor-inserter-section-row">
						<label
							for="coupon_select"><?php echo esc_html__( 'Select Loop or an Individual Coupon', 'coupon-creator' ); ?></label>
						<select name="coupon_select_box" id="coupon_select" onchange="show_category()">
							<option value=""></option>
							<option value="loop"><?php echo esc_html__( 'Coupon Loop', 'coupon-creator' ); ?></option>

						</select> <!--End Select Box Coupons-->
						<script>jQuery(function ($) { $('#coupon_select').select2({ width: '100%', ajax: {
								url: ajaxurl, type: 'POST', dataType: 'json', delay: 250,
								data: function (params) {
									return { action: 'pngx_dropdown', source: 'cctor_coupons', nonce: <?php echo wp_json_encode( wp_create_nonce( 'pngx-dropdown' ) ); ?>, search: params.term || '', page: params.page || 1 };
								},
								processResults: function (response) {
									return response.success ? response.data : { results: [] };
								}
							} }); });</script>
					</div>

					<!--Create a Select Box for Categories -->
					<div id="coupon_category_select_container" class="cctor-inserter-section-row">
						<label
							for="coupon_category_select"><?php echo esc_html__( 'Select a Coupon Category to use in the Loop', 'coupon-creator' ); ?></label>
						<select id="coupon_category_select" name="coupon_category_select">
							<option value=""></option>
							<option value=""><?php echo esc_html__( 'All Categories', 'coupon-creator' ); ?></option>
							<?php
							$cctor_cat_args = array(
								'orderby'  => 'name',
								'order'    => 'ASC',
								'taxonomy' => 'cctor_coupon_category'
							);
							$categories     = get_categories( $cctor_cat_args );


							foreach ( $categories as $category ) {
								$option = '<option value="' . esc_html( $category->name ) . '">';
								$option .= esc_html( $category->cat_name );
								$option .= '</option>';
								echo $option;
							}
							?>
						</select> <!--End Select Box Categories-->
					</div>
					<!--Create a Select Box for Align -->
					<div class="cctor-inserter-section-row">
						<label
							for="coupon_align"><?php echo esc_html__( 'Select How to Align the Coupon(s)', 'coupon-creator' ); ?></label>
						<select name="coupon_align_select_box" id="coupon_align">
							<option value="cctor_alignnone"><?php echo esc_html__( 'None', 'coupon-creator' ); ?></option>
							<option value="cctor_alignleft"><?php echo esc_html__( 'Align Left', 'coupon-creator' ); ?></option>
							<option
								value="cctor_alignright"><?php echo esc_html__( 'Align Right', 'coupon-creator' ); ?></option>
							<option
								value="cctor_aligncenter"><?php echo esc_html__( 'Align Center', 'coupon-creator' ); ?></option>
						</select> <!--End Select Box Align -->
					</div>

					<!--Create a Select Box for Orderby -->
					<div id="coupon_orderby_select_container" class="cctor-inserter-section-row">
						<label
							for="coupon_orderby"><?php echo esc_html__( 'Select how to order the coupons', 'coupon-creator' ); ?></label>
						<select id="coupon_orderby" name="coupon_orberby_select_box">
							<option value="date"><?php echo esc_html__( 'Date (default)', 'coupon-creator' ); ?></option>
							<option value="none"><?php echo esc_html__( 'None', 'coupon-creator' ); ?></option>
							<option value="ID"><?php echo esc_html__( 'ID', 'coupon-creator' ); ?></option>
							<option value="author"><?php echo esc_html__( 'Author', 'coupon-creator' ); ?></option>
							<option value="title"><?php echo esc_html__( 'Coupon Post Title', 'coupon-creator' ); ?></option>
							<option value="name"><?php echo esc_html__( 'Slug Name', 'coupon-creator' ); ?></option>
							<option value="modified"><?php echo esc_html__( 'Last Modified', 'coupon-creator' ); ?></option>
							<option value="rand"><?php echo esc_html__( 'Random', 'coupon-creator' ); ?></option>
						</select> <!--End Select Box Align -->
					</div>

				</div> <!--End Div -->

				<div class="cctor-inserter-section-bt">
					<?php
					//Show Upgrade to Pro Link
					if ( ! defined( 'CCTOR_HIDE_UPGRADE' ) || ! CCTOR_HIDE_UPGRADE ) {
						echo '<div class="cctor-inserter-upgrade-pro"><strong>' . esc_html__( 'Coupons that expire on their own.', 'coupon-creator' ) . '</strong> <a href="http://cctor.link/Abqoi" target="_blank" rel="noopener noreferrer">' . esc_html__( 'Explore Coupon Creator Pro', 'coupon-creator' ) . '</a> ' . esc_html__( 'for advanced coupon controls.', 'coupon-creator' ) . '</div>';
					}
					?>
					<!--Insert into Editor Button that Calls Script-->
					<input type="button" id="coupon-submit" onclick="InsertCoupon();" class="button-primary"
					       value="Insert Coupon" name="submit"/>
				</div>

			<?php } else { ?>
				<h4><?php echo esc_html__( 'No Coupons are Published', 'coupon-creator' ); ?></h4>
			<?php } ?>
		</div> <!--End #coupon_container -->
	<?php }

	/***************************************************************************/

	/** Search published coupons in fixed-size pages. Never accept query args from the browser. */
	public static function search_coupons( $data, $search, $page ) {
		if ( ! current_user_can( 'edit_cctor_coupons' ) ) {
			return array( 'results' => array(), 'pagination' => array( 'more' => false ) );
		}
		$term = is_array( $search ) ? ( $search['term'] ?? '' ) : $search;
		$term = is_scalar( $term ) ? sanitize_text_field( wp_unslash( (string) $term ) ) : '';
		$page = max( 1, (int) $page );
		$query = new WP_Query( array(
			'post_type' => 'cctor_coupon', 'post_status' => 'publish', 'posts_per_page' => 50,
			'paged' => $page, 's' => $term, 'orderby' => array( 'date' => 'DESC', 'ID' => 'DESC' ),
			'update_post_meta_cache' => false, 'update_post_term_cache' => false,
		) );
		/** @var list<WP_Post> $posts */
		$posts = $query->posts ?? array();
		$data = ( new \Pngx\Ajax\Dropdown() )->format_posts_for_dropdown( $posts );
		$results = $data['posts'];
		if ( 1 === $page && '' === $term ) {
			$loops = class_exists( 'Cctor__Coupon__Pro__Main' )
				? array( array( 'id' => 'proloop', 'text' => __( 'Pro Coupon Loop', 'coupon-creator' ) ), array( 'id' => 'basicloop', 'text' => __( 'Basic Coupon Loop', 'coupon-creator' ) ) )
				: array( array( 'id' => 'loop', 'text' => __( 'Coupon Loop', 'coupon-creator' ) ) );
			$results = array_merge( $loops, $results );
		}
		return array( 'results' => $results, 'pagination' => array( 'more' => $page < (int) $query->max_num_pages ) );
	}

	public static function dropdown_capability( $capability, $source ) {
		return 'cctor_coupons' === $source ? 'edit_cctor_coupons' : $capability;
	}

	public function enqueue_search() {
		$screen = get_current_screen();
		if ( $screen && 'post' === $screen->base && 'cctor_coupon' !== $screen->post_type ) {
			wp_enqueue_script( 'pngx-select2' );
			wp_enqueue_style( 'pngx-select2-css' );
		}
	}

}
