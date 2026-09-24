<?php require_once __DIR__ . '/../login/verifica_user.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
</head>
<style>
        #relatorio {
            outline: 2px solid white;
            border-radius: 5px;
        }
        .container-id {
            outline: 2px solid black;
            padding: 10px;
        }

        body {
            text-align: center;
        }
</style>
<body>
    <?php 
    include __DIR__ . '/../includes/header.php';
?> 
<?php require_once __DIR__ . '/../includes/functions.php'; ?>

<?php
        relatorio($conexao);
    ?>


</body>
</html>


