<?php require_once __DIR__ . '/../includes/functions.php'; ?>
<?php require_once __DIR__ . '/../login/verifica_user.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro Aluno</title>
    <link rel="stylesheet" href="/MINI SISTEMA/style/styleForm.css">
    <style>
        #cadastrar {
            outline: 2px solid white;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php 
        include __DIR__ . '/../includes/header.php';    
    ?>
    <form action="" method="post">
        <label for="nome">Nome</label>
        <input type="text" name="nome" id="nome" required>

        <label for="turma">Turma</label>
        <input type="text" name="turma" id="turma" required>

        <label for="nasc">Nascimento</label>
        <input type="date" name="nasc" id="nasc" required>

        <div class="form-radio">
            <label for="ativo" required>Ativo: </label>
            <input type="radio" name="ativo" id="sim" value="true" class="radio">
            <label for="sim">Sim</label>
            <input type="radio" name="ativo" id="nao" value="false" class="radio">
            <label for="nao">Não</label>
        </div>

        <input type="reset" value="Limpar" class="botao">
        <input type="submit" value="Cadastrar" class="botao">
    </form>

    <?php 
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        cadastrar($conexao, $_POST['nome'], $_POST['nasc'], $_POST['turma'], $_POST['ativo']);
    }
    include __DIR__ . '/../includes/footer.php';
    ?>

</body>
</html>