<?php require_once __DIR__ . '/../login/verifica_user.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/MINI SISTEMA/style/styleForm.css">
</head>
<style> 
        #aluno {
            outline: 2px solid white;
            border-radius: 5px;
        }

        .container-resultado {
            display: flex;
            flex-direction: column;
            max-width: 300px;
            margin: 10px auto;
            padding: 15px 35px;
            outline: 2px solid black;
            border-radius: 10px;
        }
</style>
<body>
    <?php  include __DIR__ . '/../includes/header.php'; ?>
    

    <form action="" method="post">
        <label for="id">ID: </label>
        <input type="number" name="id" id="id">
        <input type="reset" value="limpar" class="botao">
        <input type="submit" value="Consultar" class="botao">
    </form>

<div class="container-resultado">
    <p><u>Resultado da busca: </u></p>
    <?php  
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        consultar($conexao, $_POST['id']);
    }
?>
</div>

<?php require_once __DIR__ . '/../includes/functions.php'; ?>
</body>
</html>

