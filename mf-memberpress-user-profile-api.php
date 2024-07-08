<?php

/**
* Plugin Name:       MemberPress User Profile API
* Plugin URI:        https://memberfix.rocks
* Description:       A MemberPress add-on that adds a separate API endpoint for the user profile fields. Including the custom fields.
* Version:           1.1
* Requires at least: 5.2
* Requires PHP:      7.2
* Author:            Sorin Marta @ MemberFix
* Author URI:        https://sorinmarta.com
*/

define('MF_MEPR_UPA_PATH', WP_PLUGIN_DIR . '/' . 'mf-mepr-user-profile-api/');

class MF_Mepr_User_Profile_API{
	public function __construct(){
		$this->load();
		add_action('activate_mf-mepr-user-profile-api/mf-mepr-user-profile-api.php', array($this, 'activate'));
		add_action('rest_api_init', array($this, 'register_route'));
	}

	private function load(){
		require plugin_dir_path(__FILE__) . 'includes/mf-mepr-api-endpoint.php';
	}

	public function activate(){
		if (!defined('MEPR_PATH')){
			wp_die('MemberPress needs to be active', 'MemberPress needs to be active', array(
				'back_link' => true
			));
		}

		if (!defined('MPDT_PATH')){
			wp_die('MemberPress Developer Tools needs to be active', 'MemberPress Developer Tools needs to be active', array(
				'back_link' => true
			));
		}
	}

	public function register_route(){
        register_rest_route('memberfix/v1', 'update', array(
            'methods' => 'POST',
            'callback' => array($this, 'callback'),
        ), true);
    }

	public function callback(WP_REST_Request $request){
		$endpoint = new MF_Mepr_API_Endpoint();
		$endpoint->process($request);
	}
}

new MF_Mepr_User_Profile_API();