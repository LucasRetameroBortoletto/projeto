<?php require_once __DIR__ . '/../login/verifica_user.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastra usuário</title>
    <link rel="stylesheet" href="/MINI SISTEMA/style/styleForm.css">
    <style>
        #register {
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
        <label for="email">Nome:</label>
        <input type="email" name="email" id="email" required>

        <label for="password">Senha:</label>
        <input type="password" name="password" id="password">

        <input type="reset" value="Limpar" class="botao">
        <input type="submit" value="Cadastrar" class="botao">
    </form>

<?php require_once __DIR__ . '/../includes/functions.php'; ?>

    <?php 
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        cadastrar_user($conexao, $_POST['email'], $_POST['password']);
    }

    include __DIR__ .'/../includes/footer.php';
    ?>

</body>
</html>