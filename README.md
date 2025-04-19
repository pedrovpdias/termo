# 🎮 Termo Clone – Jogo de Adivinhação de Palavras

Este é um projeto pessoal inspirado no popular jogo "Termo", desenvolvido com **Vue 3** e **Laravel**. O objetivo é adivinhar uma palavra secreta em até 6 tentativas, com dicas visuais baseadas nas letras corretas e suas posições.

Criado como um exercício de aprendizado e experimentação com tecnologias modernas do ecossistema web.

🎮 Jogue agora: [termo.laravel.cloud](https://termo.laravel.cloud/)

---

## ✨ Funcionalidades

- ✅ Interface responsiva e dinâmica com **Vue 3** & **TailwindCSS 4**
- 🧠 Backend em Laravel para validação de palavras
- 🎉 Animação de vitória com confetes e efeito sonoro
- 🎹 Teclado virtual para dispositivos touch
- 💾 Persistência de progresso com **localStorage**

---

## 🛠️ Tecnologias Utilizadas

- [Vue 3](https://vuejs.org/) – Interface do jogo
- [Laravel](https://laravel.com/) – Backend e API de validação de palavras
- **MySQL** – Armazenamento das palavras e estatísticas
- [TailwindCSS 4](https://tailwindcss.com/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)
- [canvas-confetti](https://www.npmjs.com/package/canvas-confetti) – Animação de vitória
- **HTML5 Audio** – Efeitos sonoros
- **localStorage** – Persistência no navegador

---

## 📡 API & Banco de Dados

O backend da aplicação, desenvolvido com **Laravel**, fornece uma API interna para:

- Validar palavras digitadas pelo jogador

Os dados persistentes são armazenados em um banco **MySQL**, utilizando migrations que podem ser encontradas em `backend/database/migrations`.

---

## 🚀 Instalação

# Clone o repositório
git clone https://github.com/pedrovpdias/termo.git
cd termo

# Instale as dependências do frontend
cd frontend
npm install
npm run dev

# Em outra aba, vá para o backend
cd ../backend
composer install
cp .env.example .env
php artisan key:generate

# Configure o banco de dados no .env e rode as migrations
php artisan migrate

# Inicie o servidor Laravel
php artisan serve

---

## 📁 Estrutura do Projeto

- /frontend     # Aplicação Vue 3
- /backend      # API Laravel 12

---

## 👨‍💻 Sobre o Projeto
Este projeto foi desenvolvido com foco em aprendizado, explorando integrações entre frontend e backend, animações e interações com o usuário. É também uma forma divertida de aplicar boas práticas com Vue e Laravel.

Feito com 💻 + ☕ + um pouquinho de sorte nas tentativas por Pedro Dias

---

## 🖼️ Preview

![Demo](https://github.com/pedrovpdias/termo/blob/main/public/assets/demo.gif?raw=true)
