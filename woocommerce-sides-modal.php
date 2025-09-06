<?php
/**
 * Plugin Name: WooCommerce Sides Modal
 * Plugin URI: https://github.com/i2sac/woocommerce-sides-modal
 * Description: Affiche un modal personnalisé lorsqu’un produit ajouté au panier appartient à certaines catégories WooCommerce.
 * Version: 1.0.0
 * Author: Louis Isaac Diouf
 * Author URI: https://github.com/i2sac
 * License: GPL-3.0
 * Requires PHP: 7.4
 * Requires at least: 5.6
 */

if (!defined('ABSPATH'))
    exit;

define('WSM_VERSION', '1.0.0');
define('WSM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WSM_PLUGIN_URL', plugin_dir_url(__FILE__));

// Vérification de WooCommerce
function wsm_check_woocommerce()
{
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function () {
            echo '<div class="error"><p>WooCommerce Sides Modal nécessite WooCommerce pour fonctionner.</p></div>';
        });
        return false;
    }
    return true;
}

// Initialisation du plugin
function wsm_init()
{
    if (!wsm_check_woocommerce())
        return;

    require_once WSM_PLUGIN_DIR . 'includes/class-wsm-admin.php';
    require_once WSM_PLUGIN_DIR . 'includes/class-wsm-loader.php';

    $loader = new WSM_Loader();
    $loader->run();
}

add_action('plugins_loaded', 'wsm_init');