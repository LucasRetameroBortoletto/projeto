<?php
require_once __DIR__ . '/../includes/functions.php';

// O login grava $_SESSION['id'] (antes este arquivo conferia $_SESSION['usuario'],
// que nunca existia, então o logout nunca acontecia).
if(usuario_logado()) {
    // Apaga só os dados de login: o carrinho continua na sessão
    unset($_SESSION['id'], $_SESSION['papel']);
    session_regenerate_id(true);

    definir_aviso('sucesso', 'Logout realizado com sucesso.');
} else {
    definir_aviso('erro', 'Você não possui um login ativo!');
}

redirecionar('index.php');
