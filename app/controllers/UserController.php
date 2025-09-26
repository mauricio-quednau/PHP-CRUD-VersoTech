<?php

require_once __DIR__ . '/../db.php';

class UserController {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
    }

    public function getUsuario($dados) {
        $id = trim($dados['usuario_id'] ?? false);
        if (!$id) {
            return ['sucesso' => false, 'mensagem' => 'Usuário não encontrado'];
        }

        $query = "SELECT u.id, u.name, u.email,
                 GROUP_CONCAT(c.id) as cores
          FROM users u
          LEFT JOIN users_colors uc ON u.id = uc.user_id
          LEFT JOIN colors c ON uc.color_id = c.id
          WHERE u.id = :id
          GROUP BY u.id, u.name, u.email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $row['cores'] = $row['cores'] ? explode(',', $row['cores']) : [];
            return ['sucesso' => true, 'usuario' => $row];
        } else {
            return['sucesso' => false, 'mensagem' => 'Usuário não encontrado'];
        }
    }

    public function listar($dados) {
        //$query = "SELECT * FROM users";
        $query = "SELECT u.id, u.name, u.email, 
                     GROUP_CONCAT(c.name) as cores
              FROM users u
              LEFT JOIN users_colors uc ON u.id = uc.user_id
              LEFT JOIN colors c ON uc.color_id = c.id
              GROUP BY u.id, u.name, u.email";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $usuarios = [];
        foreach ($rows as $row) {
            $usuarios[] = [
                'id' => $row['id'],
                'nome' => $row['name'],
                'email' => $row['email'],
                'cores' => $row['cores'] ? explode(',', $row['cores']) : []
            ];
        }

        return ['sucesso' => true, 'usuarios' => $usuarios];
    }

    public function inserir($dados) {
        // Sanitize e validações básicas
        $nome = trim($dados['nome'] ?? '');
        $email = trim($dados['email'] ?? '');
        $cores = $dados['cores'] ?? [];
        if (!$nome || !$email) {
            return ['sucesso' => false, 'mensagem' => 'Nome e e-mail são obrigatórios.'];
        }

        // Preparar INSERT usuario
        $sql = "INSERT INTO users (name, email) 
            VALUES (:nome, :email)";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            $userId = $this->conn->lastInsertId();

            //Preparar insrt cores
            $sql = "INSERT INTO users_colors (user_id, color_id) 
            VALUES (:user_id, :color_id)";

            foreach ($cores as $value) {
                if (!is_numeric($value))
                    continue;
                $stmt = $this->conn->prepare($sql);
                $stmt->bindValue(':user_id', $userId);
                $stmt->bindValue(':color_id', $value);
                $stmt->execute();
            }

            return ['sucesso' => true, 'mensagem' => 'Usuário cadastrado com sucesso!'];
        } catch (PDOException $e) {
            return ['sucesso' => false, 'mensagem' => 'Erro ao cadastrar usuário: ' . $e->getMessage()];
        }
    }

    public function atualizar($dados) {
        $id = trim($dados['usuario_id'] ?? '');
        $nome = trim($dados['nome'] ?? '');
        $email = trim($dados['email'] ?? '');
        $cores = $dados['cores'] ?? [];
        if (!$id || !$nome || !$email) {
            return ['sucesso' => false, 'mensagem' => 'Dados inválidos para alteração.'];
        }

        $stmt = $this->conn->prepare("UPDATE users SET name = :nome, email = :email WHERE id = :id");
        $stmt->bindValue(':id', $id);
        $stmt->bindValue(':nome', $nome);
        $stmt->bindValue(':email', $email);
        $stmt->execute();

        //remover todas as cores e add novamente somente as relacionadas
        $stmt = $this->conn->prepare("DELETE FROM users_colors WHERE user_id = ?");
        $stmt->execute([$id]);
        $sql = "INSERT INTO users_colors (user_id, color_id) VALUES (:user_id, :color_id)";
        foreach ($cores as $value) {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindValue(':user_id', $id);
            $stmt->bindValue(':color_id', $value);
            $stmt->execute();
        }

        return['sucesso' => true, 'mensagem' => 'Registro atualizado.'];
    }

    public function excluir($dados) {
        $id = trim($dados['usuario_id'] ?? '');
        if (!$id) {
            return ['sucesso' => false, 'mensagem' => 'Dados inválidos para exclusão.'];
        }

        $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        return['sucesso' => true, 'mensagem' => 'Registro removido.'];
    }

}
