# Garage API (Symfony)

API REST développée avec **Symfony** pour la gestion complète d’un garage automobile : clients, véhicules, réparations, facturation, etc.

---

## Fonctionnalités

* Gestion des clients
* Gestion des véhicules
* Suivi des réparations / interventions
* Gestion de stock des pièces détachées et de rechange
* Gestion des factures
* Authentification sécurisée (JWT)
* API RESTful structurée

---

## Technologies

* PHP 8+
* Symfony 6+
* Doctrine ORM
* MySQL 
* JWT Authentication (LexikJWTAuthenticationBundle)

---

## Structure du projet

```
src/
 ├── Controller/
 ├── Entity/
 ├── EventSubscriber
 ├── Repository/
 ├── Service/
 └── Security/
```

---

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/AnjaraRasolofo/garage-api-symfony.git
cd garage-api-symfony
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Configurer l’environnement

Copier le fichier `.env` :

```bash
cp .env .env.local
```

Configurer la base de données :

```
DATABASE_URL="mysql://user:password@127.0.0.1:3306/garage_db"
```

---

## Base de données

Créer la base et exécuter les migrations :

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

---

## Authentification JWT

Générer les clés JWT :

```bash
php bin/console lexik:jwt:generate-keypair
```

Endpoint de login :

```
POST /api/login
```

---

## Endpoints principaux

### Clients

* `GET /api/customers` → Liste des clients
* `POST /api/customers` → Créer un client
* `GET /api/customers/{id}` → Détails client
* `PUT /api/customers/{id}` → Modifier
* `DELETE /api/customers/{id}` → Supprimer

---

### Véhicules

* `GET /api/vehicles`
* `POST /api/vehicles`
* `GET /api/vehicles/{id}`
* `PUT /api/vehicles/{id}`
* `DELETE /api/vehicles/{id}`

---

### Réparations

* `GET /api/repairs`
* `POST /api/repairs`
* `GET /api/repairs/{id}`
* `PUT /api/repairs/{id}`
* `DELETE /api/repairs/{id}`

---

### Factures

* `GET /api/invoices`
* `POST /api/invoices`
* `GET /api/invoices/{id}`

---

## Lancer le serveur

```bash
symfony server:start
```

Ou avec PHP :

```bash
php -S localhost:8000 -t public
```

---

## Sécurité

* Authentification via JWT
* Accès protégé par rôles (ROLE_USER, ROLE_ADMIN)
* Validation des données avec Symfony Validator

---

## Améliorations possibles

* Gestion des employés / mécaniciens
* Notifications (email / SMS)
* Historique complet des interventions
* Dashboard admin

---

## Auteur

Développé par Anjarasoa Solofondraibe - Développeur WEB fullstack https://anjara-dev.infy.uk 

---

## Licence

MIT

