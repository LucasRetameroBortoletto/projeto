<?php 
// Colocar no topo das páginas que só o administrador pode usar
// (cadastrar, atualizar, excluir, relatório e consulta).
require_once __DIR__ . '/verifica_user.php'; // primeiro precisa estar logado

if (!usuario_admin()) {
    // Logado, mas é cliente: volta para a vitrine
    header("Location: " . url('index.php'));
    exit;
}
?>
