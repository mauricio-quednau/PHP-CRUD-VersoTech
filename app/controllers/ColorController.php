<?php

require_once __DIR__ . '/../db.php';

/**
 * Description of ColorController
 *
 * @author Mauricio
 */
class ColorController {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->conectar();
        $this->checkOrCreateTables();
    }

    private function checkOrCreateTables() {
        try {
            $this->conn->query("SELECT 1 FROM colors LIMIT 1");
        } catch (PDOException $e) {
            $this->createColorsTable();
            $this->seedColors();
        }
    }
    
    private function createColorsTable() {
        $sql = "CREATE TABLE colors (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL)";
        $this->conn->exec($sql);
        
        $sql = "create table users_colors(
        user_id INTEGER NOT NULL,
        color_id INTEGER NOT NULL)";
        $this->conn->exec($sql);
    }

    private function seedColors() {
        $this->conn->exec("INSERT INTO colors (name) VALUES
        ('Blue'), ('Red'), ('Yellow'), ('Green')");
    }

    public function listar($dados) {
        // Verificar se já existe usuário com esse email
        $query = "SELECT * FROM colors";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ['sucesso' => true, 'cores' => $resultados];
    }

}
