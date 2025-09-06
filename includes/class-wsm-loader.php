<?php
if (!defined('ABSPATH')) exit;

class WSM_Loader {
    private $options;

    public function __construct() {
        $this->options = get_option(WSM_OPTIONS_KEY, array(
            'categories' => '',
            'shortcode' => '[html_block id=""]'
        ));
    }

    public function run() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_modal_markup'));
        add_action('wp_ajax_get_product_category', array($this, 'get_product_category'));
        add_action('wp_ajax_nopriv_get_product_category', array($this, 'get_product_category'));
        add_action('wp_ajax_wsm_check_categories', array($this, 'check_categories'));
        add_action('wp_ajax_nopriv_wsm_check_categories', array($this, 'check_categories'));
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

        $categories = !empty($this->options['categories']) 
            ? array_map('trim', explode(',', $this->options['categories'])) 
            : array();
            
        wp_localize_script('wc-sides-modal', 'wcSidesModal', array(
            'categories' => array_map('strtolower', $categories),
            'ajaxurl' => admin_url('admin-ajax.php')
        ));
    }

    public function get_product_category() {
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        
        if (!$product_id) {
            wp_send_json_error();
            return;
        }

        $categories = array();
        $terms = get_the_terms($product_id, 'product_cat');
        
        if ($terms && !is_wp_error($terms)) {
            $categories = wp_list_pluck($terms, 'slug');
        }
        
        echo implode(',', $categories);
        wp_die();
    }

    public function add_modal_markup() {
        ?>
        <div id="wc-sides-modal" class="wc-sides-modal" style="display: none;">
            <div class="wc-sides-modal-content">
                <div class="wc-sides-modal-header">
                    <h2 class="wc-sides-modal-title">Add Sides</h2>
                    <span class="wc-sides-modal-close">&times;</span>
                </div>
                <div class="wc-sides-modal-body">
                    <?php echo do_shortcode($this->options['shortcode']); ?>
                </div>
            </div>
        </div>
        <?php
    }
}