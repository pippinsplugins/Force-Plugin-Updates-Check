<?php
/*
 * Plugin Name: Force Plugin Updates Check
 * Description: Adds a link to the toolbar that allows site admins to force WordPress to run a check for plugin updates
 * Author: Pippin Williamson
 * Author URI: http://pippinsplugins.com
 * Version: 1.0
 */

function pw_force_updates_check_link( $wp_admin_bar ) {

	if( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	if( ! $wp_admin_bar->get_node( 'updates' ) ) {

		// This forces the update menu to show at all times, even if there are no updates

		$update_data = wp_get_update_data();

		$title = '<span class="ab-icon"></span><span class="ab-label">' . number_format_i18n( 0 ) . '</span>';
		$title .= '<span class="screen-reader-text">' . $update_data['title'] . '</span>';

		$wp_admin_bar->add_menu( array(
			'id'    => 'updates',
			'title' => $title,
			'href'  => network_admin_url( 'update-core.php' ),
			'meta'  => array(
				'title' => $update_data['title'],
			),
		) );
	}

	$args = array(
		'parent' => 'updates',
		'id'     => 'force-plugins-update',
		'title'  => __( 'Check for Plugin Updates' ),
		'href'   => add_query_arg( 'action', 'force_plugin_updates_check', admin_url( 'index.php') )
	);
	$wp_admin_bar->add_node( $args );
}
add_action( 'admin_bar_menu', 'pw_force_updates_check_link', 999 );

function pw_trigger_force_updates_check() {

	if( ! isset( $_GET['action'] ) || 'force_plugin_updates_check' != $_GET['action'] ) {
		return;
	}

	if( ! current_user_can( 'install_plugins' ) ) {
		return;
	}

	set_site_transient( 'update_plugins', null );

	wp_safe_redirect( admin_url( 'index.php' ) ); exit;

}
add_action( 'admin_init', 'pw_trigger_force_updates_check' );