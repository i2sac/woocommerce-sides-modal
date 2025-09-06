<?php
if (!defined('ABSPATH'))
    exit;

class WSM_Admin
{
    private $options;
    private $page = 'wc-sides-modal';
    private $option_group = 'wc_sides_modal_options';

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        $this->options = get_option(WSM_OPTIONS_KEY);
    }

    public function add_plugin_page()
    {
        add_options_page(
            'WooCommerce Sides Modal',
            'Sides Modal',
            'manage_options',
            $this->page,
            array($this, 'create_admin_page')
        );
    }

    public function create_admin_page()
    {
        ?>
        <div class="wrap">
            <h1>WooCommerce Sides Modal</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields($this->option_group);
                do_settings_sections($this->page);
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function page_init()
    {
        register_setting(
            $this->option_group,
            WSM_OPTIONS_KEY,
            array($this, 'sanitize')
        );

        add_settings_section(
            'wsm_setting_section',
            'Paramètres',
            null,
            $this->page
        );

        add_settings_field(
            'categories',
            'Catégories WooCommerce',
            array($this, 'categories_callback'),
            $this->page,
            'wsm_setting_section'
        );

        add_settings_field(
            'shortcode',
            'Shortcode du bloc HTML',
            array($this, 'shortcode_callback'),
            $this->page,
            'wsm_setting_section'
        );
    }

    public function sanitize($input)
    {
        $new_input = array();

        if (isset($input['categories'])) {
            $new_input['categories'] = sanitize_text_field($input['categories']);
        }

        if (isset($input['shortcode'])) {
            $new_input['shortcode'] = sanitize_text_field($input['shortcode']);
        }

        return $new_input;
    }

    public function categories_callback()
    {
        printf(
            '<input type="text" id="categories" name="%s[categories]" value="%s" class="regular-text" />',
            WSM_OPTIONS_KEY,
            esc_attr($this->options['categories'])
        );
        echo '<p class="description">Entrez les slugs des catégories séparés par des virgules (ex: senegalese-meals,african-meals)</p>';
    }

    public function shortcode_callback()
    {
        printf(
            '<input type="text" id="shortcode" name="%s[shortcode]" value="%s" class="regular-text" />',
            WSM_OPTIONS_KEY,
            esc_attr($this->options['shortcode'])
        );
        echo '<p class="description">Entrez le shortcode complet (ex: [html_block id="1234"])</p>';
    }
}