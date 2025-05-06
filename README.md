# 🚗 AutoService - Plateforme de vente et services automobiles

## 📌 Présentation

AutoService est une application web développée avec Symfony qui permet :
- La **vente de voitures** neuves et d'occasion.
- La **vente d’équipements** automobiles.
- La **réservation de mécaniciens** pour les réparations.
- La **réservation de services de remorquage**.
- La **réservation de voitures à l'essai** avant achat.

Ce projet vise à simplifier l’expérience client en centralisant plusieurs services automobiles sur une seule plateforme.

---

## 🚀 Fonctionnalités

- 👨‍🔧 Réservation de mécanicien en ligne.
- 🛠️ Vente d’équipements avec gestion de stock.
- 🚘 Consultation, recherche et réservation de véhicules.
- 🧪 Réservation d’essai de véhicule.
- 🚛 Demande de remorquage.
- 👨‍💼 Espace administrateur pour la gestion complète (voitures, équipements, réservations).
- 🔐 Authentification et rôles (admin/client).
- 📱 Compatible mobile.

---

## 🧰 Technologies utilisées

- PHP 8.x / Symfony 6.x
- MySQL
- Twig
- Doctrine ORM
- Bootstrap / Tailwind CSS (si utilisé)
- Webpack Encore (si utilisé)
- API REST (si applicable)
- Docker (si utilisé pour l’environnement)
- Stripe (si paiement en ligne est intégré)

---

## ⚙️ Prérequis

- PHP >= 8.1
- Composer
- MySQL ou MariaDB
- Node.js & npm (si tu utilises Webpack Encore)
- Symfony CLI (optionnel mais recommandé)
- Docker (si utilisé)

---

## 🛠️ Installation locale

```bash
# 1. Cloner le projet
git clone https://github.com/ton-utilisateur/auto-service.git
cd auto-service

# 2. Installer les dépendances PHP
composer install

# 3. Copier et configurer les variables d’environnement
cp .env .env.local
# Modifier DB_URL, MAILER_DSN, STRIPE_SECRET_KEY, etc. dans .env.local

# 4. Créer la base de données et exécuter les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 5. (Optionnel) Charger des données fictives
php bin/console doctrine:fixtures:load

# 6. Installer les dépendances front (si applicable)
npm install
npm run dev

# 7. Démarrer le serveur
symfony server:start
# ou
php -S localhost:8000 -t public



