<!-- PROJECT SHIELD -->
<p align="center">
  <a href="https://github.com/dhruvilob-stack/payment-module">
    <img src="https://img.shields.io/github/v/release/dhruvilob-stack/payment-module?style=for-the-badge" alt="Release">
    <img src="https://img.shields.io/github/issues/dhruvilob-stack/payment-module?style=for-the-badge" alt="Issues">
    <img src="https://img.shields.io/github/license/dhruvilob-stack/payment-module?style=for-the-badge" alt="License">
  </a>
</p>

<!-- TITLE -->
<h1 align="center">💳 Payment Module</h1>
<p align="center">
  A clean, simple & extensible payment gateway integration module built on Laravel 🎯
</p>

---

## 🚀 About The Project

This repository contains a payment module designed to make payment gateway integration smooth and modular in Laravel applications. Whether you’re processing UPI, card payments, subscriptions, or invoices – this module can be your backbone for secure transactions.

🌐 **Live Demo:** https://payment-module-47xx.onrender.com/student :contentReference[oaicite:1]{index=1}  
📦 Built with Laravel, Vue, and modern frontend tooling.

---

## 🧱 Features

✨ **Clean & Modular Structure**  
🪪 Supports multiple payment types & gateways  
⚙️ Easy to customize and extend with your own providers  
📊 Includes basic transaction logging & API routes  
🔒 Secure with validation & environment configurations  

---

## 🧭 Table of Contents

- [About The Project](#-about-the-project)  
- [Features](#-features)  
- [Screenshots](#-screenshots)  
- [Installation](#-installation)  
- [Usage](#-usage)  
- [API Endpoints](#-api-endpoints)  
- [Contributing](#-contributing)  
- [License](#-license)  
- [Contact](#-contact)

---

## 📸 Screenshots

<p align="center">
  <img width="1600" height="829" alt="image" src="https://github.com/user-attachments/assets/98efb5b9-ca31-4085-ad79-9034a3bac2dd" alt="Dashboard"/>
  <img width="1600" height="829" alt="image" src="https://github.com/user-attachments/assets/1443d5d3-6059-430b-9566-c30d9c78f982" alt="Payment Page"/>

</p>

---

## 🧰 Installation

Follow these steps to get your local development copy up and running.

```sh
# Clone
git clone https://github.com/dhruvilob-stack/payment-module.git

# Go into the directory
cd payment-module

# Install backend dependencies
composer install

# Install frontend dependencies
npm install && npm run build

# Copy environment example and set keys
cp .env.example .env
php artisan key:generate

# Start the server
php artisan serve
