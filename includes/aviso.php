<?php
// Mostra (uma única vez) o aviso guardado com definir_aviso() antes de um redirecionamento.
// Incluído pelo header.php, então aparece em qualquer página que tenha o header.
$aviso = pegar_aviso();
?>
<?php if ($aviso): ?>
    <div class="aviso aviso--<?= e($aviso['tipo']) ?>" role="<?= $aviso['tipo'] === 'erro' ? 'alert' : 'status' ?>" data-aviso>
        <p><?= e($aviso['texto']) ?></p>
        <button type="button" class="aviso__fechar" aria-label="Fechar aviso" data-fechar-aviso>&times;</button>
    </div>
<?php endif; ?>
