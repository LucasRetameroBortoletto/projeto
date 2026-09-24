<?php
// Configurações gerais do site. Carregado por includes/functions.php,
// então toda página que inclui functions.php já tem isto disponível.

// Pasta do projeto dentro do servidor: a parte da URL entre o domínio e os arquivos.
//   http://localhost/MINI SISTEMA/index.php  ->  '/MINI SISTEMA'
// Se rodar com "php -S localhost:8000" de dentro da pasta do projeto, use ''.
define('BASE_URL', '/MINI SISTEMA');

// true = mostra o erro detalhado quando a conexão com o banco falha.
// Útil enquanto você configura as credenciais; troque para false em produção.
define('MODO_DEBUG', true);

// A sessão guarda o login e o carrinho, e o header precisa dela em todas as páginas.
// Precisa ser iniciada antes de qualquer HTML ser enviado ao navegador.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
