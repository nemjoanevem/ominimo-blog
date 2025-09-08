Blog App · Laravel + Vue + PostgreSQL
=====================================

A small blog app for an interview task: token‑based auth (Sanctum), Post CRUD with pagination, comments (list/add/delete), policies (owner‑or‑admin), i18n, and a minimal, clean UI (Tailwind).

---

## Tech Stack

| Area      | Technologies |
|-----------|--------------|
| Backend   | Laravel · Sanctum (personal access tokens) · PostgreSQL · Eloquent Policies |
| Frontend  | Vue 3 · TypeScript · Vue Router · Pinia · Tailwind CSS · Axios · vue‑i18n |
| Dev/Ops   | Docker (nginx + php‑fpm + postgres) |

---

## 1) Prerequisites

- **Recommended:** Docker + Docker Compose  
- **Alternative:** PHP 8.2/8.3 + Composer + Node 18+

---

## 2) Environment

Create `.env` in `backend/` (copy from `.env.example`):

```text
backend/
├─ .env.example
└─ .env        ← copy from .env.example and adjust DB, APP_URL, etc.
```

---

## 3) Run with Docker (recommended)

From the repository root:

```bash
# build + start
docker compose up -d --build

# install backend deps
docker compose exec app composer install

# generate app key
docker compose exec app php artisan key:generate

# run migrations + seed sample data
docker compose exec app php artisan migrate:fresh --seed
```

- Backend: <http://localhost:8080>  
- Admin (seed): `admin@example.com` / `password` *(dev only)*

---

## 4) Frontend (local dev)

From `frontend/`:

```bash
npm install
npm run dev
```

- Frontend dev server: <http://localhost:5173>

---

## 5) Auth (token‑based)

```
POST   /api/register           → { token, user }
POST   /api/login              → { token, user }
GET    /api/user               → requires Authorization: Bearer <token>
POST   /api/logout             → invalidates current token
```

**Example response:**

```json
{
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  "user": {
    "id": 1,
    "name": "Admin",
    "email": "admin@example.com"
  }
}
```

---

## 6) Posts & Comments (main endpoints)

```
GET    /api/posts?per_page=10&page=1      # list (includes description + body)
GET    /api/posts/{id}                    # show
POST   /api/posts                         # create (auth)
PUT    /api/posts/{id}                    # update (owner‑or‑admin)
DELETE /api/posts/{id}                    # delete (owner‑or‑admin)

GET    /api/posts/{postId}/comments       # list comments
POST   /api/posts/{postId}/comments       # add comment
DELETE /api/comments/{commentId}          # delete comment (owner‑or‑admin)
```

---

## 7) Tests

Use a dedicated test DB (`app_test`).

```bash
# 1) create test DB
docker compose exec db psql -U postgres -c "CREATE DATABASE app_test;"

# 2) run tests
docker compose exec app php artisan test
```

---

## 8) Troubleshooting

- **CSRF/419 errors:** make sure you're using token endpoints (e.g., `/api/login`) and not session auth.
- **After changing `.env`:** clear caches.

```bash
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
```

---