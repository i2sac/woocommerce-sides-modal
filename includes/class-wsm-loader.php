<?php
if (!defined('ABSPATH')) exit;

class WSM_Loader {
    private $options;

    public function __construct() {
        $this->options = get_option('wsm_settings', array(
            'categories' => '',
            'shortcode' => '[html_block id=""]'
        ));
    }

    public function run() {
        // Admin
        if (is_admin()) {
            new WSM_Admin();
        }

        // Front
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_modal_markup'));
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'cart_fragment_modal'));
        add_action('wp_ajax_wsm_get_product_category', array($this, 'get_product_category'));
        add_action('wp_ajax_nopriv_wsm_get_product_category', array($this, 'get_product_category'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'wc-sides-modal',
            WSM_PLUGIN_URL . 'assets/css/wc-sides-modal.css',
            array(),
            WSM_VERSION
        );

        wp_enqueue_script(
            'wc-sides-modal',
            WSM_PLUGIN_URL . 'assets/js/wc-sides-modal.js',
            array('jquery'),
            WSM_VERSION,
            true
        );

        wp_localize_script('wc-sides-modal', 'wcSidesModal', array(
            'nonce' => wp_create_nonce('wsm-ajax-nonce')
        ));
    }

    public function get_product_category() {
        check_ajax_referer('wsm-ajax-nonce', 'nonce');
        
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        if (!$product_id) {
            wp_send_json_error();
            return;
        }

        // Récupération des catégories du produit
        $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'slugs'));
        
        if (is_wp_error($product_categories)) {
            wp_send_json_error();
            return;
        }

        // Récupération et nettoyage des catégories cibles
        $target_categories = array_map('trim', explode(',', $this->options['categories']));
        
        // Vérification de l'intersection
        $found = array_intersect($target_categories, $product_categories);
        
        wp_send_json_success(array(
            'show_modal' => !empty($found)
        ));
    }

    public function add_modal_markup() {
        ?>
        <div id="wc-sides-modal" class="wc-sides-modal" style="display: none;">
            <div class="wc-sides-modal-content">
                <span class="wc-sides-modal-close">&times;</span>
                <div class="wc-sides-modal-body">
                    <?php echo do_shortcode($this->options['shortcode']); ?>
                </div>
            </div>
        </div>
        <?php
    }

    public function cart_fragment_modal($fragments) {
        ob_start();
        $this->add_modal_markup();
        $fragments['div.wc-sides-modal'] = ob_get_clean();
        return $fragments;
    }
}