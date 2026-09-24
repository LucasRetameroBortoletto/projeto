<?php require_once __DIR__ . '/../includes/functions.php'; ?>
<?php require_once __DIR__ . '/../login/verifica_user.php';?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/MINI SISTEMA/style/styleForm.css">
</head>
<style>
        #deletar {
           outline: 2px solid white;
            border-radius: 5px;
        }
</style>
<body>


<?php include __DIR__ . '/../includes/header.php'; 
?>

    <form action="" method="post">
        <label for="id">ID: </label>
        <input type="number" name="id" id="id">
        <input type="reset" value="limpar" class="botao">
        <input type="submit" value="Apagar" class="botao">
    </form>


    <?php 
    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        deletar($conexao, $_POST['id']);
    }
    include __DIR__ . '/../includes/footer.php';
    ?>
<footer> </footer>
</body>
</html>

