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
```

### 3. Install PHP dependencies inside the container

```bash
docker compose exec app bash
composer install
```

### 4. Set up environment and application key

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Run database migrations

```bash
php artisan migrate
```

### (Optional) Compile frontend assets

```bash
npm install
npm run build
```