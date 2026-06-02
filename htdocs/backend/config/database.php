<?php
// backend/config/database.php
// Configuração da conexão com o banco de dados
// ================================================
// INSTRUÇÕES INFINITYFREE:
// Substitua os valores abaixo com os dados do seu painel InfinityFree:
//   Painel > MySQL Databases > seu banco criado
// ================================================

class Database {
    // ⬇️ ALTERE AQUI com os dados do seu InfinityFree
   private $host     = "sql211.infinityfree.com"; // ← seu servidor
    private $db_name  = "if0_41731400_XXX";         // ← seu banco
    private $username = "if0_41731400";              // ← seu usuário
    private $password = "Esgp147258";   

    public $conn;
    private $ultimo_erro = null;

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host=" . $this->host
                 . ";port=" . $this->port
                 . ";dbname=" . $this->db_name
                 . ";charset=utf8;connect_timeout=10";

            $this->conn = new PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_TIMEOUT            => 10,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
            $this->conn->exec("SET NAMES utf8");

        } catch (PDOException $e) {
            $this->ultimo_erro = "Erro de conexão: " . $e->getMessage();
            error_log($this->ultimo_erro);
        }

        return $this->conn;
    }

    public function getUltimoErro() {
        return $this->ultimo_erro;
    }
}
?>
