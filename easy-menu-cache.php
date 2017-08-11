<?php
/*
Plugin Name: Easy Menu Cache
Plugin URI: http://www.marcocanestrari.it
Description: Caches all navigation menus in a single wp_option
Author: Marco Canestrari
Version: 1.1.0
Author URI: http://www.marcocanestrari.it
License: GPL2


Copyright (c) 2016 Marco Canestrari


This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License version 2 as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
license.txt file included with this plugin for more information.


*/


class Easy_Menu_Cache {


	/**
	 * Class constructor.
	 */
	public function __construct() {

		// Load hooks and filters
		add_filter( 'wp_nav_menu', array( $this, 'set_cached_menu' ), 10, 2 );
		add_filter( 'pre_wp_nav_menu', array( $this, 'get_cached_menu' ), 10, 2 );
		add_action( 'wp_update_nav_menu', array( $this, 'refresh_cache' ) ); // Inizialize chache on menu update
		register_activation_hook(__FILE__, array( $this, 'refresh_cache' )); // Inizialize cache on activation
		register_deactivation_hook(__FILE__, array( $this, 'refresh_cache' )); // Inizialize cache on deactivation

	}

	/**
	 * Refresh the cache.
	 * Deletes the option where cached menus are saved
	 */
	public function refresh_cache() {

		delete_option('wp_menu_cache_cached_menus');
	}

	/**
	 * Set the menu cache.
	 *
	 * @param  string   $nav_menu   The nav menu content
	 * @param  array    $args       The menu arguments
	 * @return string   The cached menu
	 */
	public function set_cached_menu( $nav_menu, $args ) {

		$cached_menus = get_option('wp_menu_cache_cached_menus');
		if(false === $cached_menus) {
			$cached_menus = array();
			$cached_menus[$this->get_menu_cache_id($args)] = $nav_menu;
			update_option('wp_menu_cache_cached_menus',$cached_menus);

		} else {
			$cached_menus[$this->get_menu_cache_id($args)] = $nav_menu;
			update_option('wp_menu_cache_cached_menus',$cached_menus);
		}

		return $nav_menu;
	}

	/**
	 * Get the cached menu.
	 *
	 * @param  bool    $dep   Deprecated variable
	 * @param  array   $args  The menu arguments
	 * @return string  The cached menu
	 */
	public function get_cached_menu( $dep = null, $args ) {

		$cached_menus = get_option('wp_menu_cache_cached_menus');

		// Return the cached if exists
		if ( false === $cached_menus || !$cached_menus[$this->get_menu_cache_id($args)]) {
			return null;
		} else {
			return $cached_menus[$this->get_menu_cache_id($args)];
		}

	}

	/**
	 * Get the menu cache id: if in a page, the menu cache id contains the page id
	 *
	 * @param	Object	$args	Menu args
	 * @return	string	The menu cache id
	 *
	 */
	private function get_menu_cache_id($args) {

		if(is_page()) {

				return $args->menu->term_id.'-'.$args->theme_location.'-'.get_the_ID();

		} else {

				return $args->menu->term_id.'-'.$args->theme_location;
		}

	}


}
new Easy_Menu_Cache();
