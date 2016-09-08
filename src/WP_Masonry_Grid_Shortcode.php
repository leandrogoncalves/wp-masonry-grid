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


require_once 'WP_Masonry_Grid.php';

/**
 * WP_Masonry_Grid Shortcode Class
 *
 * @package WP_Masonry_Grid_Shortcode
 * @author  Juan Javier Moreno <hello@wannathemes.com>
 *
 * @since 1.0.0
 */
class WP_Masonry_Grid_Shortcode extends WP_Masonry_Grid{



    /**
     * Add shortcode
     *
     * @since    1.0.0
     */
    public function __construct() {
        parent::__construct();
        // Register shortcode
        add_shortcode( 'wpmg', array( $this, 'wpmg_shortcode' ) );

    }

  

    /**
     * Get a custom post from ACF plugin
     * @param $fieldNames
     * @param $id
     */
    private function setACFCustomFields($fieldNames, $id){

        if(function_exists('get_field')){
            $acfFiels = [];
            if(is_array($fieldNames)){
                foreach ($fieldNames as $field) {
                    $acfFiels[$field]  =  get_field($field, $id);
                }
            }else{
                $acfFiels = array($fieldNames => get_field($fieldNames, $id));
            }
            $this->customFields = array ($id => $acfFiels);

        }else{
            wp_die('O plugin Advanced Custom Fields é necessario');
        }
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
    public function wpmg_shortcode( $attributes ) {

        $atts = shortcode_atts( array(
                                    'id'        => '',
                                    'class'     => '',
                                    'type'      => 'post',
                                    'per_page'  => '',
                                    'order'     => 'ASC',
                                    'order_by'  => 'post_title',
                                    'tax'       => '',
                                    'term'      => '',
                                    'acf'       => '',
                                    'paged'     => '',
                                ), $attributes);

        foreach ($atts as $k => $att) $this->{$k} = $att;

        if( null == $this->id ) {
            $this->id = 'wpmg' . md5( date( 'jnYgis' ) );
        }

        $this->paged = TRUE == empty($this->paged) ? 1 : $this->paged;
        $this->per_page = TRUE == empty($this->per_page) ? -1 : $this->paged;

        $this->acf = explode(',',$this->acf);

        $this->site_url = get_site_url();

        $this->loop = new WP_Query ( $this->getArgs() );

        ob_start();

        if ( $this->loop->have_posts() ) {

            $this->getMansoryMode();

        }else{
            ?>
            <p>Nenhum resultado encontrado</p>
            <?php
        }

        return ob_get_clean();

        wp_reset_query();

    }


    /**
     * Mansory mode
     * @param $atts
     * @param $the_loop
     */
    private function getMansoryMode(){

        ?>
        <article class="masonry-wrapper">
            <?php
            while ( $this->loop->have_posts() ) : $this->loop->the_post();

                $this->ID = get_the_ID();
                $this->title = get_the_title();
                $this->permalink = get_the_permalink();


                if( null != $this->tax ) {
                    $tax_terms = get_the_terms($this->ID, $this->tax );

                    $this->seguimentos = [];
                    if(!empty($tax_terms)){
                        foreach ($tax_terms as  $tx){
                            $this->seguimentos[] = "<a href='?wpmg_tax={$tx->slug}'>{$tx->name}</a>";
                        }
                    }
                    $this->seguimentos = implode(' | ',  $this->seguimentos);

                }


                if($this->acf) $this->setACFCustomFields($this->acf, $this->ID);

                $this->render('loop_masonry');


            endwhile;
            ?>
        </article>
        <?php

    }



    /**
     * Isotope mode
     * @param $atts
     * @param $the_loop
     */
    private function getIsotopeMode(){

        ?>
        <ul id="filters-<?php echo esc_attr(  $this->id ); ?>" class="filters">

            <li>
                <a href="javascript:void(0)" title="filter all" data-filter=".all" class="active">
                    <?php esc_html_e( 'All', 'wp-mansory-grid' ); ?>
                </a>
            </li>

            <?php
            if( null !=  $this->tax && null ==  $this->term ) {

                $terms = get_terms(  $this->tax );
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

            } elseif ( null !=  $this->term ) {

                $term_id = get_term_by( 'slug',  $this->term,  $this->tax );
                $terms = get_term_children( $term_id->term_id,  $this->tax );
                $count = count($terms);

                if ( $count > 0 ){
                    foreach ( $terms as $term ) {
                        $single_term = get_term( $term,  $this->tax );
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

        <ul id="<?php echo esc_attr( $this->id); ?>" class="isotope-content isotope">
            <?php
            while ( $this->loop->have_posts() ) : $this->loop->the_post();

                if( null != $this->tax ) {
                    $tax_terms = get_the_terms( $this->loop->ID, $this->tax );
                    $this->term_class = '';
                    foreach( (array)$tax_terms as $term ) {
                        $this->term_class .= $term->slug . ' ';
                    }
                }

                $this->render('loop_isotope');

            endwhile; ?>
        </ul>

        <script type="text/javascript">
            jQuery(document).ready(function($) {

                var $container = $('#<?php echo esc_js( $this->id); ?>');
                $container.imagesLoaded( function(){
                    $container.isotope({
                        itemSelector: ".isotope-item",
                        layoutMode: "masonry"
                    });
                });

                var $optionSets = $('#filters-<?php echo esc_attr( $this->id ); ?>'),
                    $optionLinks = $optionSets.find('a');

                $optionLinks.click(function(){
                    var $this = $(this);
                    // don\'t proceed if already active
                    if ( $this.hasClass('active') ) {
                        return false;
                    }
                    var $optionSet = $this.parents('#filters-<?php echo esc_js( $this->id ); ?>');
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