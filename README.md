# 🗂️ Laravel Kanban Board

A Trello-style Kanban board built with **Laravel 10**, **Filament v3**, **Livewire v3**, **Breeze**, and **Docker**.

## 🚀 Features

- Laravel 10
- Livewire v3
- Filament v3 (Admin Panel)
- Breeze (Authentication scaffolding)
- Dockerized (PHP, Nginx, MySQL)
- Ready-to-use development environment

---

## 🧰 Requirements

- [Docker](https://www.docker.com/products/docker-desktop)
- Docker Desktop with WSL2 integration (on Windows)
- Make sure ports `8000` (web) and `3306` (MySQL) are available

---

## 🛠️ Setup Instructions

### 1. Clone the repository

```bash
git clone https://github.com/your-username/laravel-kanban.git
cd laravel-kanban
```

### 2. Start Docker containers

```bash
docker-compose up -d --build
docker compose up -d --build (for Windows)
```

### 3. Install PHP and NPM dependencies inside the container

```bash
docker-compose exec app bash
docker compose exec app bash (for Windows)

composer install
npm install
```

### 4. Set up environment and application key

```bash
cp .env.prod.example .env (For better performance: production env configuration)
cp .env.example .env (For better debugging: local env configuration)

cp .env.testing.example .env.testing

php artisan key:generate
php artisan key:generate --env=testing
```

### 5. Run database migrations

```bash
php artisan migrate
```

### 6. Compile frontend assets

```bash
npm run build (If you are running with production env)
npm run dev (If you are running with local env)
```

### Enjoy the Trello(Experimental Beta Alpha Demo 0.0.1 Version :P)

```bash
http://localhost:8080
```

### (Optional) Running tests

```bash
php artisan test
```