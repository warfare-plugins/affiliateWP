<?php
/**
 * Plugin Name: Social Warfare - AffiliateWP
 * Plugin URI:  http://warfareplugins.com
 * Description: A plugin to that transforms all shared links on the Social Warfare buttons across your site into affiliate links for logged in affiliates.
 * Version:     2.0.0
 * Author:      Warfare Plugins
 * Author URI:  http://warfareplugins.com
 * Text Domain: social-warfare
 *
 */


/**
 * Define the constants that are used throughout the plugin to include files,
 * check for updates, check the registration status, etc.
 *
 */
defined( 'WPINC' ) || die;
define( 'SWAW_CORE_VERSION_REQUIRED', '3.0.0' );
define( 'SWAW_VERSION', '2.0.0' );
define( 'SWAW_PLUGIN_FILE', __FILE__ );
define( 'SWAW_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SWAW_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'SWAW_SL_PRODUCT_ID', 191026 );  // AffiliateWP Utility Product id.


/**
 * Enqueue the entire plugin to run deferred to the plugins_loaded hook to
 * ensure that Social Warfare Core has already loaded up and become available.
 *
 */
add_action('plugins_loaded' , 'initialize_social_warfare_affiliatewp' , 20 );


/**
 * The function that brings the entire plugin to life.
 *
 * @since  1.0.0 | 01 JAN 2018 | Created
 * @param  void
 * @return void
 */
function initialize_social_warfare_affiliatewp() {


	/**
	 * Make sure that Social Warfare is installed by checking for it's version
	 * constant. If it doesn't exist, we'll queue up a dashboard notification
	 * to let the user know that they need to install it.
	 *
	 */
    if ( !defined( 'SWP_VERSION' ) ) :
        add_action( 'admin_notices', 'swp_needs_core' );
        return;
    endif;


	/**
	 * We need to be sure that Social Warfare is running a version that is
	 * compatibile with this addon before proceeding.
	 *
	 */
	if( version_compare( SWP_VERSION, SWAW_CORE_VERSION_REQUIRED ) >= 0 ) {


		/**
		 * If the Social_Warfare_Addon class hasn't already been loaded, we need
		 * to load it up so that we can extend it with this addon.
		 *
		 */
		if ( !class_exists( 'Social_Warfare_Addon' ) && defined( 'SWP_PLUGIN_DIR' ) ) {
		    require_once( SWP_PLUGIN_DIR . '/lib/Social_Warfare_Addon.php' );
		}


		/**
		 * If the Social_Warfare_Addon class still doesn't exist after the
		 * attempt to load it above, we need to bail out.
		 *
		 */
		if( !class_exists( 'Social_Warfare_Addon' ) ) {
			return;
		}

		require_once SWAW_PLUGIN_DIR . '/Social_Warfare_AffiliateWP.php';
        $addon = new Social_Warfare_AffiliateWP();
        // add_filter( 'swp_registrations', array( $addon, 'add_self' ) );
    } else {
        add_filter( 'swp_admin_notices', 'swp_affiliatewp_update_notification' );
	}

    if ( !class_exists( 'SWP_Plugin_Updater' ) && defined( 'SWP_PLUGIN_DIR' ) ) {
        require_once( SWP_PLUGIN_DIR . '/lib/utilities/SWP_Plugin_Updater.php' );
    }

    if ( class_exists( 'SWP_Plugin_Updater' ) ) :

        //* Everybody gets Pro updates, whether or not their license is active or valid.
        $edd_updater = new SWP_Plugin_Updater( SWP_STORE_URL, __FILE__, array(
        	'version' 	=> SWAW_VERSION,		// Current version number.
        	'license' 	=> '9a5dae1ef9c7e12a50cb52d80553daec',	// Update check key.
            'item_id'   => SWAW_SL_PRODUCT_ID,
        	'author' 	=> 'Warfare Plugins',	// Author of this plugin.
        	'url'           => home_url(),
            'beta'          => false // Set to true if you wish customers to receive update notifications of beta releases
        ) );
    endif;

}

if ( !function_exists( 'swp_needs_core' ) ) :
    function swp_needs_core() {
        ?>
        <div class="update-nag notice is-dismissable">
            <p><b>Important:</b> You currently have Social Warfare - Pro installed without our Core plugin installed.<br/>Please download the free core version of our plugin from the WordPress repo or from our <a href="https://warfareplugins.com" target="_blank">website</a>.</p>
        </div>
        <?php
    }
endif;


/**
 * Notify users that the versions of Social Warfare and SW AffiliateWP are mismatched.
 *
 *
 * @since  2.1.0
 * @param  none
 * @return void
 *
 */
 function swp_affiliatewp_update_notification( $notices = array() ) {
     if (is_string( $notices ) ) {
         $notices = array();
     }

     $notices[] = array(
         'key'   => 'update_notice_affiliatewp_' . SWAW_VERSION, // database key unique to this version.
         'message'   => 'Looks like your copy of Social Warfare - Pro isn\'t up to date with Core. While you can still use both of these plugins, we highly recommend you keep both Core and Pro up-to-date for the best of what we have to offer.',
         'ctas'  => array(
             array(
                 'action'    => 'Remind me in a week.',
                 'timeframe' => 7 // dismiss for one week.
             ),
             array(
                 'action'    => 'Thanks for letting me know.',
                 'timeframe' => 0 // permadismiss for this version.
             )
         )
     );

     return $notices;
}
