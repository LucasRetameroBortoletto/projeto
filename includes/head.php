<?php
// <head> usado por todas as páginas.
// Antes do include, a página pode definir:
//   $titulo         -> texto da aba do navegador
//   $estilos        -> arquivos CSS extras de assets/css/  (ex.: ['vitrine.css'])
//   $scripts        -> arquivos JS extras de assets/js/     (ex.: ['login.js'])
//   $scripts_inicio -> JS que precisa rodar antes de a página aparecer (splash)
//   $classes_html   -> classes para a tag <html> (ex.: 'sem-splash')
if (!isset($estilos)) $estilos = [];
if (!isset($scripts)) $scripts = [];
if (!isset($scripts_inicio)) $scripts_inicio = [];
if (!isset($classes_html)) $classes_html = '';

// Quem não está logado pode abrir o login pelo botão "Entrar" do header
// (o cartão da metamorfose), então carregamos o CSS e o JS dele.
if (!usuario_logado()) {
    if (!in_array('login.css', $estilos)) $estilos[] = 'login.css';
    if (!in_array('login.js', $scripts))  $scripts[] = 'login.js';
    $estilos[] = 'metamorfose.css';
    $scripts[] = 'metamorfose.js';

    // Voltou de uma senha errada digitada no cartão: a página já abre com o
    // cartão aberto (metamorfose-aberta) e sem a splash de abertura (sem-splash)
    if (isset($_SESSION['login_erro'])) {
        $classes_html .= ' metamorfose-aberta sem-splash';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="<?= e(trim($classes_html)) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'Lapisari') ?></title>

    <!-- Fontes: Cormorant Garamond (títulos), Manrope (textos) e Mrs Saint Delafield (logotipo provisório) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Manrope:wght@400;500;600&family=Mrs+Saint+Delafield&display=swap">

    <link rel="stylesheet" href="<?= url('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= url('assets/css/layout.css') ?>">
    <?php foreach ($estilos as $arquivo_css): ?>
    <link rel="stylesheet" href="<?= url('assets/css/' . $arquivo_css) ?>">
    <?php endforeach; ?>

    <?php foreach ($scripts_inicio as $arquivo_js): ?>
    <script src="<?= url('assets/js/' . $arquivo_js) ?>"></script>
    <?php endforeach; ?>

    <!-- defer: o script baixa em paralelo e só roda depois que o HTML foi lido -->
    <script src="<?= url('assets/js/site.js') ?>" defer></script>
    <?php foreach ($scripts as $arquivo_js): ?>
    <script src="<?= url('assets/js/' . $arquivo_js) ?>" defer></script>
    <?php endforeach; ?>
</head>
