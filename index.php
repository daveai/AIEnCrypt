<?php
/*
Plugin Name: AIEnCrypt
Plugin URI:  https://developer.wordpress.org/plugins/the-basics/
Description: An Encryption Engine for WP with AWS S3
Version:     1.0
Author:      WordPress.org
Author URI:  https://developer.wordpress.org/
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: wporg
Domain Path: /languages
*/
//enqueues our external font awesome stylesheet
function enqueue_our_required_stylesheets(){
	wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'); 
}
add_action('wp_enqueue_scripts','enqueue_our_required_stylesheets');
function wporg_options_page()
{
    add_menu_page(
        'AIEnCrypt',
        'AIEnCrypt Options',
        'manage_options',
        'wporg',
        'aiencrypt_options_page_html',
        'data:image/svg+xml;base64,' . base64_encode('<?xml version="1.0" encoding="utf-8"?>
<svg width="1792" height="1792" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path d="M640 768h512v-192q0-106-75-181t-181-75-181 75-75 181v192zm832 96v576q0 40-28 68t-68 28h-960q-40 0-68-28t-28-68v-576q0-40 28-68t68-28h32v-192q0-184 132-316t316-132 316 132 132 316v192h32q40 0 68 28t28 68z" fill="#fff"/></svg>'),
        20
    );
}
function aiencrypt_options_page_html()
{
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    require('vendor/autoload.php');
    require('inc/ServerUpload.php');
    require('inc/S3Upload.php');
    require('views/index.php');
    $serverUpload = new ServerUpload();
    $serverUpload->boot();
}
add_action('admin_menu', 'wporg_options_page');