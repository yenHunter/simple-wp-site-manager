# Simple WP Site Manager

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)
![React](https://img.shields.io/badge/React-18-61DAFB?style=for-the-badge&logo=react)
![Tailwind CSS](https://img.shields.io/badge/Tailwind-3-38B2AC?style=for-the-badge&logo=tailwind-css)
![Docker](https://img.shields.io/badge/Docker-Manager-2496ED?style=for-the-badge&logo=docker)

**Simple WP Site Manager** is a centralized dashboard (Control Plane) built with Laravel, Inertia.js, and React. It allows you to provision, manage, and monitor WordPress installations across multiple remote VPS servers using Docker.

Instead of manually editing `docker-compose` files and SSHing into servers, you can manage everything from a modern UI.

## Features

*   **Server Management:** Connect to multiple VPS servers via SSH/SFTP.
*   **Security First:** SSH Keys, Database Passwords, and Server credentials are stored using Laravel's native encryption.
*   **One-Click Deployment:** Automatically generates `docker-compose.json` configurations, creates directory structures, and spins up WordPress + MySQL containers.
*   **Site Management:** 
    *   Custom Domain & Port mapping.
    *   Automated Database credential generation.
    *   Start/Stop/Delete sites (includes file cleanup).
*   **Live Status Monitoring:** 
    *   Installs a lightweight Bash script on the remote server.
    *   Runs via Cron every 5 minutes.
    *   Reports container health back to the dashboard via Webhook.

## 🛠 Tech Stack

*   **Backend:** Laravel 11
*   **Frontend:** Inertia.js, React, Tailwind CSS
*   **Remote Communication:** `phpseclib` (SFTP/SSH)
*   **Target Environment:** Docker & Docker Compose (running on Ubuntu/Debian/WSL)

## Project Setup Guide

Follow these steps to set up the dashboard on your local machine (Windows/Mac/Linux).

### Prerequisites
*   PHP 8.2+
*   Composer
*   Node.js & NPM
*   MySQL (or SQLite)
*   A remote server or local WSL instance with **Docker** and **Docker Compose** installed.

### 1. Clone the Repository
```bash
git clone https://github.com/yenHunter/simple-wp-site-manager.git
cd simple-wp-site-manager
```

### 2. Install Dependencies
```bash
# Backend
composer install

# Frontend
npm install
```

### 3. Environment Configuration
Copy the example environment file and configure your database connection.
```bash
cp .env.example .env
```

Open `.env` and update your DB credentials and App URL:
```ini
APP_NAME="WP Manager"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simple_wp_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Key & Migrate
```bash
php artisan key:generate
php artisan migrate
```

### 5. Run the Application
You need two terminals running simultaneously:

**Terminal 1 (Vite/Frontend):**
```bash
npm run dev
```

**Terminal 2 (Laravel/Backend):**
*If using Laragon, you can skip this. If using standard terminal:*
```bash
php artisan serve
```

## Usage Guide

### 1. Adding a Server
1.  Log in to the Dashboard.
2.  Click **"+ Add Server"**.
3.  Enter the **IP Address**, **SSH Username**, and **Password/Private Key**.
    *   *Note: Ensure the user has permission to run `docker` commands without sudo.*

### 2. Deploying a WordPress Site
1.  Click on a Server in the list.
2.  Click **"+ New WordPress Site"**.
3.  Enter a **Domain Name** (this creates a folder `~/my-sites/domain`).
4.  Assign a unique **Port** (e.g., 8081).
5.  Click **Deploy**.
    *   *The system will connect via SFTP, upload a `docker-compose.json` file, and start the containers.*

### 3. Installing the Status Monitor
To enable the 5-minute health check, you must install the monitor script on the server.

1.  Get the **Server ID** (from the URL or Database).
2.  Run the following command in your local terminal:
    ```bash
    php artisan server:monitor-install {server_id}
    ```
3.  The system will upload a bash script (`~/docker-monitor.sh`) and set up a Cron job on the remote server.

## Local Development with WSL (Troubleshooting)

If you are testing this using **WSL (Windows Subsystem for Linux)** as your "Remote Server", network connectivity requires special attention.

**Issue:** The monitoring script inside WSL cannot reach `http://simple-wp-site-manager.site` because that domain exists only in Windows `hosts` file.

**Fix:**
1.  Run the install command: `php artisan server:monitor-install {id}`.
2.  Inside WSL, edit the generated script: `nano ~/docker-monitor.sh`.
3.  Change `API_URL` to point to your **Windows LAN IP** (e.g., `192.168.x.x`).
4.  Add the Host Header to the curl command in the script:
    ```bash
    -H "Host: simple-wp-site-manager.site"
    ```

## Project Structure

*   `app/Services/RemoteService.php` - Core logic for SFTP connection, file upload, and Docker command execution.
*   `app/Http/Controllers/Api/MonitorController.php` - API endpoint that receives status updates from remote servers.
*   `resources/js/Pages/` - React views for Servers and Sites.
*   `database/migrations/` - Schema for Servers (encrypted creds) and Sites.

## 🛡 Security Note
*   **Encryption:** Sensitive data (SSH Keys, DB Passwords) is encrypted at rest using Laravel's `encrypted` model casting.
*   **Validation:** API endpoints for monitoring are protected by unique per-server tokens.

## 📄 License
The MIT License (MIT).