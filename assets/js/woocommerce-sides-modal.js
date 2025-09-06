let lastClickedProductId = null;

jQuery(document).on('click', '.add_to_cart_button', function () {
    lastClickedProductId = jQuery(this).data('product_id');
});

jQuery(document.body).on('added_to_cart', function () {
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

            const hasSidesCategory =
                categoriesArray.includes('sides') ||
                categoriesArray.includes('senegalese meals') ||
                categoriesArray.includes('african meals');

            if (hasSidesCategory && window.__sidesModal && typeof window.__sidesModal.open === 'function') {
                window.__sidesModal.open();
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des catégories :', error);
        }
    })();
});