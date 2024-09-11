<?php
/**
 * Plugin Name: WP Book
 * Description: A custom plugin to manage books with custom post types, taxonomies, and meta boxes.
 * Author: Arisha
 * Text Domain: wp-book
 * Domain Path: /languages
 */

// Load translation files
function wp_book_load_textdomain() {
    load_plugin_textdomain( 'wp-book', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'wp_book_load_textdomain' );

// Activation hook
function wp_book_activation() {
    wp_book_create_post_type();
    wp_book_create_taxonomies();
    flush_rewrite_rules(); // This ensures that rewrite rules are updated after activation.
}
register_activation_hook( __FILE__, 'wp_book_activation' );

// Custom Post Type: Book
function wp_book_create_post_type() {
    $labels = array(
        'name'               => __( 'Books', 'wp-book' ),
        'singular_name'      => __( 'Book', 'wp-book' ),
        'menu_name'          => __( 'Books', 'wp-book' ),
        'name_admin_bar'     => __( 'Book', 'wp-book' ),
        'add_new'            => __( 'Add New', 'wp-book' ),
        'add_new_item'       => __( 'Add New Book', 'wp-book' ),
        'edit_item'          => __( 'Edit Book', 'wp-book' ),
        'new_item'           => __( 'New Book', 'wp-book' ),
        'view_item'          => __( 'View Book', 'wp-book' ),
        'all_items'          => __( 'All Books', 'wp-book' ),
        'search_items'       => __( 'Search Books', 'wp-book' ),
        'not_found'          => __( 'No books found', 'wp-book' ),
        'not_found_in_trash' => __( 'No books found in Trash', 'wp-book' ),
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'has_archive'        => true,
        'supports'           => array( 'title', 'editor', 'thumbnail' ),
        'rewrite'            => array( 'slug' => 'books' ),
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-book-alt',
    );
    register_post_type( 'book', $args );
}
add_action( 'init', 'wp_book_create_post_type' );

// Create Book Category (Hierarchical) Taxonomy
function wp_book_create_taxonomies() {
    // Book Category
    $labels = array(
        'name'              => __( 'Book Categories', 'wp-book' ),
        'singular_name'     => __( 'Book Category', 'wp-book' ),
        'search_items'      => __( 'Search Book Categories', 'wp-book' ),
        'all_items'         => __( 'All Book Categories', 'wp-book' ),
        'parent_item'       => __( 'Parent Book Category', 'wp-book' ),
        'parent_item_colon' => __( 'Parent Book Category:', 'wp-book' ),
        'edit_item'         => __( 'Edit Book Category', 'wp-book' ),
        'update_item'       => __( 'Update Book Category', 'wp-book' ),
        'add_new_item'      => __( 'Add New Book Category', 'wp-book' ),
        'new_item_name'     => __( 'New Book Category Name', 'wp-book' ),
        'menu_name'         => __( 'Book Categories', 'wp-book' ),
    );

    $args = array(
        'hierarchical'      => true,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'book-category' ),
    );

    register_taxonomy( 'book_category', array( 'book' ), $args );

    // Book Tag (Non-hierarchical) Taxonomy
    $labels = array(
        'name'              => __( 'Book Tags', 'wp-book' ),
        'singular_name'     => __( 'Book Tag', 'wp-book' ),
        'search_items'      => __( 'Search Book Tags', 'wp-book' ),
        'all_items'         => __( 'All Book Tags', 'wp-book' ),
        'edit_item'         => __( 'Edit Book Tag', 'wp-book' ),
        'update_item'       => __( 'Update Book Tag', 'wp-book' ),
        'add_new_item'      => __( 'Add New Book Tag', 'wp-book' ),
        'new_item_name'     => __( 'New Book Tag Name', 'wp-book' ),
        'menu_name'         => __( 'Book Tags', 'wp-book' ),
    );

    $args = array(
        'hierarchical'      => false,
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'book-tag' ),
    );

    register_taxonomy( 'book_tag', array( 'book' ), $args );
}
add_action( 'init', 'wp_book_create_taxonomies' );

// Meta Box for Book Information
function wp_book_add_meta_box() {
    add_meta_box(
        'wp_book_meta_box',         // ID
        __( 'Book Information', 'wp-book' ), // Title
        'wp_book_meta_box_callback', // Callback
        'book',                     // Post type
        'normal',                   // Context
        'high'                      // Priority
    );
}
add_action( 'add_meta_boxes', 'wp_book_add_meta_box' );

function wp_book_meta_box_callback( $post ) {
    // Add nonce for security
    wp_nonce_field( 'wp_book_save_meta_box_data', 'wp_book_meta_box_nonce' );

    // Retrieve existing book meta data
    $author = get_post_meta( $post->ID, '_wp_book_author', true );
    $price = get_post_meta( $post->ID, '_wp_book_price', true );
    $publisher = get_post_meta( $post->ID, '_wp_book_publisher', true );
    $year = get_post_meta( $post->ID, '_wp_book_year', true );
    $edition = get_post_meta( $post->ID, '_wp_book_edition', true );
    $url = get_post_meta( $post->ID, '_wp_book_url', true );

    // Output fields for meta box
    echo '<label for="wp_book_author">' . __( 'Author', 'wp-book' ) . '</label>';
    echo '<input type="text" id="wp_book_author" name="wp_book_author" value="' . esc_attr( $author ) . '" size="25" />';
    
    echo '<label for="wp_book_price">' . __( 'Price', 'wp-book' ) . '</label>';
    echo '<input type="text" id="wp_book_price" name="wp_book_price" value="' . esc_attr( $price ) . '" size="25" />';

    echo '<label for="wp_book_publisher">' . __( 'Publisher', 'wp-book' ) . '</label>';
    echo '<input type="text" id="wp_book_publisher" name="wp_book_publisher" value="' . esc_attr( $publisher ) . '" size="25" />';

    echo '<label for="wp_book_year">' . __( 'Year', 'wp-book' ) . '</label>';
    echo '<input type="text" id="wp_book_year" name="wp_book_year" value="' . esc_attr( $year ) . '" size="25" />';

    echo '<label for="wp_book_edition">' . __( 'Edition', 'wp-book' ) . '</label>';
    echo '<input type="text" id="wp_book_edition" name="wp_book_edition" value="' . esc_attr( $edition ) . '" size="25" />';

    echo '<label for="wp_book_url">' . __( 'URL', 'wp-book' ) . '</label>';
    echo '<input type="url" id="wp_book_url" name="wp_book_url" value="' . esc_attr( $url ) . '" size="25" />';
}

function wp_book_save_meta_box_data( $post_id ) {
    // Check if nonce is set
    if ( ! isset( $_POST['wp_book_meta_box_nonce'] ) ) {
        return;
    }

    // Verify nonce
    if ( ! wp_verify_nonce( $_POST['wp_book_meta_box_nonce'], 'wp_book_save_meta_box_data' ) ) {
        return;
    }

    // Check if the current user has permission to edit the post
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Update book meta data
    if ( isset( $_POST['wp_book_author'] ) ) {
        update_post_meta( $post_id, '_wp_book_author', sanitize_text_field( $_POST['wp_book_author'] ) );
    }

    if ( isset( $_POST['wp_book_price'] ) ) {
        update_post_meta( $post_id, '_wp_book_price', sanitize_text_field( $_POST['wp_book_price'] ) );
    }

    if ( isset( $_POST['wp_book_publisher'] ) ) {
        update_post_meta( $post_id, '_wp_book_publisher', sanitize_text_field( $_POST['wp_book_publisher'] ) );
    }

    if ( isset( $_POST['wp_book_year'] ) ) {
        update_post_meta( $post_id, '_wp_book_year', sanitize_text_field( $_POST['wp_book_year'] ) );
    }

    if ( isset( $_POST['wp_book_edition'] ) ) {
        update_post_meta( $post_id, '_wp_book_edition', sanitize_text_field( $_POST['wp_book_edition'] ) );
    }

    if ( isset( $_POST['wp_book_url'] ) ) {
        update_post_meta( $post_id, '_wp_book_url', esc_url_raw( $_POST['wp_book_url'] ) );
    }
}
add_action( 'save_post', 'wp_book_save_meta_box_data' );
// Create custom table for storing book meta information
function wp_book_create_meta_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'book_meta';
    
    if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            book_id bigint(20) NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext NOT NULL,
            PRIMARY KEY  (id),
            KEY book_id (book_id),
            KEY meta_key (meta_key)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }
}
register_activation_hook( __FILE__, 'wp_book_create_meta_table' );

// Function to save custom meta in custom table
function wp_book_save_custom_meta( $post_id ) {
    global $wpdb;

    // Make sure it's a book post type
    if ( 'book' != get_post_type( $post_id ) ) {
        return;
    }

    // Check if custom meta box fields are set
    $meta_keys = ['wp_book_author', 'wp_book_price', 'wp_book_publisher', 'wp_book_year', 'wp_book_edition', 'wp_book_url'];
    
    foreach ( $meta_keys as $key ) {
        if ( isset( $_POST[$key] ) ) {
            // Sanitize and save meta values in custom table
            $meta_value = sanitize_text_field( $_POST[$key] );
            $wpdb->insert(
                $wpdb->prefix . 'book_meta',
                array(
                    'book_id' => $post_id,
                    'meta_key' => $key,
                    'meta_value' => $meta_value
                ),
                array( '%d', '%s', '%s' )
            );
        }
    }
}
add_action( 'save_post', 'wp_book_save_custom_meta' );
// Create custom admin settings page
function wp_book_add_admin_menu() {
    add_submenu_page(
        'edit.php?post_type=book',
        __( 'Book Settings', 'wp-book' ),
        __( 'Settings', 'wp-book' ),
        'manage_options',
        'wp-book-settings',
        'wp_book_settings_page'
    );
}
add_action( 'admin_menu', 'wp_book_add_admin_menu' );

// Register settings
function wp_book_register_settings() {
    register_setting( 'wp_book_options_group', 'wp_book_currency' );
    register_setting( 'wp_book_options_group', 'wp_book_books_per_page' );
}
add_action( 'admin_init', 'wp_book_register_settings' );

// Settings page content
function wp_book_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Book Settings', 'wp-book' ); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'wp_book_options_group' ); ?>
            <?php do_settings_sections( 'wp_book_options_group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( 'Currency', 'wp-book' ); ?></th>
                    <td><input type="text" name="wp_book_currency" value="<?php echo esc_attr( get_option( 'wp_book_currency', '$' ) ); ?>" /></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Books per page', 'wp-book' ); ?></th>
                    <td><input type="number" name="wp_book_books_per_page" value="<?php echo esc_attr( get_option( 'wp_book_books_per_page', '10' ) ); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
// Shortcode to display book information
function wp_book_shortcode( $atts ) {
    // Attributes: id, author_name, year, category, tag, and publisher
    $atts = shortcode_atts( array(
        'id' => '',
        'author_name' => '',
        'year' => '',
        'category' => '',
        'tag' => '',
        'publisher' => '',
    ), $atts, 'book' );

    global $wpdb;
    
    // Query to fetch book information
    $query = "SELECT * FROM {$wpdb->prefix}posts WHERE post_type = 'book' AND post_status = 'publish' ";
    if ( ! empty( $atts['id'] ) ) {
        $query .= $wpdb->prepare( "AND ID = %d", $atts['id'] );
    }

    $books = $wpdb->get_results( $query );

    // Display book information
    $output = '<div class="wp-book-list">';
    foreach ( $books as $book ) {
        $output .= '<h3>' . esc_html( get_the_title( $book->ID ) ) . '</h3>';
        $output .= '<p>' . esc_html( get_the_content( null, false, $book->ID ) ) . '</p>';
    }
    $output .= '</div>';

    return $output;
}
add_shortcode( 'book', 'wp_book_shortcode' );
// Custom Widget to display books of a selected category
class WP_Book_Category_Widget extends WP_Widget {
    function __construct() {
        parent::__construct(
            'wp_book_category_widget',
            __( 'Book Category Widget', 'wp-book' ),
            array( 'description' => __( 'Display books from a selected category', 'wp-book' ) )
        );
    }

    public function widget( $args, $instance ) {
        $category = ! empty( $instance['category'] ) ? $instance['category'] : __( 'Uncategorized', 'wp-book' );

        echo $args['before_widget'];
        echo $args['before_title'] . $category . $args['after_title'];

        // Query to fetch books from the selected category
        $query = new WP_Query( array(
            'post_type' => 'book',
            'tax_query' => array(
                array(
                    'taxonomy' => 'book_category',
                    'field'    => 'slug',
                    'terms'    => $category,
                ),
            ),
        ));

        if ( $query->have_posts() ) {
            echo '<ul>';
            while ( $query->have_posts() ) {
                $query->the_post();
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';
        }

        echo $args['after_widget'];
    }

    public function form( $instance ) {
        $category = ! empty( $instance['category'] ) ? $instance['category'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php _e( 'Category:', 'wp-book' ); ?></label> 
            <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'category' ) ); ?>" type="text" value="<?php echo esc_attr( $category ); ?>">
        </p>
        <?php 
    }

    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['category'] = ( ! empty( $new_instance['category'] ) ) ? strip_tags( $new_instance['category'] ) : '';
        return $instance;
    }
}
add_action( 'widgets_init', function(){
    register_widget( 'WP_Book_Category_Widget' );
});
// Add Dashboard Widget to display top 5 book categories
function wp_book_dashboard_widget() {
    wp_add_dashboard_widget(
        'wp_book_dashboard_widget', 
        __( 'Top 5 Book Categories', 'wp-book' ), 
        'wp_book_dashboard_widget_content'
    );
}
add_action( 'wp_dashboard_setup', 'wp_book_dashboard_widget' );

function wp_book_dashboard_widget_content() {
    $terms = get_terms( array(
        'taxonomy' => 'book_category',
        'orderby'  => 'count',
        'order'    => 'DESC',
        'number'   => 5,
    ));

    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
        echo '<ul>';
        foreach ( $terms as $term ) {
            echo '<li>' . esc_html( $term->name ) . ' (' . $term->count . ' books)</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>' . __( 'No categories found.', 'wp-book' ) . '</p>';
    }
}
