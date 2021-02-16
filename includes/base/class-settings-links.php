<?php if ( ! defined( 'ABSPATH' ) ) exit;

class FD_Settings_Links
{
	public function __construct() 
	{
		add_filter( "plugin_action_links_" . fdscf_plugin, array( $this, 'settings_link' ) );
	}

	public function settings_link( $links ) 
	{
		$settings_link = '<a href="admin.php?page=fd-site-settings">Settings</a>';
		array_push( $links, $settings_link );
		return $links;
	}
}

new FD_Settings_Links();