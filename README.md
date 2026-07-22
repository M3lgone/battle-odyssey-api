<p align="center">
  <img src="public/images/logo/logo-battle-odissey.png" alt="Battle Odyssey Logo" width="500">
</p>

# ⚔️ Battle Odyssey API

A REST API for a turn-based RPG game where players choose a character and fight enemies across multiple battles.

## 📚 Table of Contents
- [Technologies](#technologies)
- [Features](#features)
- [Getting Started](#getting-started)
- [Automated Testing](#automated-testing)
- [API Testing](#api-testing)
- [Endpoints](#endpoints)

## 🛠 Technologies
- **Framework:** Laravel 13 (PHP 8.4+)
- **Authentication:** Laravel Passport
- **Authorization:** Custom Role Middleware
- **Database:** MySQL
- **Testing:** Pest Framework
- **Documentation:** Scribe

## ✨ Features

* **Secure Authentication:** Robust OAuth2 implementation using Laravel Passport (Bearer tokens).
* **Role-Based Access Control:** Organized routing and permissions separated by Public, Player, and Admin scopes.
* **Interactive API Documentation:** Auto-generated, up-to-date documentation using Scribe.
* **Automated Testing:** Comprehensive test suite covering endpoints and edge cases to ensure reliability.
* **Data Integrity:** Strict input validation handled through clean and reusable FormRequests.

## 🚀 Getting Started

### Prerequisites
- PHP 8.4+
- Composer
- MySQL

### Installation

1. Clone the repository

```bash
git clone https://github.com/M3lgone/battle-odyssey-api.git
cd battle-odyssey-api
```

2. Install dependencies

```bash
composer install
```

3. Set up environment

```bash
cp .env.example .env
```

4. Generate app key

```bash
php artisan key:generate
```

5. Edit the following in `.env`:

```
APP_NAME="Battle Odyssey API"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=battle_odyssey_api
DB_USERNAME=root
DB_PASSWORD=
```

6. Run migrations and seed database (This also initializes Passport)

```bash
php artisan migrate --seed
```

7. Start the server

```bash
php artisan serve
```

8. Generate API Documentation (Optional)

Scribe documentation is already included, but if you need to regenerate it, run:

```bash
php artisan scribe:generate
```

Access:
- API → http://localhost:8000/api/v1/
- Docs → http://localhost:8000/docs

## 🧪 Automated Testing

Run all tests:

```bash
php artisan test
```

Run a specific test file:

```bash
php artisan test tests/Feature/Auth/RegisterTest.php
```

## ✅ API Testing

### Scribe Documentation
Available at http://localhost:8000/docs

### Postman
Scribe generates a Postman collection automatically. In the docs menu, click **View Postman collection**, import it into Postman and use the test credentials below.

### Test Credentials

| Role   | Email                 | Password  |
|--------|-----------------------|-----------|
| Admin  | admin@example.com     | admin123  |
| Player | test@example.com      | password  |

## 📋 Endpoints

| Group      | Method | Endpoint                        | Description                    | Auth      |
|------------|--------|---------------------------------|--------------------------------|-----------|
| Auth       | POST   | `/api/v1/register`              | Register a new player          | Public    |
| Auth       | POST   | `/api/v1/login`                 | Login and receive Bearer Token | Public    |
| Auth       | POST   | `/api/v1/logout`                | Revoke current access token    | 🔑 Player |
| Profile    | GET    | `/api/v1/me`                    | View own profile               | 🔑 Player |
| Profile    | PUT    | `/api/v1/me`                    | Update own profile             | 🔑 Player |
| Profile    | DELETE | `/api/v1/me`                    | Delete own account             | 🔑 Player |
| Admin      | GET    | `/api/v1/users`                 | List all users                 | 🛡️ Admin  |
| Admin      | GET    | `/api/v1/users/{user}`          | View user details              | 🛡️ Admin  |
| Admin      | PUT    | `/api/v1/users/{user}`          | Edit any user profile          | 🛡️ Admin  |
| Admin      | DELETE | `/api/v1/users/{user}`          | Delete any user                | 🛡️ Admin  |
| Characters | GET    | `/api/v1/characters`            | List all characters            | 🔑 Player / 🛡️ Admin |
| Characters | GET    | `/api/v1/characters/{character}`| Character details with skills  | 🔑 Player / 🛡️ Admin |
| Characters | POST   | `/api/v1/characters`            | Create a character             | 🛡️ Admin  |
| Characters | PUT    | `/api/v1/characters/{character}`| Edit a character               | 🛡️ Admin  |
| Characters | DELETE | `/api/v1/characters/{character}`| Delete a character             | 🛡️ Admin  |
| Enemies    | GET    | `/api/v1/enemies`               | List all enemies               | 🛡️ Admin  |
| Enemies    | GET    | `/api/v1/enemies/{enemy}`       | Enemy details with skills      | 🛡️ Admin  |
| Enemies    | POST   | `/api/v1/enemies`               | Create an enemy                | 🛡️ Admin  |
| Enemies    | PUT    | `/api/v1/enemies/{enemy}`       | Edit an enemy                  | 🛡️ Admin  |
| Enemies    | DELETE | `/api/v1/enemies/{enemy}`       | Delete an enemy                | 🛡️ Admin  |
| Skills     | GET    | `/api/v1/skills`                | List all skills                | 🛡️ Admin  |
| Skills     | POST   | `/api/v1/skills`                | Create a skill                 | 🛡️ Admin  |
| Skills     | PUT    | `/api/v1/skills/{skill}`        | Edit a skill                   | 🛡️ Admin  |
| Skills     | DELETE | `/api/v1/skills/{skill}`        | Delete a skill                 | 🛡️ Admin  |
| Games      | GET    | `/api/v1/games`                 | Load active game               | 🔑 Player |
| Games      | POST   | `/api/v1/games`                 | Start a new game               | 🔑 Player |
| Battles    | POST   | `/api/v1/battles`               | Start a new battle             | 🔑 Player |
| Battles    | GET    | `/api/v1/battles/{battle}`      | View battle status             | 🔑 Player |
| Battles    | GET    | `/api/v1/games/{game}/battles`  | List game battle history       | 🔑 Player |

## 👤 Author
Ismael Gonzalez
