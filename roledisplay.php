<?php
/**
 * @since              1.0.0
 * @package           RoleDisplay
 *
 * @wordpres_plugin
 * Plugin Name:       Role Display Notification
 * Plugin URI:        https://karenattfield.com
 * Description:       Displays a notice with the user role plus a link to it's capabilities
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Karen Attfield
 * Author URI:        https://karenattfield.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       roledisplay
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// if( !defined( 'WPCMN_VER' ) )
// 	define( 'WPCMN_VER', '1.0.0' );



if ( ! class_exists( 'RoleDisplay' ) ) {

    class RoleDisplay {
        
        /**
         * Constructor
         */
        public function __construct() {
            $this->setup_actions();
        }
        
        /**
         * Setting up Hooks
         */
        public function setup_actions() {

            // Main plugin hooks
            register_activation_hook( DIR_PATH, array( $this, 'activate' ) );
            register_deactivation_hook( DIR_PATH, array( $this, 'deactivate' ) );
            add_action( 'plugins_loaded', array( $this, 'textdomain') );
            // Add role style notice and meta key for dismissal
            add_action( 'admin_init', array( $this, 'roledisplay_notice_dismissed') );
            add_action( 'admin_notices', array( $this, 'roledisplay_admin_notice') );
            // Add role notice reset option to user profile pages
            add_action( 'show_user_profile', array( $this, 'roledisplay_usermeta_form_field') );
			add_action( 'edit_user_profile', array( $this, 'roledisplay_usermeta_form_field') );

        }

    	/**
    	 * load textdomain
    	 *
    	 * @return void
    	 */
    	public function textdomain() {

    		load_plugin_textdomain( 'roledisplay', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    	}

    	/**
    	 * create the admin display
    	 *
    	 * @return void
    	 */
    	public function roledisplay_admin_notice(  ) {

    	 	if( is_user_logged_in() ) { // check if there is a logged in user 

    	 		$user = wp_get_current_user(); // getting & setting the current user 
    	 		$roles = ( array ) $user->roles; // obtaining the role 
                $thisrole = $roles[0];
                $user_id = get_current_user_id(); 

                // if 'roledisplay_notice_dismissed' is not set to true, echo the role notice
                if ( ! (get_user_meta( $user_id, 'roledisplay_notice_dismissed')[0] == 'true' )) {

                    $wpurl = __( 'https://wordpress.org/support/article/roles-and-capabilities/', 'roledisplay' );
                    $rsurl = __( '?roledisplay-dismissed', 'roledisplay');

        	        // display the user role notice
                    ?>
                    <div class="notice notice-success">
                    <p><?php printf(
                        esc_html__( 'Your user role is %1$s! Find out what that means %2$shere%3$s. %4$s Dismiss notice. %5$s', 'roledisplay' ),
                        $thisrole,
                        '<a href="' . $wpurl . '">',
                        '</a>',
                        '<a style="padding-left: 1em; font-weight: bold;" href="' . $rsurl . '">',
                        '</a>'
                        ); ?>
                    </p>
                    </div>
                    <?php

                }

                else {
                    return;
                }

     		}

    	}


        public function roledisplay_notice_dismissed() {

            $user_id = get_current_user_id();

            // if 'roledisplay-dismissed' is in the URL and the meta key not already set to false, GET it and set 'roledisplay_notice_dismissed' to true
            if ( isset( $_GET['roledisplay-dismissed'] ) ) {

                if (!get_user_meta( $user_id, 'roledisplay_notice_dismissed', 'false')) { 

                 add_user_meta( $user_id, 'roledisplay_notice_dismissed', 'true', true );

                }

                // else if it in the URL and had been set to true previously, make sure it's true again
                else {

                update_user_meta($user_id, 'roledisplay_notice_dismissed', 'true' );
               
                }

            }

        }
    
        /**
         * The roledisplay option showing on all user profile pages, but only visible to admins.
         *
         * @param $user WP_User user object
         */
        public function roledisplay_usermeta_form_field( $user ) {

            // if is current user's profile page(profile.php) then display an option to reset the notice
            if ( defined('IS_PROFILE_PAGE') && IS_PROFILE_PAGE ) {

            $user_id = get_current_user_id();
            $reseturl = __( '?roledisplay-reset', 'roledisplay');

            ?>
            <div><h3><?php esc_html_e( 'User Role Notice', 'roledisplay' ); ?></h3></div>

            <table class="form-table">
                <tr>
                   <th><?php esc_html_e( 'Display user role notice', 'roledisplay' ); ?></th>
                    <td>
                        <p><?php printf(
                            esc_html__( '%1$sReset%2$s', 'roledisplay' ),
                            '<a style="font-weight: bold;" href="' . $reseturl . '">',
                            '</a>'
                            ); ?>   
                        </p>
                        <span class="description"><?php esc_html_e('Displays notice on page update / refresh.'); ?></span>
                    </td>
                </tr>
            </table>
                
            <?php

            // if 'roledisplay-reset' has just been added to the URL, set the 'roledisplay_notice_dismissed' meta key to false
            if ( isset( $_GET['roledisplay-reset'] ) ) {
  
                update_user_meta( $user_id, 'roledisplay_notice_dismissed', 'false'  );

                }

            }

        }
  
        
        /**
         * Activate callback
         */
        public static function activate() {

          if ( ! current_user_can('activate_plugins')) {
			return;
			}

        }
        
        /**
         * Deactivate callback
         */
        public static function deactivate() {

           if (!current_user_can('deactivate_plugins')) {
			 return;
			}

        }



    } // end the roledisplay class

    // instantiate the plugin class
    $wp_plugin_template = new roledisplay();

} // end of 'if roledisplay class exists'