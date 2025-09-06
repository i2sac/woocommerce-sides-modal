<?php
/**
 * Plugin Name: WooCommerce Sides Modal
 * Plugin URI: https://github.com/i2sac/woocommerce-sides-modal
 * Description: Affiche un modal personnalisé lorsqu’un produit ajouté au panier appartient à certaines catégories.
 * Version: 1.0.0
 * Author: Louis Isaac Diouf
 * Author URI: https://github.com/i2sac
 * License: GPL-3.0
 */

defined('ABSPATH') || exit;

// 🔹 AJAX handler pour récupérer les catégories d’un produit
add_action('wp_ajax_get_product_category', 'woocommerce_sides_modal_get_product_category');
add_action('wp_ajax_nopriv_get_product_category', 'woocommerce_sides_modal_get_product_category');

function woocommerce_sides_modal_get_product_category()
{
    if (!isset($_POST['product_id'])) {
        wp_die('Missing product_id', 400);
    }

    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);

    if (!$product) {
        wp_die('Invalid product', 404);
    }

    $terms = wp_get_post_terms($product->get_id(), 'product_cat');
    $names = wp_list_pluck($terms, 'name');
    echo implode(', ', $names);
    wp_die();
}

// 🔹 Injecte le modal HTML dans le footer
add_action('wp_footer', function () {
    if (!is_product() && !is_shop() && !is_cart() && !is_checkout())
        return;

    $content = do_shortcode('[html_block id="29271"]');
    ?>
    <div id="sides-modal" class="sides-modal" aria-hidden="true" role="dialog" aria-modal="true" style="display:none;">
        <div class="sides-modal__overlay" data-sides-modal-close></div>
        <div class="sides-modal__dialog" role="document">
            <button type="button" class="sides-modal__close" aria-label="Fermer" data-sides-modal-close>&times;</button>
            <div class="sides-modal__content">
                <?php echo $content; ?>
            </div>
        </div>
    </div>
    <style>
        .sides-modal {
            position: fixed;
            inset: 0;
            z-index: 9999;
        }

        .sides-modal[aria-hidden="true"] {
            display: none;
        }

        .sides-modal__overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
        }

        .sides-modal__dialog {
            position: relative;
            max-width: 640px;
            margin: 5vh auto;
            background: #fff;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .sides-modal__close {
            position: absolute;
            top: 8px;
            right: 8px;
            background: transparent;
            border: 0;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
        }

        .sides-modal__content {
            max-height: 70vh;
            overflow: auto;
        }

        body.sides-modal-open {
            overflow: hidden;
        }
    </style>
    <script>
        (function () {
            const modal = document.getElementById('sides-modal');
            const closeEls = modal ? modal.querySelectorAll('[data-sides-modal-close]') : [];

            function openSidesModal() {
                if (!modal) return;
                modal.style.display = 'block';
                modal.setAttribute('aria-hidden', 'false');
                document.body.classList.add('sides-modal-open');
                const closeBtn = modal.querySelector('.sides-modal__close');
                if (closeBtn) closeBtn.focus();
            }

            function closeSidesModal() {
                if (!modal) return;
                modal.setAttribute('aria-hidden', 'true');
                modal.style.display = 'none';
                document.body.classList.remove('sides-modal-open');
            }

            closeEls.forEach(el => el.addEventListener('click', closeSidesModal));
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeSidesModal();
            });

            window.__sidesModal = { open: openSidesModal, close: closeSidesModal };
        })();
    </script>
    <?php
});

// 🔹 Injecte le JS principal
add_action('wp_enqueue_scripts', function () {
    if (!is_product() && !is_shop() && !is_cart() && !is_checkout())
        return;

    wp_enqueue_script(
        'woocommerce-sides-modal-js',
        plugin_dir_url(__FILE__) . 'assets/js/woocommerce-sides-modal.js',
        ['jquery'],
        null,
        true
    );
});