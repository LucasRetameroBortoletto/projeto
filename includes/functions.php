<?php 
require_once __DIR__ . '/../database/connect_postgres.php';


function cadastrar($conexao, $nome, $nasc, $turma, $ativo) {
    $sql = "INSERT INTO alunos1 (nome, nasc, turma, ativo) VALUES(:nome, :nasc, :turma, :ativo)";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":nome", $nome);
    $stmt->bindParam(":nasc",$nasc);
    $stmt->bindParam(":turma",$turma);
    $stmt->bindParam(":ativo",$ativo);

    $stmt->execute();
    echo "Aluno cadastrado com sucesso!";

}

function deletar($conexao, $id) {
    if($_SERVER['REQUEST_METHOD'] == "POST"){

    $id = $_POST['id'];
    $sql = "DELETE FROM alunos1 WHERE id = :id";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    echo "Registro apagado com sucesso"; 
}
}

function consultar($conexao, $id) {
    $id = $_POST['id'];
    $sql = "SELECT nome, nasc, turma, ativo FROM alunos1 WHERE id =  :id";
    
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "Aluno: {$aluno['nome']}<br> Turma: {$aluno['turma']}<br> Nascimento: {$aluno['nasc']}";
}

function relatorio($conexao) {
        $sql = "SELECT * FROM alunos1";

    //stmt é uma variável, função do própio PDO
    $stmt = $conexao->prepare($sql);
    $stmt->execute();

    $alunos1 = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($alunos1 as $aluno) {
        echo "ID: {$aluno['id']}<br>";
        echo "Nome: {$aluno['nome']}<br>";
        echo "Nascimento: {$aluno['nasc']}<br>";
        echo "Turma: {$aluno['turma']}<br>";
        echo "Ativo: {$aluno['ativo']}<br>";
        echo "<br>";
        echo "<hr>";
        echo "<br>";
    }
}

function atualizar($conexao, $id,  $nome, $nasc, $turma, $ativo) {

    $sql = "UPDATE alunos1 SET nome = :nome, id = :id, turma = :turma, nasc = :nasc, ativo = :ativo WHERE id = :id";

    $stmt = $conexao->prepare($sql);

    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":nome", $nome);
    $stmt->bindValue(":nasc", $nasc);
    $stmt->bindValue(":turma", $turma);
    $stmt->bindValue(":ativo", $ativo);
    
    $stmt->execute();

echo "Alteração realizada com sucesso!!";
}

//Função cadastrar usuários login

function cadastrar_user($conexao,$email , $password) {
    $sql = "INSERT INTO usuarios(email, senha) VALUES(:email, :senha)";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":senha",$password);

    $stmt->execute();
    echo "Usuário cadastrado com sucesso!";

}

function consultar_user($conexao, $email) {

    $sql = "SELECT id, email, senha FROM usuarios WHERE email =  :email";
    try {
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

     $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    return $usuario; 
    } catch (PDOException $e) {
        echo $e -> getMessage();
    }
}
?>