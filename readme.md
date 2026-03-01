# Todo Backend API

A RESTful API for managing todos with authentication support, built with PHP and MySQL, containerized with Docker.

### 1. Start the Application

Navigate to the project directory and run:

```bash
docker-compose up -d
```

This command will:
- Build the PHP/Apache container
- Start MySQL database container
- Start phpMyAdmin container
- Create necessary volumes for data persistence

### 2. Import Database Schema

After containers are running, open myphpadmin and run the 'todo_app.sql' query.


## Database Structure

### Tables

**users**
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `username` (VARCHAR(32), NOT NULL)
- `password` (VARCHAR(255), NOT NULL, hashed)
- `timestamp` (TIMESTAMP)

**todos**
- `id` (INT, UNIQUE KEY, AUTO_INCREMENT)
- `text` (VARCHAR(255), NOT NULL)
- `status` (VARCHAR(16), NOT NULL)
- `user_id` (INT, for secured endpoints)
- `timestamp` (TIMESTAMP)
- `last_updated` (TIMESTAMP)

**auth_tokens**
- `user_id` (INT, PRIMARY KEY)
- `token` (VARCHAR(255), NOT NULL)
- `expires_at` (DATETIME, NOT NULL)

## 🔗 API Routes

All endpoints accept and return JSON (except error messages). CORS is configured for `http://localhost:5173`.

### Authentication Endpoints

#### Register a New User

```
POST /auth/register.php
```

**Request Body:**
```json
{
  "username": "john_doe",
  "password": "securepassword123"
}
```

---

#### Login

```
POST /auth/login.php
```

**Request Body:**
```json
{
  "username": "john_doe",
  "password": "securepassword123"
}
```

**Response (Success):**
```json
{
  "message": "Login successful",
  "token": "a1b2c3d4e5f6...",
  "expiration": "2026-03-02 12:00:00"
}
```

**Note:** Save the token for authenticated requests. Token expires after 100 hours (360000 seconds).

---

### Public Todo Endpoints

No authentication required. These endpoints manage todos globally.

#### Get All Todos

```
GET /api/getTodos.php
```

**Response:**
```json
[
  {
    "id": "1",
    "text": "Buy groceries",
    "status": "todo",
    "timestamp": "2026-03-01 10:00:00",
    "last_updated": "2026-03-01 10:00:00"
  },
  {
    "id": "2",
    "text": "Finish project",
    "status": "done",
    "timestamp": "2026-03-01 11:00:00",
    "last_updated": "2026-03-01 14:30:00"
  }
]
```

---

#### Add a Todo

```
POST /api/addTodo.php
```

**Request Body:**
```json
{
  "text": "Complete documentation"
}
```

**Note:** Status is automatically set to "todo".

---

#### Edit Todo Status

```
PATCH /api/editTodo.php
```

**Request Body:**
```json
{
  "id": 1,
  "status": "done"
}
```

---

### Secured Todo Endpoints

Require Bearer token authentication. These endpoints manage user-specific todos.

**Authentication Header Required:**
```
Authorization: Bearer YOUR_TOKEN_HERE
```

#### Get User's Todos

```
GET /api_sec/getTodos.php
```

**Headers:**
```
Authorization: Bearer a1b2c3d4e5f6...
```

**Response:**
```json
[
  {
    "id": "1",
    "text": "My personal todo",
    "status": "todo",
    "user_id": "1",
    "timestamp": "2026-03-01 10:00:00",
    "last_updated": "2026-03-01 10:00:00"
  }
]
```

---

#### Add User's Todo

```
POST /api_sec/addTodo.php
```

**Headers:**
```
Authorization: Bearer a1b2c3d4e5f6...
```

**Request Body:**
```json
{
  "text": "Complete user authentication"
}
```


---

#### Edit User's Todo

```
PATCH /api_sec/editTodo.php
```

**Headers:**
```
Authorization: Bearer a1b2c3d4e5f6...
```

**Request Body:**
```json
{
  "id": 1,
  "status": "in-progress"
}
```

**Response:**
```
Success!
```

---

## 💡 Usage Examples

### Using JavaScript (Fetch API)

**Register:**
```javascript
fetch('http://localhost:8080/auth/register.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    username: 'testuser',
    password: 'testpass123'
  })
})
.then(response => response.json())
.then(data => console.log(data));
```

**Get Todos with Authentication:**
```javascript
const token = 'YOUR_TOKEN_HERE';

fetch('http://localhost:8080/api_sec/getTodos.php', {
  headers: { 'Authorization': `Bearer ${token}` }
})
.then(response => response.json())
.then(todos => console.log(todos));
```

## 🔒 Authentication Flow

1. **Register** a new user account via `/auth/register.php`
2. **Login** with credentials via `/auth/login.php` to receive a token
3. **Store** the token securely (localStorage, sessionStorage, or secure cookie)
4. **Include** the token in the `Authorization` header for secured endpoints:
   ```
   Authorization: Bearer YOUR_TOKEN_HERE
   ```
5. **Token expires** after 100 hours; re-login to get a new token

## 📝 Notes

- All secured endpoints validate tokens against the `auth_tokens` table
- Expired tokens are automatically rejected
- Passwords are hashed using PHP's `password_hash()` with `PASSWORD_DEFAULT`
- CORS is configured for `http://localhost:5173` (adjust in PHP files if needed)
- Readme is AI generated.