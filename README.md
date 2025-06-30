
# 🔐 Multi-System Login: Foodpanda Part

This project demonstrates a **secure, seamless multi system login** system between two independent Laravel applications:
- **Ecommerce App**
- **Foodpanda App**

Users can log into **ecommerce**, and be **automatically authenticated** into **foodpanda** — without re-entering their credentials.

---

## 📌 Features

- ✅ Laravel 11 projects with separate user tables
- ✅ Breeze authentication (Customized UI)
- ✅ Token-based SSO using Laravel Sanctum
- ✅ Auto-login via cookies across apps
- ✅ Auto-logout from both apps
- ✅ Session protection & regeneration
- ✅ Reusable SSO middleware

---

## 📁 Demo Project


Both apps are fully independent and hosted on different ports/domains:
- **Demo Ecommerce App:** [http://ecommerce.irfandev.xyz/login](http://ecommerce.irfandev.xyz/login)
<br>
Credentials : -

    ```php
    email: admin@gmail.com
    password: admin@gmail.com
    ```


- **Demo Foodpanda App:** [http://foodpanda.irfandev.xyz/login](http://foodpanda.irfandev.xyz/login)
<br>
Credentials :
    ```php
    email: admin@gmail.com
    password: admin@gmail.com
    ```

---

## 🔐 How Multiple Login Works (Flow Diagram)

```text
[1] User logs into Ecommerce App
     ↓
[2] Ecommerce creates Sanctum token
     ↓
[3] Ecommerce sends token & email via HTTP to Foodpanda
     ↓
[4] Foodpanda stores hashed token in DB
     ↓
[5] Ecommerce sets browser cookies: sso_token, sso_email
     ↓
[6] Later, user visits Foodpanda → middleware checks cookies & token
     ↓
[7] If valid → Auth::login() in foodpanda → access granted
     ↓
[8] Logout functionality that signs the user out of both apps.
```

---


## ⚙️ Technologies Used

* Laravel 11
* Laravel Sanctum
* Laravel Breeze
* HTTP Client (`Http::post()`)
* Cookie-based session sharing
* CSRF, hashing, middleware, session

---

### 📥 Installation Steps

- Clone the repository

```bash
git clone git@github.com:Irfan-Chowdhury/steadfast-ecommerce.git
```

- Install PHP dependencies

```bash
composer install
```

- Copy .env file and configure DB

```bash
cp .env.example .env
```
Edit DB credentials in .env file


- Generate key & run migrations

```bash
php artisan key:generate
php artisan migrate --seed
```

- Install Node dependencies (optional for UI)

```bash
npm install && npm run build
```

- Start the server

```bash
php artisan serve
```

## 🧪 Testing Instructions

1. Visit: [http://127.0.0.1:8000/login](http://127.0.0.1:8000/login) and log into ecommerce
2. Go to ecommerce dashboard → you will now be authenticated
3. Open [http://127.0.0.1:8001/login](http://127.0.0.1:8001/login) directly → you're auto-logged in
4. Logout from ecommerce → foodpanda also logs out
5. Try visiting foodpanda dashboard again → you'll be redirected to login


## Login Process

### Ecommerce App:

* User logs in
* Token is generated (`$user->createToken(...)`)
* Token + email is sent to Foodpanda via API (`/api/cross-login`)
* Cookies are set (`sso_token`, `sso_email`)
* User lands on ecommerce dashboard

### Foodpanda App:

* Middleware checks if cookies exist
* If cookies are valid, and token matches DB, `Auth::login($user)` is triggered
* User is logged in **without form**

---

## Logout Process

<!-- When the user logs out from **ecommerce** or **foodpanda**: -->
When the user logs out from **ecommerce**:

1. The SSO token is deleted from both ecommerce & foodpanda databases
2. Cookies (`sso_token`, `sso_email`) are removed
3. Session is invalidated
4. Optionally, foodpanda's session is destroyed via cookie, SSO middleware

---

## 🧠 Middleware (SSO Logic)

Foodpanda uses a middleware `CheckSSOCookie` that:

* Logs user **in** if valid cookie + token found
* Logs user **out** if cookie is missing but user is logged in

```php
if (!cookie || !token) {
    Auth::logout();
} elseif (cookie exists && !Auth::check()) {
    // hash token, match in DB → login
}
```

---

## 🛡️ Security Considerations

* Tokens are hashed with SHA-256 and never stored in plain form
* CSRF tokens are applied to all forms
* Session regeneration is handled after login/logout
* Token expiration and rotation can be added for extra protection

---



## 👨‍💻 Author

**Md Irfan Chowdhury** <br>
PHP-Laravel Developer  <br>
🔗 [LinkedIn Profile](https://www.linkedin.com/in/irfan-chowdhury/) | 📧 [irfanchowdhury80@gmail.com](irfanchowdhury80@gmail.com)

---
