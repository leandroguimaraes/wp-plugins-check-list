<?php
/*
Plugin Name: Plugins check list
Plugin URI: 
Description: Just a check list for plugins used in every WordPress project
Version: 0.1.0
Author: Leandro Martins Guimarães
Author URI: https://profiles.wordpress.org/leandropl
License: GPL
*/

$wp_errors = new WP_Error();

if(!class_exists('WPPluginsCheckList'))
{
	class WPPluginsCheckList
	{
		public static function Run()
		{
			if (defined('DOING_AJAX') && DOING_AJAX)
			{
				// AJAX only functions
			}
			else
			{
				// regular only functions

				if (is_admin())
				{
					// admin only functions
					add_action('admin_init', 'WPPluginsCheckList::admin_init');
				}
			}
		}

		public static function admin_init()
		{
			global $wp_errors;

			WPPluginsCheckList::check_prerequisites();
			if ($wp_errors->get_error_messages() > 0)
				add_action('admin_notices', 'WPPluginsCheckList::prerequisite_notice');
		}

		public static function check_prerequisites()
		{
			global $wp_errors;

			//General purpose
			WPPluginsCheckList::check_plugin($wp_errors, 'iThemes Security', 'better-wp-security/better-wp-security.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'Contact Form 7', 'contact-form-7/wp-contact-form-7.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'Flamingo', 'flamingo/flamingo.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'Easy WP SMTP', 'easy-wp-smtp/easy-wp-smtp.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'File Renaming on upload', 'file-renaming-on-upload/file-renaming-on-upload.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'Google Analytics by MonsterInsights', 'google-analytics-for-wordpress/googleanalytics.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'Theme Check', 'theme-check/theme-check.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'TinyMCE Advanced', 'tinymce-advanced/tinymce-advanced.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'Yoast SEO', 'wordpress-seo/wp-seo.php', 'https://yoast.com/wordpress/plugins/seo/');

			//E-commerce: WooCommerce
			WPPluginsCheckList::check_plugin($wp_errors, 'WooCommerce', 'woocommerce/woocommerce.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'WC Password Strength Settings', 'wc-password-strength-settings/wc-password-strength-settings.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'WooCommerce PagSeguro', 'woocommerce-pagseguro/woocommerce-pagseguro.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'WooCommerce Correios', 'woocommerce-correios/woocommerce-correios.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'WooCommerce Extra Checkout Fields for Brazil', 'woocommerce-extra-checkout-fields-for-brazil/woocommerce-extra-checkout-fields-for-brazil.php');
			WPPluginsCheckList::check_plugin($wp_errors, 'WooCommerce Email Test', 'woocommerce-email-test/woocommerce-email-test.php');
		}

		// $errors: WP_Error object
		// $plugin_name: plugin name for user feedback
		// $plugin_path: Plugin sub-directory/file path
		// $plugin_slug: plugin slug, if this is the case
		// $external_url: plugin external download link, if this is the case
		protected static function check_plugin($errors, $plugin_name, $plugin_path, $external_url = '')
		{
			$plugin_slug = explode('/', $plugin_path);
			$plugin_slug = $plugin_slug[0];

			if (!is_file(WP_PLUGIN_DIR.'/'.$plugin_path))
			{
				$user_feedback = '';
				if ($external_url == '')
				{
					$action = 'install-plugin';
					$url = wp_nonce_url(
								add_query_arg(
									array(
										'action' => $action,
										'plugin' => $plugin_slug
									),
									admin_url('update.php')
								),
								$action.'_'.$plugin_slug
					);

					$user_feedback = '<a href="'.$url.'" target="_parent"><strong>'.$plugin_name.'</strong></a>';
				}
				else
					$user_feedback = '<a href="'.$external_url.'" target="_blank"><strong>'.$plugin_name.'</strong></a>';

				$errors->add($plugin_slug.'_missing', __(sprintf('%s plugin is not installed.', $user_feedback), 'wp_plugins_check_list'));
			}
			else if (!is_plugin_active($plugin_path))
			{
				$url = sprintf(admin_url('plugins.php?action=activate&plugin=%s&plugin_status=all&paged=1&s'), $plugin_path);
				$url = wp_nonce_url($url, 'activate-plugin_' . $plugin_path);

				$user_feedback = '<a href="'.$url.'" target="_parent"><strong>'.$plugin_name.'</strong></a>';

				$errors->add($plugin_slug.'_missing', __(sprintf('%s plugin is not activated.', $user_feedback), 'wp_plugins_check_list'));
			}
		}

		//deactivated plugin message feedback based on non met prerequisites
		public static function prerequisite_notice()
		{
			global $wp_errors;
			$errors = $wp_errors->get_error_messages();

			$msg = '';
			foreach ($errors as $error)
				$msg .= '<br />'.$error;

			echo '<div class="error"><p>'.substr($msg, 6).'</p></div>';
		}
	}

	WPPluginsCheckList::Run();
}
