<?php
//arquivo para ser chamado sempre que precisar conectar ao banco de dados, por exemplos quando formos fazer um CRUD pelo php
// A estrutura das tabelas está em database/estrutura.sql

// Preencha com as credenciais do seu PostgreSQL
$host = "192.168.10.34";
$dbname = "escola";
$user = "escola";
$pass = "escola";

try {
    $conexao = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $user,
        $pass,
        [
            // Erros de SQL viram exceções em vez de falhar em silêncio
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
    return $conexao;
} catch (PDOException $e) {
    // Sem conexão nenhuma página funciona, então paramos aqui.
    // O detalhe do erro só aparece com MODO_DEBUG (includes/config.php),
    // porque ele pode expor host e usuário do banco.
    http_response_code(500);
    echo "Não foi possível conectar ao banco de dados.";
    if (defined('MODO_DEBUG') && MODO_DEBUG) {
        echo "<br>Erro: " . htmlspecialchars($e->getMessage());
    }
    exit;
}
?>
