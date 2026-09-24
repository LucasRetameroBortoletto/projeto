<?php 
// Incluir no topo das páginas que só o administrador pode usar
// (cadastrar, editar, excluir, relatório e consulta).
require_once __DIR__ . '/verifica_user.php'; // primeiro: precisa estar logado

if (!usuario_admin()) {
    definir_aviso('erro', 'Esta área é restrita ao administrador.');
    redirecionar('index.php'); // redirecionar() já faz o exit
}
?>
