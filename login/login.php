<?php session_start() ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<?php require_once __DIR__ . '/../includes/functions.php'; ?>

<?php 
if($_SERVER['REQUEST_METHOD'] == "POST") {
    $usuario = consultar_user($conexao, $_POST['email']); 
    if($usuario['email'] == $_POST['email'] && $usuario['senha'] == $_POST['password']) {
        
        //cria uma sessão para manter o usuário logado
        $_SESSION['id'] = $usuario['id'];

        header("Location: /MINI SISTEMA/index.php"); //leva o usuário a index.php 
    } else {
        $mensagem = "Usuário ou senha inválidos!!";
    }
}

?>
<style>
    * {
        margin: 0;
        padding: 0;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    }
 
    body {
            background-color: rgb(0, 0, 0, 0.4);
    }
    .display {
        background-color: rgba(0, 0, 0, 0.4);
        display: flex;
        height: 100vh;
        align-items: center;
        justify-content: center;
    }
    
    .container {
        background-color: rgba(0, 0, 0, 1);
        width: 300px;
        min-height: 220px;
        padding: 14px 50px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
    }

    .container h1 {
        font-size: 26px;
        color: white;
        text-align: center;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    .container h2 {
        font-size: 20px;
        color: white;
        text-align: center;
        margin-bottom: 20px;
    }

    .container .erro-login {
        font-size: 14px;
        color: red;
        text-align: center;
        margin-bottom: 10px;
    }

    .container form {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    #enviar {
        height: 36px;
        width: 50%;
        margin: 10px auto 0;
        background-color: #666;
        color: white;
        outline: none;
        border: none;
        border-radius: 5px;
        font-size: 15px;
    }
    
    #enviar:hover {
        cursor: pointer;
        background-color: dimgray;
        transition: 0.2s;
    }

    .container form {
        align-items: center;
        gap: 15px;
    }

    .container input{
        width: 100%;
        height: 34px;
        padding: 0 5px;
        background-color: #444;
        color: white;
        border: 3px solid transparent;
        outline: none;
        border-radius: 5px;
    }

    .container input::placeholder {
        color: #bbb;    
    }

    .container input:hover, 
    .container input:focus {
        border: 3px solid white;
        transition: border 0.3s ease;
    }

    #enviar:active {
        background-color: white;
    }

    .container input.campo-invalido {
        border: 2px solid red;
}

    .container input.campo-valido {
        border: 2px solid green;
}

    .container footer {
        margin-top: 10px;
        text-align: center;
        font-size: 12px;
    }

    .container footer a {
        text-decoration: none;
        color: white;
    }

</style>
<body>
<div class="display">
    <div class="container">
        <h1>Bem vindo</h1>
        <h2>Faça login para continuar</h2>
        
        <?php if (isset($mensagem)): ?>
    <p class="erro-login"><?php echo $mensagem; ?></p>
        <?php endif; ?>

        <form action="" method="POST" id="formulario">
            <label hidden for="email">Usuário: </label>
            <input type="email" name="email" placeholder="email" id="email">

            <label hidden for="senha"></label>
            <input type="password" name="password" placeholder="senha" id="password">
            
            <button type="submit" id="enviar">Enviar</button>
        </form>
        <footer>
            <a href="/MINI SISTEMA/index.php">voltar | tela inicial</a>
        </footer>
    </div>
</div>

    <script>
        const form = document.querySelector('#formulario');
        const campoFormEmail = document.querySelector('#email');
        const campoFormSenha = document.querySelector('#password');

    //verifica individualmente após o envio do formulario e add borda vermelha ao input
    form.addEventListener('submit', function(event) {

    // O navegador valida automaticamente os campos 'required'
        if (campoFormEmail.value.trim() === '') {
        campoFormEmail.classList.add('campo-invalido'); //classes criadas css
        campoFormEmail.classList.remove('campo-valido');
        event.preventDefault(); // Impede o envio do formulário
    } else {
        campoFormEmail.classList.add('campo-valido');
        campoFormEmail.classList.remove('campo-invalido');
    }
});

    //verifica individualmente após o envio do formulario e add borda vermelha ao input
    form.addEventListener('submit', function(event) {
    // O navegador valida automaticamente os campos 'required'
        if (campoFormSenha.value.trim() === '') {
        campoFormSenha.classList.add('campo-invalido');
        campoFormSenha.classList.remove('campo-valido');
        event.preventDefault(); // Impede o envio do formulário
    } else {
        campoFormSenha.classList.add('campo-valido');
        campoFormSenha.classList.remove('campo-invalido');
    }

});
    </script>
</body>
</html>