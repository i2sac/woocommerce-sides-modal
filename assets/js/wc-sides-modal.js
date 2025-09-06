(function ($) {
    'use strict';

    let lastClickedProductId = null;

    // Capture le clic sur le bouton "Ajouter au panier"
    $(document).on('click', '.add_to_cart_button', function () {
        lastClickedProductId = $(this).data('product_id');
    });

    // Déclenche l'appel AJAX après ajout au panier
    $(document.body).on('added_to_cart', function () {
        if (!lastClickedProductId) return;

        (async () => {
            try {
                const response = await fetch('/wp-admin/admin-ajax.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: new URLSearchParams({
                        action: 'get_product_category',
                        product_id: lastClickedProductId
                    })
                });

                const text = await response.text();

                const categoriesArray = text
                    .split(',')
                    .map(cat => cat.trim().toLowerCase())
                    .filter(cat => cat.length > 0);

                const hasMatchingCategory = wcSidesModal.categories.some(category =>
                    categoriesArray.includes(category.toLowerCase())
                );

                if (hasMatchingCategory) {
                    $('#wc-sides-modal').fadeIn();
                }
            } catch (error) {
                console.error('Erreur lors de la récupération des catégories :', error);
            }
        })();
    });

    // Fermeture du modal au clic sur la croix
    $(document).on('click', '.wc-sides-modal-close', function () {
        $('#wc-sides-modal').fadeOut();
    });

    // Fermeture du modal au clic en dehors
    $(document).on('click', function (event) {
        let $modal = $('#wc-sides-modal');
        if ($(event.target).is($modal)) {
            $modal.fadeOut();
        }
    });

    // Fermeture du modal avec la touche Echap
    $(document).on('keyup', function (event) {
        if (event.key === 'Escape') {
            $('#wc-sides-modal').fadeOut();
        }
    });

})(jQuery);