<?php
/**
 * The [wpmg] shortcode of the plugin.
 *
 * @link       https://github.com/leandrogoncalves/wp-masonry-grid
 * @since      1.0.0
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/public
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * WP_Masonry_Grid Shortcode Class
 *
 * @package WP_Masonry_Grid_Shortcode
 * @author  Juan Javier Moreno <hello@wannathemes.com>
 *
 * @since 1.0.0
 */
class WP_Masonry_Grid_Shortcode {

	/**
	 * Add shortcode
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		// Register shortcode
		add_shortcode( 'wpmg', array( $this, 'wpmg_shortcode' ) );

	}

	/**
	 * Isotope output
	 *
	 * Retrieves a media files and settings to display a video.
	 *
	 * @since    1.0.0
	 *
	 * @param    array $atts Shortcode attributes
	 */
	public function wpmg_shortcode( $atts ) {

        extract( shortcode_atts( array(
            'id'        => '',
            'class'     => '',
            'type'      => 'post',
            'items'     => 4,
            'order'     => '',
            'order_by'  => 'menu_order',
            'tax'       => '',
            'term'      => '',
        ), $atts) );

        if( null == $id ) {
	       $id = 'wpmg' . md5( date( 'jnYgis' ) );
	    }

        if( null == $term ) {
            $query_args = array(
                'post_type'       => $type,
                'order'           => $order,
                'orderby'         => $order_by,
                'posts_per_page'  => $items
            );
        } else {
            $query_args = array(
                'post_type'       => $type,
                'order'           => $order,
                'orderby'         => $order_by,
                'posts_per_page'  => $items,
                'tax_query' => array(
                    array(
                        'taxonomy' => $tax,
                        'field'    => 'slug',
                        'terms'    => $term,
                    ),
                ),
            );
        }
        $isotope_loop = new WP_Query ( $query_args );
        
        if ( $isotope_loop->have_posts() ) :
            ob_start(); ?>

            <ul id="filters-<?php echo esc_attr( $id ); ?>" class="filters">

                <li>
                    <a href="javascript:void(0)" title="filter all" data-filter=".all" class="active">
                        <?php esc_html_e( 'All', 'wp-mansory-grid' ); ?>
                    </a>
                </li>

            <?php
            if( null != $tax && null == $term ) {

                $terms = get_terms( $tax );
                $count = count($terms); 

                if ( $count > 0 ){
                    foreach ( $terms as $term ) {
                        $termname = strtolower($term->slug); 
                        $title = $term->name; ?>
                        <li>
                            <a href="javascript:void(0)" title="filter <?php echo esc_attr( $title ); ?>" data-filter=".<?php echo esc_attr( $termname ); ?>">
                                <?php echo esc_html( $title ); ?>
                            </a>
                        </li><?php
                    }
                } 

            } elseif ( null != $term ) {

                $term_id = get_term_by( 'slug', $term, $tax );
                $terms = get_term_children( $term_id->term_id, $tax );
                $count = count($terms);

                if ( $count > 0 ){
                    foreach ( $terms as $term ) {
                        $single_term = get_term( $term, $tax );
                        $termslug = strtolower($single_term->slug);
                        $termname = strtolower($single_term->name); ?>
                        <li>
                            <a href="javascript:void(0)" title="filter <?php echo esc_attr( $termslug ); ?>" data-filter=".<?php echo esc_attr( $termslug ); ?>">
                                <?php echo esc_html( $termname ); ?>
                            </a>
                        </li><?php
                    }
                }

            } ?>
            </ul>

            <ul id="<?php echo esc_attr( $id ); ?>" class="isotope-content isotope">
            <?php
            while ( $isotope_loop->have_posts() ) : $isotope_loop->the_post();

                if( null != $tax ) {
                    $tax_terms = get_the_terms( $isotope_loop->ID, $tax );
                    $term_class = '';
                    foreach( (array)$tax_terms as $term ) {
                        $term_class .= $term->slug . ' '; 
                    }
                }

                if( file_exists( plugin_dir_path(__FILE__) . 'templates/loop.php' ) ) {
                    
                    // Load from plugin
                    include( plugin_dir_path(__FILE__) . 'templates/loop.php' );

                }

            endwhile; ?>
            </ul> 

            <?php
            return ob_get_clean();

        endif;

        wp_reset_query();

	}

    private function getJavascript(){
     ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {

                var $container = $('#<?php echo esc_js( $id ); ?>');
                $container.imagesLoaded( function(){
                    $container.isotope({
                        itemSelector: ".isotope-item",
                        layoutMode: "masonry"
                    });
                });

                var $optionSets = $('#filters-<?php echo esc_attr( $id ); ?>'),
                    $optionLinks = $optionSets.find('a');

                $optionLinks.click(function(){
                    var $this = $(this);
                    // don\'t proceed if already active
                    if ( $this.hasClass('active') ) {
                        return false;
                    }
                    var $optionSet = $this.parents('#filters-<?php echo esc_js( $id ); ?>');
                    $optionSets.find('.active').removeClass('active');
                    $this.addClass('active');

                    //When an item is clicked, sort the items.
                    var selector = $(this).attr('data-filter');
                    $container.isotope({ filter: selector });

                    return false;
                });
            });
        </script>

        <?php
    }

}