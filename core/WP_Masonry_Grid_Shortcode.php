<?php
if(!defined('ABSPATH')) die('Wordpress is required');

/**
 * The [wpmg] shortcode of the plugin.
 *
 * @link       https://github.com/leandrogoncalves/wp-masonry-grid
 * @since      1.0.0
 *
 * @package    WP_Masonry_Grid
 * @subpackage WP_Masonry_Grid/src
 */


require_once 'WP_Masonry_Grid.php';

/**
 * WP_Masonry_Grid Shortcode Class
 *
 * @package WP_Masonry_Grid_Shortcode
 * @author  Leandro Goncalves <contato.Leandro Goncalves@gmail.com>
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
     * Isotope output
     *
     * Retrieves a media files and settings to display a video.
     *
     * @since    1.0.0
     *
     * @param    array $atts Shortcode attributes
     */
    public function wpmg_shortcode( $attributes ) {

        $this->page = get_query_var('pagename');

        $atts = shortcode_atts( array(
                                    'id'              => '',
                                    'class'           => '',
                                    'type'            => 'lojas',
                                    'posts_per_page'  => '',
                                    'order'           => 'ASC',
                                    'orderby'         => 'title',
                                    'tax'             => '',
                                    'term'            => '',
                                    'acf'             => '',
                                    'paged'           => '',
                                    'pagination'      => 'default',
                                    'post_status'     => 'publish',
                                ), $attributes);

        foreach ($atts as $k => $att) $this->{$k} = $att;


        if( null == $this->id ) {
            $this->id = 'wpmg' . md5( date( 'jnYgis' ) );
        }

        //Pagina atual de exibição de registros
        $this->paged = !$this->paged ? 1 : $this->paged;

        //Posts por pagina
        $this->posts_per_page = !$this->posts_per_page ? -1 : $this->posts_per_page;

        //Array de nomes dos campos do ACF
        $this->acf = explode(',',$this->acf);

        $this->_update_options($atts);

        $this->loop = $this->getResults(['tax'=>$this->tax]);


        if ( $this->loop->have_posts() ) {
            return  $this->getMansoryMode();
        }else{
            return  " <p>Nenhum resultado encontrado</p> ";
        }



    }

    /**
     * Salvattore mode - return the html structure of columns used in Salvattore mansoury framawork
     *@link http://salvattore.com/
     */
    private function getMansoryMode(){

        $vars = [];

        ob_start();
        ?>
        <div class="masonry-wrapper" data-columns>
            <?php
            $post_thumb = null;
            $posts = [];
            while ( $this->loop->have_posts() ) : $this->loop->the_post();
                $the_id = get_the_ID();

                if( null != $this->tax ) {
                    $tax_terms = get_the_terms($the_id, $this->tax );


                    $segmentos = [];
                    if(!empty($tax_terms)){
                        foreach ($tax_terms as  $tx){
                            $segmentos[] = "<a href='/{$this->page}/{$this->tax}/{$tx->slug}'>{$tx->name}</a>";
                        }
                    }
                    $segmentos = implode(' | ',  $segmentos);

                }

                if( has_post_thumbnail()) {
                    ob_start();
                    the_post_thumbnail( 'medium' );
                    $post_thumb = ob_get_clean();
                }


                $posts[] = [
                    'ID'        => $the_id,
                    'title'     => get_the_title(),
                    'permalink' => get_the_permalink(),
                    'segmentos' => $segmentos,
                    'thumb'     => $post_thumb
                ];



            endwhile;

            $chunk = array_chunk($posts, 3);

            foreach ($chunk as $post){
                ?>
                <div class="masonry-collumn">
                    <?php
                        foreach ($post as $p){
                            $p['customFields'] =  $this->acf ? WP_Masonry_Grid_Static::getACFCustomFields($this->acf, $p['ID'] ) : '';
                            echo $this->view->render('frontend/loop_masonry', $p);
                        }
                    ?>
                </div>
                <?php
            }

            ?>
        </div>
        <?php

        $this->pagination == 'default' && $this->getDefaultPagination();
        $this->pagination == 'ajax' && $this->getAjaxPagination();

        wp_reset_query();

        return ob_get_clean();
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


    private function getAjaxPagination(){

        /** Stop execution if there's only 1 page */
        if( $this->loop->max_num_pages <= 1 ) return;

        wp_nonce_field( 'wpmg-ajax-pagination' , 'wpmg-ajax-pagination-nonce' );

        echo "<div class=\"wpmg-nav\"><ul><li><a href=\"javascript:void(0)\" id='wpmg-loadmore' data-page='1' >Ver  mais</a></li></ul></div>";


    }


    /**
     * Defautl pagination
     */
    private function getDefaultPagination(){


        /** Stop execution if there's only 1 page */
        if( $this->loop->max_num_pages <= 1 ) return;

        $paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
        $max   = intval( $this->loop->max_num_pages );

        /**	Add current page to the array */
        if ( $paged >= 1 )
            $links[] = $paged;

        /**	Add the pages around the current page to the array */
        if ( $paged >= 3 ) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }

        if ( ( $paged + 2 ) <= $max ) {
            $links[] = $paged + 2;
            $links[] = $paged + 1;
        }

        echo '<div class="wpmg-nav"><ul>' . "\n";

        /**	Previous Post Link */
        if ( get_previous_posts_link() )
            printf( '<li>%s</li>' . "\n", get_previous_posts_link() );

        /**	Link to first page, plus ellipses if necessary */
        if ( ! in_array( 1, $links ) ) {
            $class = 1 == $paged ? ' class="active"' : '';

            printf( '<li %s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

            if ( ! in_array( 2, $links ) )
                echo '<li>…</li>';
        }

        /**	Link to current page, plus 2 pages in either direction if necessary */
        sort( $links );
        foreach ( (array) $links as $link ) {
            $class = $paged == $link ? ' class="active"' : '';
            printf( '<li %s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
        }

        /**	Link to last page, plus ellipses if necessary */
        if ( ! in_array( $max, $links ) ) {
            if ( ! in_array( $max - 1, $links ) )
                echo '<li>…</li>' . "\n";

            $class = $paged == $max ? ' class="active"' : '';
            printf( '<li %s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
        }

        /**	Next Post Link */
        if ( get_next_posts_link() )
            printf( '<li>%s</li>' . "\n", get_next_posts_link() );

        echo '</ul></div>' . "\n";

    }


}