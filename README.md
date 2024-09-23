# Fresh-Shop

Fresh-Shop est un projet de site e-commerce développé avec le framework Symfony, permettant la vente en ligne de produits. Ce projet a pour objectif de me perfectionner dans le développement d'applications web en Symfony.

## Fonctionnalités

- Catalogue de produits : Affichage des produits disponibles à la vente.
- Système d'authentification : Inscription, connexion, gestion des utilisateurs.
- Panier d'achat : Ajouter, retirer et mettre à jour les articles dans le panier.
- Passation de commande : Gestion des commandes et intégration d'une solution de paiement.
- Back-office : Interface d'administration pour gérer les produits, utilisateurs et commandes.

## Prérequis

- PHP 8.1+
- Composer
- MySQL ou PostgreSQL
- Symfony CLI (optionnel, mais recommandé)

## Installation

1. Clonez le dépôt :
  `git clone git@github.com:Sadam-SIBY/e-commerce.git`
2. Accédez au répertoire du projet :
    `cd e-commerce`
3. Installez les dépendances via Composer : `composer install`

4. Configuration du fichier `.env`

5. Création de la base de donnée A la racine du projet, lancer la commande suivante : `php bin/console doctrine:database:create`

6. Appliquez les migrations
`php bin/console doctrine:migrations:migrate`

7. Démarrez le serveur Symfony
`symfony serve`
