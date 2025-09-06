<?php
if (!defined('ABSPATH'))
    exit;

class WSM_Admin
{
    private $options;
    private $menu_slug = 'wc-sides-modal-settings';

    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'register_settings'));

        $this->options = get_option(WSM_OPTIONS_KEY, array(
            'categories' => '',
            'shortcode' => '[html_block id=""]'
        ));
    }

    public function add_plugin_page()
    {
        add_submenu_page(
            'woocommerce',
            'WooCommerce Sides Modal',
            'Sides Modal',
            'manage_woocommerce',
            $this->menu_slug,
            array($this, 'create_admin_page')
        );
    }

    public function register_settings()
    {
        register_setting(
            $this->menu_slug,
            WSM_OPTIONS_KEY,
            array(
                'sanitize_callback' => array($this, 'sanitize')
            )
        );
    }

    public function create_admin_page()
    {
        ?>
        <div class="wrap">
            <h1>WooCommerce Sides Modal</h1>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <?php wp_nonce_field($this->menu_slug . '_save'); ?>
                <input type="hidden" name="action" value="save_wsm_settings">

                <table class="form-table">
                    <tr>
                        <th scope="row">Catégories WooCommerce</th>
                        <td>
                            <input type="text" name="<?php echo WSM_OPTIONS_KEY; ?>[categories]"
                                value="<?php echo esc_attr($this->options['categories']); ?>" class="regular-text" />
                            <p class="description">
                                Entrez les slugs des catégories séparés par des virgules (ex: senegalese-meals,african-meals)
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Shortcode du bloc HTML</th>
                        <td>
                            <input type="text" name="<?php echo WSM_OPTIONS_KEY; ?>[shortcode]"
                                value="<?php echo esc_attr($this->options['shortcode']); ?>" class="regular-text" />
                            <p class="description">
                                Entrez le shortcode complet (ex: [html_block id="1234"])
                            </p>
                        </td>
                    </tr>
                </table>

                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function sanitize($input)
    {
        $sanitized = array();

        if (isset($input['categories'])) {
            $sanitized['categories'] = sanitize_text_field($input['categories']);
        }

        if (isset($input['shortcode'])) {
            $sanitized['shortcode'] = sanitize_text_field($input['shortcode']);
        }

        return $sanitized;
    }

    public function save_settings()
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(__('Vous n\'avez pas les permissions suffisantes pour accéder à cette page.'));
        }

        check_admin_referer($this->menu_slug . '_save');

        $settings = isset($_POST[WSM_OPTIONS_KEY]) ? $_POST[WSM_OPTIONS_KEY] : array();
        $settings = $this->sanitize($settings);

        update_option(WSM_OPTIONS_KEY, $settings);

        wp_redirect(add_query_arg(
            'settings-updated',
            'true',
            admin_url('admin.php?page=' . $this->menu_slug)
        ));
        exit;
    }

    public function init()
    {
        add_action('admin_post_save_wsm_settings', array($this, 'save_settings'));
    }
}