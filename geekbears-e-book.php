<?php
/**
* Plugin Name: Geekbears E-book
* Plugin URI: https://www.geekbears.com/
* Description: This is the geekbears E-Books plugin.
* Version: 1.0
* Author: Wuilly Vargas
* Author URI: http://instagram.com/wuilly.vargas
**/

/* ---------------------------------------------------------------------------
 * Function to load script and css files
 * --------------------------------------------------------------------------- */
function load_ebook_file(){
    wp_enqueue_style('ebook-style', plugin_dir_url( __FILE__ ) . '/css/style.css', array(), '1.0.1', 'all');
    wp_enqueue_script( 'ebook-script',  plugin_dir_url( __FILE__ ) . '/js/ebook-ajax.js', array('jquery')); // jQuery will be included automatically
    wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) ); // setting ajaxurl
}
add_action( 'wp_enqueue_scripts','load_ebook_file' );

/* ---------------------------------------------------------------------------
 * Function to cut text strings
 * --------------------------------------------------------------------------- */
function wv_trim_text($text, $limit=100){   
    $text = trim($text);
    $text = strip_tags($text);
    $size = strlen($text);
    $result = '';
    if($size <= $limit){
        return $text;
    }else{
        $text = substr($text, 0, $limit);
        $words = explode(' ', $text);
        $result = implode(' ', $words);
        $result .= '...';
    }   
    return $result;
}
/* ---------------------------------------------------------------------------
 * Post Type Ebook
 * --------------------------------------------------------------------------- */
function wv_ebook_posts() {
 
    $labels = array(
        'name'                => _x( 'E-books', 'Post Type General Name', 'geekbears' ),
        'singular_name'       => _x( 'E-book', 'Post Type Singular Name', 'geekbears' ),
        'menu_name'           => __( 'E-books', 'geekbears' ),
        'parent_item_colon'   => __( 'Parent E-book', 'geekbears' ),
        'all_items'           => __( 'All E-books', 'geekbears' ),
        'view_item'           => __( 'View E-book', 'geekbears' ),
        'add_new_item'        => __( 'Add New E-book', 'geekbears' ),
        'add_new'             => __( 'Add New', 'geekbears' ),
        'edit_item'           => __( 'Edit E-book', 'geekbears' ),
        'update_item'         => __( 'Update E-book', 'geekbears' ),
        'search_items'        => __( 'Search E-book', 'geekbears' ),
        'not_found'           => __( 'Not Found', 'geekbears' ),
        'not_found_in_trash'  => __( 'Not found in Trash', 'geekbears' ),
    );
     
    $args = array(
        'label'               => __( 'E-book', 'geekbears' ),
        'description'         => __( 'E-book custom post type', 'geekbears' ),
        'labels'              => $labels,
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        'taxonomies'          => array( 'e-book-category' ),
        'hierarchical'        => false,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_nav_menus'   => true,
        'show_in_admin_bar'   => true,
        'menu_position'       => 5,
        'menu_icon'           => 'dashicons-book',
        'can_export'          => true,
        'has_archive'         => true,
        'exclude_from_search' => false,
        'publicly_queryable'  => true,
        'capability_type'     => 'post',
    );

    register_post_type( 'ebook', $args );
} 
add_action( 'init', 'wv_ebook_posts', 0 );


function wv_ebook_posts_taxonomies() {
    $labels = array(
        'name' => _x( 'Categories', 'taxonomy general name' ),
        'singular_name' => _x( 'Category', 'taxonomy singular name' ),
        'search_items' =>  __( 'Search by Category' ),
        'all_items' => __( 'All Categories' ),
        'parent_item' => __( 'Parent Category' ),
        'parent_item_colon' => __( 'Parent Category:' ),
        'edit_item' => __( 'Edit Category' ),
        'update_item' => __( 'Update Category' ),
        'add_new_item' => __( 'Add New Category' ),
        'new_item_name' => __( 'Name of Category' ),
    );

    $args = array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'query_var' => true,
        //'rewrite' => array( 'slug' => 'category' ),
    );
    
    register_taxonomy('ebook-category', 'ebook', $args);
}
add_action( 'init', 'wv_ebook_posts_taxonomies', 0 );


function wv_add_meta_boxes_by_ebook( $post ){
    add_meta_box( 'ebook_meta_box', __( 'E-book Details', 'ebook_details' ), 'ebook_build_meta_box', 'ebook', 'normal', 'high' );
}
add_action( 'add_meta_boxes_ebook', 'wv_add_meta_boxes_by_ebook' );

function ebook_build_meta_box( $post ){
    // make sure the form request comes from WordPress
    wp_nonce_field( basename( __FILE__ ), 'ebook_meta_box_nonce' );
    // retrieve the _project_link current value
    $amazon = get_post_meta( $post->ID, '_ebook_amazon', true );
    ?>
    <div class='inside'>
        <h3><?php _e( 'Amazon link', 'ebook_geekbears' ); ?></h3>
        <p><input type="text" name="amazon" value="<?php echo $amazon; ?>" style="width: 100%;"/></p>
    </div>
    <?php
}

/**
 * Save meta box content.
 *
 * @param int $post_id Post ID
 */
function wv_save_meta_box_ebook( $post_id ) {
    // verify meta box nonce
    if ( !isset( $_POST['ebook_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['ebook_meta_box_nonce'], basename( __FILE__ ) ) ){
        return;
    }
    // return if autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
        return;
    }
    // Check the user's permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ){
        return;
    }
    // store custom fields values
    if ( isset( $_REQUEST['amazon'] ) ) {
        update_post_meta( $post_id, '_ebook_amazon', sanitize_text_field( $_POST['amazon'] ) );
    }
}
add_action( 'save_post_ebook', 'wv_save_meta_box_ebook' );

/* ---------------------------------------------------------------------------
 * [wv_ebook_load_more_ajax]
 * --------------------------------------------------------------------------- */
if( ! function_exists( 'wv_ebook_load_more_ajax' ) ){
    function wv_ebook_load_more_ajax( $attr, $content = null )
    {
        ob_start();
        extract(shortcode_atts(array(
            'count'             => 6,
            'excerpt'           => '1',
        ), $attr));
        
        $args = array(
            'post_type'             => 'ebook',
            'posts_per_page'        => intval($count),
            'no_found_rows'         => 1,
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 0,
        );

        $query = new WP_Query( $args );?>
        
        <div id="ebook-box">
            <div class="pgafu-filter-wrp">  
                
                <ul class="pgafu-filter">
                    <li class="pgafu-filtr-cat pgafu-active-filtr"><a href="" id="all-items" class="active">All</a></li>
                    <?php 
                        $taxonomy = 'ebook-category';
                        $terms = get_terms($taxonomy);
                        if ( $terms && !is_wp_error( $terms ) ) {
                            foreach ( $terms as $term ) { ?>
                            <li class="pgafu-filtr-cat"><a href="<?php echo get_term_link($term->slug, $taxonomy); ?>" id="<?php echo $term->term_id; ?>"><?php echo $term->name; ?></a></li>
                        <?php }
                        }
                    ?>
                </ul>
                
                <?php if ($query->have_posts()) :?>
                    <div class="pgafu-filtr-container">
                        <div id="container-grid-ebook" class="pgafu-post-grid-main pgafu-post-filter pgafu-design-1 pgafu-image-fit has-no-animation pgafu-clearfix">
                            <?php 
                                while( $query->have_posts() ){
                                    $query->the_post();
                                    require('partials/content-loop.php');                        
                                }                
                                wp_reset_postdata();
                            ?>                        
                        </div>
                    </div>
                <?php endif; ?>
                
            </div>
            
            <div class="text-center load_more_wrapper"><a href="#" class="load_more_item" id="load_more_button"><span>VIEW MORE</span></a></div>
        </div>
        <?php 
        return ob_get_clean();
    }
}
add_shortcode( 'wv_ebook_load_more_ajax', 'wv_ebook_load_more_ajax' );


function load_ebook() {

    $id = $_POST['term_id'];
    $paged = $_POST['paged'];
    $args = array(
        'tax_query' => array(
            array(
                'taxonomy' => 'ebook-category',
                'terms' => array($id),
                'field' => 'term_id',
            ),
        ),
        'post_type'         => 'ebook',
        'posts_per_page'    => 6,
        'post_status'       => 'publish',
        'paged'             => $paged
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post(); 
            require 'partials/content-loop.php';  
        }
    }
    wp_reset_postdata();
    die(); 
}
add_action( 'wp_ajax_load_ebook', 'load_ebook' );
add_action( 'wp_ajax_nopriv_load_ebook', 'load_ebook' );


function load_more_ebook() {

    $id = $_POST['term_id'];
    $paged = $_POST['paged'];
    $args = array(
        'tax_query' => array(
            array(
                'taxonomy' => 'ebook-category',
                'terms' => array($id),
                'field' => 'term_id',
            ),
        ),
        'post_type'         => 'ebook',
        'posts_per_page'    => 6,
        'post_status'       => 'publish',
        'paged'             => $paged
    );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post(); 
            require 'partials/content-loop.php';  
        }
    }
    wp_reset_postdata();
    die(); 
}
add_action( 'wp_ajax_load_more_ebook', 'load_more_ebook' );
add_action( 'wp_ajax_nopriv_load_more_ebook', 'load_more_ebook' );


function load_all_ebook() {

    $paged = $_POST['paged'];
    $args = array(
            'post_type'             => 'ebook',
            'posts_per_page'        => 6,
            'no_found_rows'         => 1,
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 0,
            'paged'             => $paged
        );

    $query = new WP_Query($args);

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post(); 
            require 'partials/content-loop.php';  
        }
    }
    wp_reset_postdata();
    die(); 
}
add_action( 'wp_ajax_load_all_ebook', 'load_all_ebook' );
add_action( 'wp_ajax_nopriv_load_all_ebook', 'load_all_ebook' );


/* ---------------------------------------------------------------------------
 * [wv_ebook_load_featured]
 * --------------------------------------------------------------------------- */
if( ! function_exists( 'wv_ebook_load_featured' ) ){
    function wv_ebook_load_featured( $attr, $content = null )
    {
        ob_start();
        extract(shortcode_atts(array(
            'count'             => 1,
            'excerpt'           => '1',
        ), $attr));
        
        $args = array(
            'post_type'             => 'ebook',
            'posts_per_page'        => intval($count),
            'no_found_rows'         => 1,
            'post_status'           => 'publish',
            'ignore_sticky_posts'   => 0,
        );

        $query = new WP_Query( $args );?>  
  
        <?php if ($query->have_posts()) :?>
            <div class="featured-ebook">
                <?php 
                    while( $query->have_posts() ){
                        $query->the_post();
                        require('partials/content-featured.php');                        
                    }                
                    wp_reset_postdata();
                ?>                        
            </div>
        <?php endif; ?>
            
        <?php 
        return ob_get_clean();
    }
}
add_shortcode( 'wv_ebook_load_featured', 'wv_ebook_load_featured' );

