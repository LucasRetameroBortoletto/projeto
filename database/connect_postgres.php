<?php 
//arquivo para ser chamado sempre que precisar conectar ao banco de dados, por exemplos quando formos fazer um CRUD pelo php 
// A estrutura das tabelas está em database/estrutura.sql

$host = "192.168.10.34";
$dbname = "escola";
$user = "escola";
$pass = "escola";

try {
    $conexao = new PDO(
        "pgsql:host=$host;dbname=$dbname",
        $user,
        $pass
    );
    return $conexao; 
} catch (PDOException $e) {
    echo "Erro: ". $e->getMessage();
    exit; // sem conexão nenhuma página funciona, então paramos aqui
}
?>
