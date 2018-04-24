<?php
class Social_Warfare_AffiliateWP extends SWP_Addon {
    public function __construct() {
        parent::__construct();
        $this->name = 'Social Warfare - AffiliateWP';
        $this->key = 'affiliatewp';
        $this->product_id = 114264;
        $this->version = '1.1.0';
        $this->core_required = '3.0.0';

        if ( $this->is_registered() ) {
            if ( version_compare($this->core_version, $this->core_required) >= 0) {
                add_filter( 'swp_network_buttons', [$this, 'append_affiliate_id_to_links'], 1, 1 );
            } else {
               throw( "Please make sure you are using the most recent version of Social Warfare. We require at least version " . $this->core_required . "." );
           }
        }
    }

    /**
     * Adds affiliate links to the share buttons.
     *
     * @since  1.0.0
     * @access public
     * @param  Array $buttons An array of information about the buttons being generated
     * @return Array $buttons The modified array of information about the buttons being generated
     *
     */
    public function append_affiliate_id_to_links( $buttons ) {

        // Make sure core is on a version that contains our dependancies
        if (defined('SWP_VERSION') && version_compare(SWP_VERSION, SWAW_CORE_VERSION_REQUIRED) >= 0) {

            // Check if the AffiliateWP plugin is installed
            if ( function_exists('affwp_is_affiliate') ) {

                // Check if the current user is logged in and is an affiliate
                if ( ! ( is_user_logged_in() && affwp_is_affiliate() ) ) {
                    return $buttons;
                }

                // Append referral parameter and affiliate ID to sharing links in Social Warfare
                $buttons['url'] = add_query_arg( affiliate_wp()->tracking->get_referral_var(), affwp_get_affiliate_id(), $buttons['url'] );
            }
        }

        // Return the modified array
        return $buttons;
    }
}