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
        if (is_admin()) {
            new WSM_Admin();
        }

        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_modal_markup'));
        add_filter('woocommerce_add_to_cart_fragments', array($this, 'cart_fragment_modal'));
        add_action('wp_ajax_wsm_check_product_categories', array($this, 'check_product_categories'));
        add_action('wp_ajax_nopriv_wsm_check_product_categories', array($this, 'check_product_categories'));
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

        $categories = array_map('trim', explode(',', $this->options['categories']));

        wp_localize_script('wc-sides-modal', 'wcSidesModal', array(
            'categories' => $categories,
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wsm-ajax-nonce')
        ));
    }

    public function check_product_categories() {
        check_ajax_referer('wsm-ajax-nonce', 'nonce');
        
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        if (!$product_id) {
            wp_send_json_error();
            return;
        }

        $product = wc_get_product($product_id);
        if (!$product) {
            wp_send_json_error();
            return;
        }

        $target_categories = array_map('trim', explode(',', $this->options['categories']));
        $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'slugs'));
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