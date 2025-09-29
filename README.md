# 🚀 CRUD de Usuários com PHP e Bootstrap

Este projeto é um exemplo de **CRUD de Usuários** desenvolvido em **PHP ** no backend e **Bootstrap 5** no frontend.  
Permite cadastrar, listar, editar e excluir usuários, além de vincular múltiplas cores a cada usuário.

## 🛠️ Projeto disponível em:
https://mauriciodosite.com.br/versotech/

## 🛠️ Tecnologias Utilizadas

- [PHP](https://www.php.net/) (PDO)
- [MySQL](https://www.mysql.org/)
- [Bootstrap 5](https://getbootstrap.com/)

## 📋 Pré-requisitos

- PHP >= 7.3
- Servidor web (Apache/Nginx ou embutido do PHP)
- Banco de dados MySQL

## ⚙️ Instalação

1. Clone o repositório:
   ```bash
   git clone https://github.com/mauricio-quednau/PHP-CRUD-VersoTech.git

2. Configure o banco de dados no arquivo db.php:
   (app/db.php)

3. Criação de tabelas:
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);

CREATE TABLE colors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL
);

CREATE TABLE user_colors (
    user_id INT,
    color_id INT,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (color_id) REFERENCES colors(id)
);

4. Inserção de dados da tabela colors:
INSERT INTO colors(name) VALUES ('Blue'), ('Red'), ('Yellow'), ('Green')