<?php
if (!defined('ABSPATH')) exit;

class WSM_Admin {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
    }

    public function add_plugin_page() {
        add_options_page(
            'WooCommerce Sides Modal',
            'WC Sides Modal',
            'manage_options',
            'wc-sides-modal',
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page() {
        $this->options = get_option('wsm_settings', array(
            'categories' => '',
            'shortcode' => '[html_block id=""]'
        ));
        ?>
        <div class="wrap">
            <h1>WooCommerce Sides Modal</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wsm_option_group');
                do_settings_sections('wc-sides-modal');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init() {
        register_setting(
            'wsm_option_group',
            'wsm_settings',
            array($this, 'sanitize')
        );

        add_settings_section(
            'wsm_setting_section',
            'Paramètres',
            array($this, 'section_info'),
            'wc-sides-modal'
        );

        add_settings_field(
            'categories',
            'Catégories WooCommerce',
            array($this, 'categories_callback'),
            'wc-sides-modal',
            'wsm_setting_section'
        );

        add_settings_field(
            'shortcode',
            'Shortcode du bloc HTML',
            array($this, 'shortcode_callback'),
            'wc-sides-modal',
            'wsm_setting_section'
        );
    }

    public function section_info() {
        echo 'Configurez les paramètres du modal ici.';
    }

    public function sanitize($input) {
        $new_input = array();
        
        $new_input['categories'] = isset($input['categories']) 
            ? sanitize_text_field($input['categories']) 
            : '';

        $new_input['shortcode'] = isset($input['shortcode']) 
            ? sanitize_text_field($input['shortcode']) 
            : '';

        return $new_input;
    }

    public function categories_callback() {
        printf(
            '<input type="text" id="categories" name="wsm_settings[categories]" value="%s" class="regular-text" />',
            esc_attr($this->options['categories'])
        );
        echo '<p class="description">Entrez les slugs des catégories séparés par des virgules (ex: senegalese-meals,african-meals)</p>';
    }

    public function shortcode_callback() {
        printf(
            '<input type="text" id="shortcode" name="wsm_settings[shortcode]" value="%s" class="regular-text" />',
            esc_attr($this->options['shortcode'])
        );
        echo '<p class="description">Entrez le shortcode complet (ex: [html_block id="1234"])</p>';
    }
}