(function($) {
    'use strict';

    // Ouverture du modal lors de l'ajout au panier
    $(document).on('added_to_cart', function(event, fragments, cart_hash, button) {
        let $product = button.closest('.product');
        let productClasses = $product.attr('class').split(' ');
        
        // Vérification des catégories configurées
        let showModal = wcSidesModal.categories.some(category => 
            productClasses.some(prodClass => prodClass.includes(category))
        );

        if (showModal) {
            $('#wc-sides-modal').fadeIn();
        }
    });

    // Fermeture du modal au clic sur la croix
    $(document).on('click', '.wc-sides-modal-close', function() {
        $('#wc-sides-modal').fadeOut();
    });

    // Fermeture du modal au clic en dehors
    $(document).on('click', function(event) {
        let $modal = $('#wc-sides-modal');
        if ($(event.target).is($modal)) {
            $modal.fadeOut();
        }
    });

    // Fermeture du modal avec la touche Echap
    $(document).on('keyup', function(event) {
        if (event.key === 'Escape') {
            $('#wc-sides-modal').fadeOut();
        }
    });

})(jQuery);