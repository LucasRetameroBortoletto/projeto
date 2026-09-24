<?php
// <head> compartilhado por todas as páginas.
// Antes de incluir, a página pode definir:
//   $titulo  -> texto da aba do navegador
//   $estilos -> CSS extras de assets/css/ (ex.: ['vitrine.css'])
//   $scripts -> JS extras de assets/js/ (ex.: ['login.js'])
//   $scripts_inicio -> JS que precisa rodar ANTES da página ser desenhada
//                      (sem defer; use só para arquivos minúsculos)
$estilos = $estilos ?? [];
$scripts = $scripts ?? [];
$scripts_inicio = $scripts_inicio ?? [];
//   $classes_html -> classes extras no <html> (ex.: 'sem-splash')
$classes_html = $classes_html ?? '';
?>
<!DOCTYPE html>
<html lang="pt-BR"<?= $classes_html ? ' class="' . e($classes_html) . '"' : '' ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titulo ?? 'Lapisari') ?></title>

    <!-- Fontes: Cormorant Garamond (títulos), Manrope (textos) e Mrs Saint Delafield (logotipo provisório) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Manrope:wght@400;500;600&family=Mrs+Saint+Delafield&display=swap">

    <link rel="stylesheet" href="<?= asset('assets/css/base.css') ?>">
    <link rel="stylesheet" href="<?= asset('assets/css/layout.css') ?>">
    <?php foreach ($estilos as $arquivo_css): ?>
    <link rel="stylesheet" href="<?= asset('assets/css/' . $arquivo_css) ?>">
    <?php endforeach; ?>

    <?php foreach ($scripts_inicio as $arquivo_js): ?>
    <script src="<?= asset('assets/js/' . $arquivo_js) ?>"></script>
    <?php endforeach; ?>

    <!-- defer: o script baixa em paralelo e só roda depois que o HTML foi lido -->
    <script src="<?= asset('assets/js/site.js') ?>" defer></script>
    <?php foreach ($scripts as $arquivo_js): ?>
    <script src="<?= asset('assets/js/' . $arquivo_js) ?>" defer></script>
    <?php endforeach; ?>
</head>
