<?php
// Cartão de login que "nasce" do header (Fase 4: metamorfose header -> login).
// Incluído pelo header.php só para quem não está logado.
//
// Fica escondido (hidden) até o clique em "Entrar"; o metamorfose.js então
// transforma o header neste cartão. Sem JavaScript, o "Entrar" continua
// sendo um link normal para login/login.php.
//
// Se o login falhou, o login.php volta para a página inicial com
// $_SESSION['login_erro']: o cartão já é desenhado aberto, com a mensagem.
$login_erro = $_SESSION['login_erro'] ?? null;
unset($_SESSION['login_erro']);
?>
<div class="metamorfose<?= $login_erro ? ' metamorfose--aberta' : '' ?>" data-metamorfose <?= $login_erro ? '' : 'hidden' ?>>
    <!-- Véu escuro com desfoque sobre a página. Clicar nele fecha o cartão. -->
    <div class="metamorfose__veu" data-fechar-metamorfose></div>

    <div class="metamorfose__cartao" role="dialog" aria-modal="true" aria-labelledby="metamorfose-titulo">
        <!-- Retângulo preto que se transforma do header no cartão (animado pelo JS) -->
        <div class="metamorfose__fundo" aria-hidden="true"></div>

        <button type="button" class="metamorfose__fechar" aria-label="Fechar login" data-fechar-metamorfose>
            <svg viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.3"
                 stroke-linecap="round" aria-hidden="true" focusable="false">
                <path d="M3 3 L13 13 M13 3 L3 13"/>
            </svg>
        </button>

        <!-- O logotipo do header "sobe" para o topo do cartão -->
        <div class="metamorfose__marca">
            <?php
                $classe_assinatura = 'metamorfose__assinatura';
                include __DIR__ . '/assinatura.php';
            ?>
        </div>

        <div class="metamorfose__entrada">
            <h2 id="metamorfose-titulo" class="metamorfose__titulo">Bem-vindo</h2>
            <p class="metamorfose__subtitulo">Faça login para continuar</p>
        </div>

        <?php if ($login_erro): ?>
            <p class="login__erro metamorfose__entrada" role="alert"><?= e($login_erro['mensagem']) ?></p>
        <?php endif; ?>

        <!-- Mesmo endpoint e mesmos campos da página de login.
             "origem" avisa o login.php que o envio veio do cartão (para reabri-lo em caso de erro). -->
        <form action="<?= url('login/login.php') ?>" method="POST" class="login__formulario metamorfose__formulario"
              novalidate data-form-login>
            <input type="hidden" name="origem" value="cartao">

            <div class="login__campo metamorfose__entrada">
                <label for="metamorfose-email">E-mail</label>
                <input type="email" name="email" id="metamorfose-email" autocomplete="email"
                       value="<?= e($login_erro['email'] ?? '') ?>">
            </div>

            <div class="login__campo metamorfose__entrada">
                <label for="metamorfose-senha">Senha</label>
                <input type="password" name="password" id="metamorfose-senha" autocomplete="current-password">
            </div>

            <!-- A lapiseira do header "desce" até aqui e vira o botão de envio -->
            <button type="submit" class="login__lapiseira<?= $login_erro ? ' login__lapiseira--erro' : '' ?>"
                    aria-label="Entrar na conta">
                <?php
                    $classe_lapiseira = 'login__lapiseira-desenho metamorfose__lapiseira';
                    include __DIR__ . '/lapiseira.php';
                ?>
                <span class="login__lapiseira-rotulo metamorfose__entrada" aria-hidden="true">Entrar</span>
            </button>
        </form>

        <p class="metamorfose__rodape metamorfose__entrada">
            Ainda não tem conta? <a href="<?= url('login/registerUser.php') ?>">Criar conta</a>
        </p>
    </div>
</div>
