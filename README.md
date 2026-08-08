# 🛒 QuickCart

**QuickCart** is an e-commerce platform where users can register, log in, browse products, add products to their cart, and communicate with the admin through real-time chat support.

The platform uses **PHP and MySQL** for the backend and database, while **Node.js and Socket.IO** are used for real-time communication.

## ✨ Features

* 👤 User registration and login
* 🔐 Forgot password functionality
* 📧 Password reset link sent through email using **PHPMailer**
* 🛍️ Browse products
* 🛒 Add and manage products in cart
* 💬 Real-time chat support with admin
* ⚡ Real-time communication using **Socket.IO**
* 🗄️ MySQL database for storing user and product data

## 🛠️ Technologies Used

* HTML
* CSS
* JavaScript
* PHP
* MySQL
* XAMPP
* Node.js
* Socket.IO
* PHPMailer

## ⚙️ How to Run

1. Install **XAMPP** and start Apache and MySQL.
2. Place the QuickCart project inside the `htdocs` folder.
3. Create the QuickCart database in **phpMyAdmin** and import the provided SQL file.
4. Configure the database connection in the PHP files.
5. Install the Node.js dependencies using:

```bash
npm install
```

6. Start the Node.js server:

```bash
node server.js
```

7. Open the project in your browser:

```text
http://localhost/QuickCart
```

## 💬 Real-Time Chat

QuickCart uses **Node.js and Socket.IO** to provide real-time chat between users and the admin without requiring the page to be refreshed.

## 📧 Password Reset

If a user forgets their password, QuickCart uses **PHPMailer** to send a password reset link to their registered email address.

## 🎯 Project Objective

The main objective of QuickCart is to build a functional e-commerce platform while gaining practical experience in **web development, database management, authentication, email integration, and real-time communication**.

## 👨‍💻 Author

**Pavan Kumar Shetty**
