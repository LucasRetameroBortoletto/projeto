    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
        }
        header {
            text-align: center;
        }

        nav {
        justify-content: space-between;
        display: flex;
        background-color: black;
        padding: 16px;
        margin-bottom: 20px;
        margin-bottom: 5%;
        }

        nav a {
            text-transform: uppercase;
            margin-left: 10px;
            text-decoration: none;
            padding: 5px;
            color: white;
            outline: 2px solid transparent;
            transition: outline-color 0.3s ease-in-out;
            border-radius: 5px;
        }

        nav a:hover {
            outline: 2px solid lightslategray;
            border-radius: 5px;
        }
    </style>
    <body>
        
    </body>
    </html>
    <div class="container-header">
    <header>
        <nav>
            <div>
            <a href="/MINI SISTEMA/index.php" id="inicio">Início</a>
            <a href="/MINI SISTEMA/app/create.php" id="cadastrar">Cadastrar</a>
            <a href="/MINI SISTEMA/app/delete.php" id="deletar">Deletar</a>
            <a href="/MINI SISTEMA/app/select.php" id="relatorio">Relatório</a>
            <a href="/MINI SISTEMA/app/select_where.php" id="aluno">Aluno</a>
            <a href="/MINI SISTEMA/app/update.php" id="atualizar">Atualizar</a>
            <a href="/MINI SISTEMA/login/registerUser.php" id="register">Registrar</a>
            </div>
            <div>
                <a href="/MINI SISTEMA/login/login.php"">LOGIN</a>
                <a href="/MINI SISTEMA/login/logout.php">LOGOUT</a>
            </div>
        </nav>
    </div>
    </header>