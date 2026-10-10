# Websource — thème WordPress gratuit

Thème WordPress libre pour boutique WooCommerce et blog, édité par [Websource](https://www.websource.fr).
C'est l'édition publiée sur le répertoire WordPress.org.

- Une feuille de style de base, plus une feuille par type de page (accueil, boutique, fiche produit, panier, commande, compte, article, page, recherche, 404).
- Aucune ressource distante, aucun suivi.
- Fonctionne avec les blocs Panier et Commande de WooCommerce comme avec les codes courts.
- 3 styles de blocs, 2 compositions, 18 zones de widgets, 4 menus, traduction française.

Présentation, téléchargement et comparaison avec la version Premium : <https://www.websource.fr/theme-wordpress-gratuit>

## Installation

Téléchargez l'archive `.zip` depuis la page ci-dessus (ou une version de ce dépôt), puis dans WordPress :
Apparence > Thèmes > Ajouter un thème > Téléverser un thème.

## Développement

Le CSS livré dans `assets/css/` est compilé depuis `assets/src/` avec Tailwind CSS v4 :

```
npm install
npm run build
```

## Licence

GPL version 2 ou ultérieure. Voir `readme.txt` pour le détail des ressources tierces (Tailwind CSS, licence MIT).
