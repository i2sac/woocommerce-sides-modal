<?php
if (!defined('ABSPATH'))
    exit;

class WSM_Admin
{
    private $options;

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'init_settings'));
    }

    public function add_plugin_page()
    {
        // Ajout de la page dans le menu principal de WordPress
        add_menu_page(
            'WooCommerce Sides Modal', // Titre de la page
            'Sides Modal',             // Titre du menu
            'manage_options',          // Capacité requise
            'wc-sides-modal',         // Slug du menu
            array($this, 'create_admin_page'), // Fonction de callback
            'dashicons-cart',         // Icône
            56                        // Position
        );
    }

    public function init_settings()
    {
        register_setting(
            'wc_sides_modal_options', // Option group
            WSM_OPTIONS_KEY,          // Option name
            array($this, 'sanitize')  // Sanitize callback
        );

        add_settings_section(
            'wc_sides_modal_section', // ID
            'Paramètres',            // Title
            null,                    // Callback
            'wc-sides-modal'         // Page
        );

        add_settings_field(
            'categories',             // ID
            'Catégories WooCommerce', // Title
            array($this, 'categories_callback'), // Callback
            'wc-sides-modal',        // Page
            'wc_sides_modal_section' // Section
        );

        add_settings_field(
            'shortcode',              // ID
            'Shortcode du bloc HTML', // Title
            array($this, 'shortcode_callback'), // Callback
            'wc-sides-modal',        // Page
            'wc_sides_modal_section' // Section
        );

        $this->options = get_option(WSM_OPTIONS_KEY, array(
            'categories' => '',
            'shortcode' => '[html_block id=""]'
        ));
    }

    public function create_admin_page()
    {
        // Vérification des droits d'accès
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions suffisantes pour accéder à cette page.'));
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wc_sides_modal_options');
                do_settings_sections('wc-sides-modal');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function sanitize($input)
    {
        $sanitized_input = array();

        if (isset($input['categories'])) {
            $sanitized_input['categories'] = sanitize_text_field($input['categories']);
        }

        if (isset($input['shortcode'])) {
            $sanitized_input['shortcode'] = sanitize_text_field($input['shortcode']);
        }

        return $sanitized_input;
    }

    public function categories_callback()
    {
        printf(
            '<input type="text" id="categories" name="%s[categories]" value="%s" class="regular-text">',
            WSM_OPTIONS_KEY,
            esc_attr($this->options['categories'])
        );
        echo '<p class="description">Entrez les slugs des catégories séparés par des virgules (ex: senegalese-meals,african-meals)</p>';
    }

    public function shortcode_callback()
    {
        printf(
            '<input type="text" id="shortcode" name="%s[shortcode]" value="%s" class="regular-text">',
            WSM_OPTIONS_KEY,
            esc_attr($this->options['shortcode'])
        );
        echo '<p class="description">Entrez le shortcode complet (ex: [html_block id="1234"])</p>';
    }
}