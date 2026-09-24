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

// Erros de banco que ninguém tratou (ex.: tabela que não existe) não devem
// aparecer como "Fatal error" com caminhos de pastas do servidor.
// Este handler mostra uma página curta; com MODO_DEBUG, inclui o detalhe
// e uma dica para os erros mais comuns de configuração.
set_exception_handler(function ($erro) {
    http_response_code(500);
    error_log((string) $erro); // o erro completo continua no log do PHP

    $detalhe = '';
    if (MODO_DEBUG) {
        $dica = '';
        // 42P01 = tabela não existe; 42703 = coluna não existe
        if ($erro instanceof PDOException && in_array($erro->getCode(), ['42P01', '42703'], true)) {
            $dica = '<p><strong>Dica:</strong> rode o script <code>database/estrutura.sql</code> no mesmo banco '
                  . 'configurado em <code>database/connect_postgres.php</code>.</p>';
        }
        $detalhe = $dica . '<pre style="white-space:pre-wrap">'
                 . htmlspecialchars($erro->getMessage()) . '</pre>';
    }

    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><title>Erro · Lapisari</title></head>'
       . '<body style="font-family:system-ui,sans-serif;max-width:720px;margin:80px auto;padding:0 24px;color:#1f2023">'
       . '<h1 style="font-weight:500">Algo deu errado</h1>'
       . '<p>Não foi possível concluir a operação. Tente novamente em instantes.</p>'
       . $detalhe
       . '</body></html>';
});
