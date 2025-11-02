# 🧩 Fullstack App — Next.js + Symfony + MongoDB

## 🚀 Structure du projet

blog-management/
├── my-frontend/ → Frontend : Next.js + React + Axios
└── my-backend/ → Backend : Symfony + API REST + MongoDB + JWT

## 🖥️ Frontend (Next.js / React)

### ⚙️ Installation

```bash
cd my-frontend
yarn install
```

### 🧩 Variables d’environnement (fichier .env.local)

Créer un fichier .env.local :

```bash
NEXT_PUBLIC_API_URL=http://localhost:8000/api
```
(c’est l’URL du backend Symfony)

### ▶️ Lancer le frontend

En mode développement :

```bash
yarn dev
```

Le site sera accessible sur : 👉 http://localhost:3000

## ⚙️ Backend (Symfony + MongoDB + JWT)

### Prérequis

```bash
PHP >= 8.1

Composer

MongoDB Community Server

Extension PHP mongodb activée (php -m | grep mongodb)
```

### 📦 Installation

```bash
cd my-backend
composer install
```

### 🧩 Fichier .env

```bash
MONGODB_URL="mongodb://127.0.0.1:27017"
MONGODB_DB="blog"

JWT_SECRET=ChangeThisSecret
JWT_TTL=3600
CORS_ALLOW_ORIGIN=frontend_url
DATABASE_URL=mongodb://127.0.0.1:27017/my_database

```
⚠️ Remplace my_database par le nom souhaité pour ta base MongoDB.

### 🛠️ Lancer le serveur Symfony

```bash
symfony serve -d
```

Le backend est accessible sur :
👉 http://localhost:8000

## 📁 Détails techniques
```
-> Frontend:	Next.js 14 / React 18 / Axios

-> Backend:	Symfony 7 / Doctrine MongoDB ODM 
-> Base de données:	MongoDB
-> Auth:	JWT Token (stocké dans localStorage)
```

