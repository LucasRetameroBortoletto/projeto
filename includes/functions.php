<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../database/connect_postgres.php';

// Bitolas aceitas (as mesmas do CHECK no banco)
$bitolas = ['0.3', '0.5', '0.7', '0.9', '1.3', '2.0'];


// =====================================================================
// Funções de apoio para as telas
// =====================================================================

// Escapa um texto antes de mostrar no HTML.
// Se alguém cadastrar "<script>" como nome, ele aparece como texto e não é executado.
function e($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

// Monta o endereço de uma página ou arquivo do projeto.
// Ex.: url('app/create.php') -> '/MINI SISTEMA/app/create.php'
function url($caminho) {
    return str_replace(' ', '%20', BASE_URL . '/' . $caminho);
}

// Ex.: 129.9 -> "R$ 129,90"
function formatar_preco($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

// Ex.: "0.5" -> "0.5 mm"
function formatar_bitola($bitola) {
    return number_format($bitola, 1, '.', '') . ' mm';
}

// Endereço da foto da lapiseira, ou da imagem padrão quando não há foto
function imagem_lapiseira($lapiseira) {
    if ($lapiseira['imagem']) {
        return url($lapiseira['imagem']);
    }
    return url('assets/img/produto-sem-foto.svg');
}

// Quem está logado? (o login grava o id na sessão)
function usuario_logado() {
    return isset($_SESSION['id']);
}

// O usuário logado é administrador?
// trim/strtolower: aceita "admin", "Admin" ou "admin   " (com espaços sobrando no banco)
function usuario_admin() {
    return isset($_SESSION['papel']) && strtolower(trim($_SESSION['papel'])) == 'admin';
}


// =====================================================================
// Papel do usuário sempre atualizado
// =====================================================================
// O papel (admin/cliente) é conferido no banco a cada página aberta.
// Assim, se você trocar o papel direto no banco, a mudança vale na hora,
// sem precisar sair e entrar de novo.
if (usuario_logado()) {
    $stmt = $conexao->prepare("SELECT papel FROM usuarios WHERE id = :id");
    $stmt->bindParam(":id", $_SESSION['id']);
    $stmt->execute();
    $usuario_atual = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario_atual) {
        $_SESSION['papel'] = $usuario_atual['papel'];
    } else {
        // O usuário foi apagado do banco: desloga
        unset($_SESSION['id']);
        unset($_SESSION['papel']);
    }
}


// =====================================================================
// CRUD de lapiseiras (mesma ideia do CRUD de alunos)
// =====================================================================

function cadastrar($conexao, $modelo, $marca, $bitola, $preco, $imagem, $ativo) {
    $sql = "INSERT INTO lapiseiras (modelo, marca, bitola, preco, imagem, ativo)
            VALUES (:modelo, :marca, :bitola, :preco, :imagem, :ativo)";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":modelo", $modelo);
    $stmt->bindParam(":marca", $marca);
    $stmt->bindParam(":bitola", $bitola);
    $stmt->bindParam(":preco", $preco);
    $stmt->bindParam(":imagem", $imagem);
    $stmt->bindParam(":ativo", $ativo);   // "true" ou "false", vindo do formulário

    $stmt->execute();
}

function deletar($conexao, $id) {
    $sql = "DELETE FROM lapiseiras WHERE id = :id";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
}

// Busca uma lapiseira pelo id. Devolve os dados ou false se não existir.
function consultar($conexao, $id) {
    $sql = "SELECT * FROM lapiseiras WHERE id = :id";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":id", $id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Lista as lapiseiras.
//   $bitola  -> '' para todas, ou uma bitola ('0.5', ...) para filtrar
//   $ordem   -> 'novidades', 'preco_asc' ou 'preco_desc'
//   $todas   -> true mostra também as que estão fora da vitrine (só o admin usa)
function relatorio($conexao, $bitola = '', $ordem = 'novidades', $todas = false) {
    $sql = "SELECT * FROM lapiseiras WHERE 1 = 1";
    // "WHERE 1 = 1" é sempre verdadeiro: serve só para podermos
    // ir acrescentando "AND ..." abaixo sem se preocupar com o primeiro filtro

    if (!$todas) {
        $sql .= " AND ativo = true";
    }
    if ($bitola != '') {
        $sql .= " AND bitola = :bitola";
    }

    // A ordenação não pode ser um parâmetro (:ordem) do prepared statement,
    // então escolhemos entre textos fixos. Nada digitado pelo usuário entra aqui.
    if ($ordem == 'preco_asc') {
        $sql .= " ORDER BY preco ASC";
    } elseif ($ordem == 'preco_desc') {
        $sql .= " ORDER BY preco DESC";
    } else {
        $sql .= " ORDER BY criado_em DESC, id DESC";
    }

    //stmt é uma variável, função do própio PDO
    $stmt = $conexao->prepare($sql);
    if ($bitola != '') {
        $stmt->bindParam(":bitola", $bitola);
    }
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function atualizar($conexao, $id, $modelo, $marca, $bitola, $preco, $imagem, $ativo) {

    $sql = "UPDATE lapiseiras SET modelo = :modelo, marca = :marca, bitola = :bitola,
            preco = :preco, imagem = :imagem, ativo = :ativo WHERE id = :id";

    $stmt = $conexao->prepare($sql);

    $stmt->bindValue(":id", $id);
    $stmt->bindValue(":modelo", $modelo);
    $stmt->bindValue(":marca", $marca);
    $stmt->bindValue(":bitola", $bitola);
    $stmt->bindValue(":preco", $preco);
    $stmt->bindValue(":imagem", $imagem);
    $stmt->bindValue(":ativo", $ativo);

    $stmt->execute();
}

// Confere os campos do formulário de lapiseira.
// Devolve uma lista de mensagens de erro (lista vazia = tudo certo).
function validar_lapiseira($dados) {
    global $bitolas;
    $erros = [];

    // Se algum campo não veio no envio, considera vazio
    $campos = ['modelo', 'marca', 'bitola', 'preco', 'ativo'];
    foreach ($campos as $campo) {
        if (!isset($dados[$campo])) {
            $dados[$campo] = '';
        }
    }

    if (trim($dados['modelo']) == '') {
        $erros[] = 'Informe o modelo.';
    }
    if (trim($dados['marca']) == '') {
        $erros[] = 'Informe a marca.';
    }
    if (!in_array($dados['bitola'], $bitolas)) {
        $erros[] = 'Escolha uma bitola da lista.';
    }
    if (!is_numeric($dados['preco']) || $dados['preco'] < 0) {
        $erros[] = 'Informe um preço válido.';
    }
    if ($dados['ativo'] != 'true' && $dados['ativo'] != 'false') {
        $erros[] = 'Escolha se a lapiseira aparece na vitrine.';
    }

    return $erros;
}


// =====================================================================
// Upload da foto
// =====================================================================

// Salva a foto enviada no formulário, depois de conferir tipo e tamanho.
// Devolve um array com duas posições:
//   ['uploads/lapiseiras/abc123.jpg', '']  -> deu certo (caminho, sem erro)
//   [null, '']                            -> nenhuma foto foi enviada
//   [null, 'mensagem']                    -> foto recusada (sem caminho, com erro)
function salvar_imagem($arquivo) {
    // Campo de arquivo vazio: não é erro, a lapiseira só fica sem foto
    if (!isset($arquivo) || $arquivo['error'] == UPLOAD_ERR_NO_FILE) {
        return [null, ''];
    }

    // Tamanho: no máximo 2 MB. UPLOAD_ERR_INI_SIZE = passou do limite do php.ini
    if ($arquivo['error'] == UPLOAD_ERR_INI_SIZE || $arquivo['size'] > 2 * 1024 * 1024) {
        return [null, 'A foto deve ter no máximo 2 MB.'];
    }
    if ($arquivo['error'] != UPLOAD_ERR_OK) {
        return [null, 'Não foi possível receber a foto.'];
    }

    // Tipo: o nome do arquivo pode mentir ("virus.php" renomeado para "foto.jpg"),
    // então o finfo abre o arquivo e descobre o tipo verdadeiro pelo conteúdo
    $tipos_aceitos = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);

    if (!isset($tipos_aceitos[$tipo])) {
        return [null, 'A foto precisa ser JPG, PNG ou WEBP.'];
    }

    // Nome novo e aleatório para o arquivo: não sobrescreve outra foto e não
    // usa o nome original, que vem do usuário
    $nome = bin2hex(random_bytes(16)) . '.' . $tipos_aceitos[$tipo];
    $caminho = 'uploads/lapiseiras/' . $nome;

    move_uploaded_file($arquivo['tmp_name'], __DIR__ . '/../' . $caminho);

    return [$caminho, ''];
}

// Apaga do disco uma foto que não é mais usada
function apagar_imagem($caminho) {
    if ($caminho) {
        // basename() pega só o nome do arquivo: garante que só apagamos
        // arquivos de dentro da pasta uploads/lapiseiras
        $arquivo = __DIR__ . '/../uploads/lapiseiras/' . basename($caminho);
        if (is_file($arquivo)) {
            unlink($arquivo);
        }
    }
}


// =====================================================================
// Carrinho
// =====================================================================
// O carrinho não fica no banco: fica na sessão, como um array
//   $_SESSION['carrinho'] = [ id da lapiseira => quantidade ]
// Ex.: [3 => 1, 5 => 2]  ->  1 unidade da lapiseira 3 e 2 da lapiseira 5

function carrinho_adicionar($id) {
    if (!isset($_SESSION['carrinho'])) {
        $_SESSION['carrinho'] = [];
    }

    if (isset($_SESSION['carrinho'][$id])) {
        $_SESSION['carrinho'][$id] = $_SESSION['carrinho'][$id] + 1;   // já tinha: soma 1
    } else {
        $_SESSION['carrinho'][$id] = 1;                               // primeira unidade
    }
}

function carrinho_remover($id) {
    unset($_SESSION['carrinho'][$id]);
}

// Quantidade total de unidades (é o número que aparece no header)
function carrinho_quantidade() {
    if (!isset($_SESSION['carrinho'])) {
        return 0;
    }
    return array_sum($_SESSION['carrinho']);
}

// Monta a lista do carrinho com os dados de cada lapiseira vindos do banco
function carrinho_itens($conexao) {
    $itens = [];

    if (!isset($_SESSION['carrinho'])) {
        return $itens;
    }

    foreach ($_SESSION['carrinho'] as $id => $quantidade) {
        $lapiseira = consultar($conexao, $id);

        // Foi excluída ou saiu da vitrine depois de entrar no carrinho: tira do carrinho
        if (!$lapiseira || !$lapiseira['ativo']) {
            carrinho_remover($id);
            continue;
        }

        $lapiseira['quantidade'] = $quantidade;
        $lapiseira['subtotal'] = $lapiseira['preco'] * $quantidade;
        $itens[] = $lapiseira;
    }

    return $itens;
}


// =====================================================================
// Usuários (login e cadastro)
// =====================================================================

function cadastrar_user($conexao, $email, $password) {
    $sql = "INSERT INTO usuarios(email, senha) VALUES(:email, :senha)";

    // A senha nunca é salva como foi digitada: o password_hash transforma
    // em um código que não dá para desfazer. No login usamos password_verify.
    $senha_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":senha", $senha_hash);

    $stmt->execute();
}

// Lista todos os usuários (para a tela de usuários do admin)
function listar_usuarios($conexao) {
    $sql = "SELECT id, email, papel FROM usuarios ORDER BY email";

    $stmt = $conexao->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Troca o papel de um usuário ('cliente' ou 'admin')
function atualizar_papel($conexao, $id, $papel) {
    $sql = "UPDATE usuarios SET papel = :papel WHERE id = :id";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":papel", $papel);
    $stmt->bindParam(":id", $id);
    $stmt->execute();
}

function consultar_user($conexao, $email) {

    $sql = "SELECT id, email, senha, papel FROM usuarios WHERE email = :email";

    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
