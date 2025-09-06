# WooCommerce Sides Modal

Affiche un modal personnalisé lorsqu’un produit ajouté au panier appartient à certaines catégories WooCommerce.

## 🔗 Plugin URI
[https://github.com/i2sac/woocommerce-sides-modal](https://github.com/i2sac/woocommerce-sides-modal)

## 🧑‍💻 Auteur
**Louis Isaac Diouf**  
GitHub: [https://github.com/i2sac](https://github.com/i2sac)

## 📦 Fonctionnalités

- Détecte l’ajout d’un produit au panier via AJAX
- Vérifie si le produit appartient à l’une des catégories suivantes :
  - `sides`
  - `senegalese meals`
  - `african meals`
- Affiche un modal contenant le bloc `[html_block id="29271"]`
- Modal responsive, accessible, et personnalisable

## 📁 Installation

1. Téléchargez ou clonez le dépôt dans `wp-content/plugins/woocommerce-sides-modal`
2. Activez le plugin via l’interface WordPress
3. Assurez-vous que le bloc HTML `[html_block id="29271"]` existe

## 🛠️ Personnalisation

- Pour modifier les catégories ciblées, éditez le tableau dans `woocommerce-sides-modal.js`
- Pour changer le contenu du modal, modifiez le shortcode dans `woocommerce-sides-modal.php`

## 📜 Licence

Ce plugin est distribué sous la licence **GPL 3.0**.  
Voir [LICENSE](https://www.gnu.org/licenses/gpl-3.0.html) pour plus d’informations.