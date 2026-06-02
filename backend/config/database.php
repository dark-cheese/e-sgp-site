<?php
// backend/config/database.php

class Database {
    private $host     = "200.131.251.11";
    private $port     = 3341;
    private $db_name  = "2026ProjetoInv";
    private $username = "2026Iventario";
    private $password = "Inventa@2026";

    public $conn;
    private $ultimo_erro = null;

    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8;connect_timeout=10";

            $this->conn = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_TIMEOUT            => 10,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
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
<!-- esse é o arquivo de conexão  -->
