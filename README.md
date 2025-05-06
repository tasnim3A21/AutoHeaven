# 🚗 AutoService - Plateforme de vente et services automobiles

## 📌 Présentation

AutoService est une application web développée avec Symfony qui permet :
- La **vente de voitures** neuves .
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
- 📝 Ajout de réclamations par les utilisateurs.
- 🌟 Ajout d’avis et de notes pour les voitures.

---

## 🧰 Technologies utilisées

- PHP 8.1.25 / Symfony 6.4
- MySQL
- Twig
- Doctrine ORM
- Bootstrap / Tailwind CSS
- API REST
- Stripe 
---

## ⚙️ Prérequis

- PHP >= 8.1
- Composer
- MySQL
- Symfony CLI 


---

## 🛠️ Installation locale

```bash
# 1. Cloner le projet
git clone https://github.com/tasnim3A21/AutoHeaven.git
cd AutoHeaven.git

# 2. Ajouter les dépendances nécessaires au projet
composer require pusher/pusher-php-server 
composer require knplabs/knp-paginator-bundle
composer require stripe/stripe-php
composer require twilio/sdk    
composer require beberlei/doctrineextensions
composer require symfony/http-client
composer require dompdf/dompdf
composer require php-ai/php-ml 
composer require symfony/mercure-bundle
composer require openspout/openspout
composer require hwi/oauth-bundle 

# 3. Ensuite, installer toutes les dépendances
composer install


# 4. Copier et configurer les variables d’environnement
cp .env .env.local
# Modifier DB_URL, MAILER_DSN, STRIPE_SECRET_KEY,GEMINI_API_KEY,GOOGLE_RECAPTCHA_SECRET_KEY etc. dans .env.local

# 5. Créer la base de données et exécuter les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate


# 6. Démarrer le serveur
symfony server:start
# ou
php -S localhost:8000 -t public
```


---

## 📜 Licence

Ce projet est sous licence **MIT**. Pour plus de détails, consultez le fichier [LICENSE](LICENSE).

![Licence MIT](images/licence_mit.png)

---

# 🚗 AutoService - Plateforme de vente et services automobiles  
![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)


