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
