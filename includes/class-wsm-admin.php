<?php
if (!defined('ABSPATH'))
    exit;

class WSM_Admin
{
    private $options;
    private $page_title = 'WooCommerce Sides Modal';
    private $menu_title = 'Sides Modal';
    private $menu_slug = 'woocommerce-sides-modal';
    private $capability = 'manage_options';

    public function __construct()
    {
        $this->options = get_option(WSM_OPTIONS_KEY, array(
            'categories' => '',
            'shortcode' => '[html_block id=""]'
        ));
    }

    public function init()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_admin_menu()
    {
        add_menu_page(
            $this->page_title,
            $this->menu_title,
            $this->capability,
            $this->menu_slug,
            array($this, 'create_admin_page'),
            'dashicons-feedback',
            56
        );
    }

    public function register_settings()
    {
        register_setting(
            $this->menu_slug,
            WSM_OPTIONS_KEY,
            array($this, 'sanitize')
        );
    }

    public function create_admin_page()
    {
        if (!current_user_can($this->capability)) {
            wp_die(__('Vous n\'avez pas les permissions suffisantes pour accéder à cette page.'));
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html($this->page_title); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields($this->menu_slug);
                do_settings_sections($this->menu_slug);
                ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <label for="categories">Catégories WooCommerce</label>
                        </th>
                        <td>
                            <input type="text"
                                id="categories"
                                name="<?php echo WSM_OPTIONS_KEY; ?>[categories]"
                                value="<?php echo esc_attr($this->options['categories']); ?>"
                                class="regular-text"
                            />
                            <p class="description">
                                Entrez les slugs des catégories séparés par des virgules (ex: senegalese-meals,african-meals)
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="shortcode">Shortcode du bloc HTML</label>
                        </th>
                        <td>
                            <input type="text"
                                id="shortcode"
                                name="<?php echo WSM_OPTIONS_KEY; ?>[shortcode]"
                                value="<?php echo esc_attr($this->options['shortcode']); ?>"
                                class="regular-text"
                            />
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

    public function sanitize($input) {
        $sanitized = array();
        
        if (isset($input['categories'])) {
            $sanitized['categories'] = sanitize_text_field($input['categories']);
        }
        
        if (isset($input['shortcode'])) {
            $sanitized['shortcode'] = sanitize_text_field($input['shortcode']);
        }
        
        return $sanitized;
    }
}