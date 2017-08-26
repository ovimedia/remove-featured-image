<?php
/*
Plugin Name: WP Remove featured image
Description: Remove the featured image of any post type.
Author: Ovi García - ovimedia.es
Author URI: http://www.ovimedia.es/
Text Domain: remove-featured-image
Version: 0.1
Plugin URI: https://github.com/ovimedia/remove-featured-image
*/

if ( ! defined( 'ABSPATH' ) ) exit; 

if ( ! class_exists( 'remove_featured_image' ) ) 
{
	class remove_featured_image 
    {        
        function __construct() 
        {   
            add_action( 'init', array( $this, 'rfi_load_languages') );
            add_action( 'admin_menu', array( $this, 'rfi_admin_menu' )); 
        }

        public function rfi_load_languages() 
        {
            load_plugin_textdomain( 'remove-featured-image', false, '/'.basename( dirname( __FILE__ ) ) . '/languages/' ); 
        }

        public function rfi_admin_menu() 
        {	
            $menu = add_menu_page( 'Remove featured image', 'Remove featured image', 'read',  
                                  'remove-featured-image', array( $this,'rfi_options'), 'dashicons-dismiss', 70);
        }    

        public function rfi_options()
        {
            require_once( ABSPATH . 'wp-admin/includes/image.php' );
            
            ?>

            <form action="<?php echo get_admin_url()."admin.php?page=remove-featured-image"; ?>" method="post" >

                <h4><?php echo translate( 'Click the button to remove the featured image for any selected post type.', 'remove-featured-image' ); ?></h4>

                <p><label for="post_type"><?php echo translate( 'Select post type to remove featured images:', 'remove-featured-image' ); ?></label>
                
                <select id="post_type" name="post_type">

                    <?php
                        global $wpdb;

                        $results = $wpdb->get_results( 'SELECT DISTINCT post_type FROM '.$wpdb->prefix.'posts 
                        WHERE post_status like "publish" and post_type <> "nav_menu_item" 
                        and post_type <> "wpcf7_contact_form" order by 1 asc'  );
        
                        $post_types = array();

                        foreach ( $results as $row )
                        {
                            $post_types[] = $row->post_type;
                            
                            echo '<option ';

                            if( in_array($row->post_type, $types[0]) )
                                echo ' selected="selected" ';

                            echo ' value="'.$row->post_type.'">'.ucfirst ($row->post_type).'</option>';
                        } 

                    ?>
                
                </select></p>
               
                <input type="submit"  class="button button-primary"  value="<?php echo translate( 'Remove featured images', 'remove-featured-image' ); ?>" />

            </form>

            <?php

            if(isset($_REQUEST["post_type"] )) 
            {
                $args = array(
                'numberposts' =>   -1,
		        'post_status'      => 'publish',
                'post_type' => $_REQUEST['post_type'],
                ); 

                $posts = get_posts($args); 

                $x = $total = 0;

                foreach($posts as $post)
                {
                    if(delete_post_thumbnail($post->ID)) $total++;

                    $x++;
                    
                    if($x % 10 == 0)
                        sleep(1);              
                }

                echo "<p>".translate( 'Featured images successfully removed. Total:', 'remove-featured-image' )." ".$total."</p>";
            }
        }



    }
}

$GLOBALS['remove_featured_image'] = new remove_featured_image();   
    
?>
