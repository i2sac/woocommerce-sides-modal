(function($) {
    'use strict';

    $(document).on('added_to_cart', function(event, fragments, cart_hash, button) {
        let product = button.closest('.product');
        let productCategories = product.attr('class').split(' ');
        
        // Utiliser les catégories définies dans l'admin
        let targetCategories = wcSidesModal.categories;
        
        let showModal = targetCategories.some(category => 
            productCategories.some(prodCat => prodCat.includes(category))
        );

        if (showModal) {
            $('#wc-sides-modal').fadeIn();
        }
    });

    // Fermeture du modal
    $('.wc-sides-modal-close').on('click', function() {
        $('#wc-sides-modal').fadeOut();
    });

    $(window).on('click', function(event) {
        if ($(event.target).hasClass('wc-sides-modal')) {
            $('#wc-sides-modal').fadeOut();
        }
    });
})(jQuery);