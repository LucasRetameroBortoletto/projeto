<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../database/connect_postgres.php';


// =====================================================================
// Constantes do catálogo
// =====================================================================

// Bitolas aceitas (a mesma lista do CHECK no banco). Ficam como texto porque
// é assim que chegam do formulário e do PostgreSQL (NUMERIC vem como "0.5").
const BITOLAS = ['0.3', '0.5', '0.7', '0.9', '1.3', '2.0'];

// Ordenações da vitrine. A chave vem da URL (?ordem=preco_asc) e o valor entra
// no SQL. Nome de coluna não pode ser parâmetro de prepared statement, então
// só aceitamos o que está nesta lista fixa: nada digitado pelo usuário chega ao SQL.
const ORDENACOES = [
    'novidades'  => ['rotulo' => 'Novidades',   'sql' => 'criado_em DESC, id DESC'],
    'preco_asc'  => ['rotulo' => 'Menor preço', 'sql' => 'preco ASC, id ASC'],
    'preco_desc' => ['rotulo' => 'Maior preço', 'sql' => 'preco DESC, id DESC'],
];

// Upload das fotos
const IMAGEM_TAMANHO_MAXIMO = 2 * 1024 * 1024; // 2 MB
const IMAGEM_TIPOS = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
const PASTA_UPLOADS = 'uploads/lapiseiras'; // relativo à raiz do projeto

// Páginas para onde um formulário pode mandar o usuário de volta (campo "voltar").
// Sem essa lista, alguém poderia usar o campo para redirecionar para outro site.
const PAGINAS_RETORNO = ['index.php', 'app/select.php', 'app/delete.php', 'carrinho/index.php'];


// =====================================================================
// Utilidades
// =====================================================================

// Escapa texto antes de colocá-lo no HTML. Todo dado vindo do banco ou do
// usuário passa por aqui: "<script>" vira "&lt;script&gt;" e é exibido como texto.
function e($valor) {
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

// Monta o endereço completo de uma página ou arquivo do projeto.
// url('app/create.php') -> '/MINI%20SISTEMA/app/create.php'
function url($caminho = '') {
    return str_replace(' ', '%20', BASE_URL . '/' . ltrim($caminho, '/'));
}

// Igual a url(), mas acrescenta ?v=<data de modificação> em CSS/JS/imagens.
// Quando o arquivo muda, o endereço muda e o navegador não usa a versão antiga do cache.
function asset($caminho) {
    $arquivo = __DIR__ . '/../' . $caminho;
    $versao = is_file($arquivo) ? filemtime($arquivo) : 0;
    return url($caminho) . '?v=' . $versao;
}

function redirecionar($caminho) {
    header('Location: ' . url($caminho));
    exit; // sem exit o PHP continuaria executando o resto da página
}

// Aceita o destino de retorno só se for uma das PAGINAS_RETORNO (com ou sem ?query).
function caminho_retorno($caminho, $padrao) {
    $partes = parse_url((string) $caminho);
    if ($partes === false || isset($partes['scheme']) || isset($partes['host'])
        || !in_array($partes['path'] ?? '', PAGINAS_RETORNO, true)) {
        return $padrao;
    }
    return $caminho;
}

// Converte o id recebido do formulário/URL em inteiro positivo, ou null se for inválido.
// Evita mandar "abc" para uma coluna inteira do PostgreSQL (o que geraria erro).
function ler_id($valor) {
    $id = filter_var($valor, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : $id;
}

function formatar_preco($valor) {
    return 'R$ ' . number_format((float) $valor, 2, ',', '.');
}

function formatar_bitola($bitola) {
    return number_format((float) $bitola, 1, '.', '') . ' mm';
}

// Foto do produto ou a imagem padrão, quando não há foto cadastrada.
function imagem_lapiseira($lapiseira) {
    return $lapiseira['imagem'] ? url($lapiseira['imagem']) : asset('assets/img/produto-sem-foto.svg');
}


// =====================================================================
// Avisos (mensagens que sobrevivem a um redirecionamento)
// =====================================================================
// Depois de salvar algo, a página redireciona (para o F5 não reenviar o formulário).
// A mensagem de sucesso fica guardada na sessão e é exibida uma única vez
// pela próxima página, através de includes/aviso.php.

function definir_aviso($tipo, $texto) {
    $_SESSION['aviso'] = ['tipo' => $tipo, 'texto' => $texto];
}

function pegar_aviso() {
    $aviso = $_SESSION['aviso'] ?? null;
    unset($_SESSION['aviso']);
    return $aviso;
}


// =====================================================================
// Sessão do usuário
// =====================================================================

function usuario_logado() {
    return isset($_SESSION['id']);
}

function usuario_admin() {
    return ($_SESSION['papel'] ?? '') === 'admin';
}


// =====================================================================
// Lapiseiras (CRUD)
// =====================================================================

function cadastrar($conexao, $modelo, $marca, $bitola, $preco, $imagem, $ativo) {
    $sql = "INSERT INTO lapiseiras (modelo, marca, bitola, preco, imagem, ativo)
            VALUES (:modelo, :marca, :bitola, :preco, :imagem, :ativo)";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(":modelo", $modelo);
    $stmt->bindValue(":marca", $marca);
    $stmt->bindValue(":bitola", $bitola);
    $stmt->bindValue(":preco", $preco);
    $stmt->bindValue(":imagem", $imagem, $imagem === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(":ativo", $ativo ? 'true' : 'false');

    $stmt->execute();
}

// Devolve true se alguma linha foi apagada.
function deletar($conexao, $id) {
    $sql = "DELETE FROM lapiseiras WHERE id = :id";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->rowCount() > 0;
}

// Devolve a lapiseira como array, ou false se o id não existir.
function consultar($conexao, $id) {
    $sql = "SELECT * FROM lapiseiras WHERE id = :id";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch();
}

// Lista as lapiseiras. Usada pela vitrine (com filtro e ordenação) e pelo relatório do admin.
//   $bitola: uma das BITOLAS, ou '' para todas
//   $ordem: uma das chaves de ORDENACOES
//   $incluir_inativas: true só para o admin, que precisa ver as que estão fora da vitrine
function relatorio($conexao, $bitola = '', $ordem = 'novidades', $incluir_inativas = false) {
    $condicoes = [];
    $parametros = [];

    if (!$incluir_inativas) {
        $condicoes[] = "ativo = true";
    }
    if (in_array($bitola, BITOLAS, true)) {
        $condicoes[] = "bitola = :bitola";
        $parametros[':bitola'] = $bitola;
    }

    $sql = "SELECT * FROM lapiseiras";
    if ($condicoes) {
        $sql .= " WHERE " . implode(" AND ", $condicoes);
    }
    $sql .= " ORDER BY " . (ORDENACOES[$ordem] ?? ORDENACOES['novidades'])['sql'];

    //stmt é uma variável, função do própio PDO
    $stmt = $conexao->prepare($sql);
    $stmt->execute($parametros);

    return $stmt->fetchAll();
}

function atualizar($conexao, $id, $modelo, $marca, $bitola, $preco, $imagem, $ativo) {

    $sql = "UPDATE lapiseiras
            SET modelo = :modelo, marca = :marca, bitola = :bitola, preco = :preco, imagem = :imagem, ativo = :ativo
            WHERE id = :id";

    $stmt = $conexao->prepare($sql);

    $stmt->bindValue(":id", $id, PDO::PARAM_INT);
    $stmt->bindValue(":modelo", $modelo);
    $stmt->bindValue(":marca", $marca);
    $stmt->bindValue(":bitola", $bitola);
    $stmt->bindValue(":preco", $preco);
    $stmt->bindValue(":imagem", $imagem, $imagem === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
    $stmt->bindValue(":ativo", $ativo ? 'true' : 'false');

    $stmt->execute();
}

// Confere os campos do formulário de lapiseira (cadastro e edição).
// Devolve [dados limpos, erros]; erros é um array campo => mensagem (vazio = tudo certo).
function validar_lapiseira($entrada) {
    $dados = [
        'modelo' => trim($entrada['modelo'] ?? ''),
        'marca'  => trim($entrada['marca'] ?? ''),
        'bitola' => $entrada['bitola'] ?? '',
        // aceita "129,90" e "129.90"
        'preco'  => str_replace(',', '.', trim($entrada['preco'] ?? '')),
        'ativo'  => $entrada['ativo'] ?? '',
    ];
    $erros = [];

    if ($dados['modelo'] === '' || mb_strlen($dados['modelo']) > 120) {
        $erros['modelo'] = 'Informe o modelo (até 120 caracteres).';
    }
    if ($dados['marca'] === '' || mb_strlen($dados['marca']) > 60) {
        $erros['marca'] = 'Informe a marca (até 60 caracteres).';
    }
    if (!in_array($dados['bitola'], BITOLAS, true)) {
        $erros['bitola'] = 'Escolha uma bitola da lista.';
    }
    if (!is_numeric($dados['preco']) || $dados['preco'] < 0 || $dados['preco'] >= 100000000) {
        $erros['preco'] = 'Informe um preço válido.';
    }
    if (!in_array($dados['ativo'], ['true', 'false'], true)) {
        $erros['ativo'] = 'Escolha se a lapiseira aparece na vitrine.';
    }

    return [$dados, $erros];
}


// =====================================================================
// Upload de fotos
// =====================================================================

// Valida e salva a foto enviada pelo formulário.
// Devolve o caminho salvo (ex.: "uploads/lapiseiras/3f9c...jpg"), null se nenhum
// arquivo foi enviado, ou lança RuntimeException com a mensagem para o usuário.
function salvar_imagem($arquivo) {
    if (!$arquivo || $arquivo['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }
    // INI_SIZE: maior que o upload_max_filesize do php.ini
    if (in_array($arquivo['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)
        || $arquivo['size'] > IMAGEM_TAMANHO_MAXIMO) {
        throw new RuntimeException('A foto deve ter no máximo 2 MB.');
    }
    if ($arquivo['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($arquivo['tmp_name'])) {
        throw new RuntimeException('Não foi possível receber a foto. Tente de novo.');
    }

    // O tipo que o navegador informa ($arquivo['type']) pode ser falsificado.
    // O finfo lê os primeiros bytes do próprio arquivo para descobrir o tipo real.
    $tipo = (new finfo(FILEINFO_MIME_TYPE))->file($arquivo['tmp_name']);
    if (!isset(IMAGEM_TIPOS[$tipo])) {
        throw new RuntimeException('A foto precisa ser JPG, PNG ou WEBP.');
    }

    // Nome aleatório: não sobrescreve outra foto e não usa o nome original,
    // que vem do usuário e poderia conter caracteres perigosos.
    $nome = bin2hex(random_bytes(16)) . '.' . IMAGEM_TIPOS[$tipo];
    $pasta = __DIR__ . '/../' . PASTA_UPLOADS;

    if (!is_dir($pasta) && !mkdir($pasta, 0755, true)) {
        throw new RuntimeException('A pasta de fotos não existe e não pôde ser criada.');
    }
    if (!move_uploaded_file($arquivo['tmp_name'], $pasta . '/' . $nome)) {
        throw new RuntimeException('Não foi possível salvar a foto no servidor.');
    }

    return PASTA_UPLOADS . '/' . $nome;
}

function apagar_imagem($caminho) {
    if (!$caminho) {
        return;
    }
    // basename() descarta qualquer pasta do caminho: só apagamos dentro de uploads/lapiseiras
    $arquivo = __DIR__ . '/../' . PASTA_UPLOADS . '/' . basename($caminho);
    if (is_file($arquivo)) {
        unlink($arquivo);
    }
}


// =====================================================================
// Carrinho (guardado na sessão: $_SESSION['carrinho'][id da lapiseira] = quantidade)
// =====================================================================

function carrinho_adicionar($id) {
    $_SESSION['carrinho'][$id] = ($_SESSION['carrinho'][$id] ?? 0) + 1;
}

function carrinho_remover($id) {
    unset($_SESSION['carrinho'][$id]);
}

// Total de unidades no carrinho (usado no contador do header)
function carrinho_quantidade() {
    return array_sum($_SESSION['carrinho'] ?? []);
}

// Busca no banco os dados das lapiseiras do carrinho, com quantidade e subtotal.
function carrinho_itens($conexao) {
    $carrinho = $_SESSION['carrinho'] ?? [];
    if (!$carrinho) {
        return [];
    }

    // Um "?" para cada id: WHERE id IN (?, ?, ?). Continua sendo prepared statement.
    $marcadores = implode(', ', array_fill(0, count($carrinho), '?'));
    $stmt = $conexao->prepare("SELECT * FROM lapiseiras WHERE id IN ($marcadores) AND ativo = true ORDER BY modelo");
    $stmt->execute(array_keys($carrinho));

    $itens = [];
    foreach ($stmt->fetchAll() as $lapiseira) {
        $lapiseira['quantidade'] = $carrinho[$lapiseira['id']];
        $lapiseira['subtotal'] = $lapiseira['preco'] * $lapiseira['quantidade'];
        $itens[$lapiseira['id']] = $lapiseira;
    }

    // Tira do carrinho o que foi excluído ou saiu da vitrine desde que foi adicionado
    $_SESSION['carrinho'] = array_intersect_key($carrinho, $itens);

    return array_values($itens);
}


// =====================================================================
// Usuários (login e cadastro)
// =====================================================================

function cadastrar_user($conexao, $email, $password) {
    $sql = "INSERT INTO usuarios(email, senha) VALUES(:email, :senha)";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(":email", $email);
    // password_hash gera um hash com "sal" aleatório: a senha nunca fica salva em texto puro
    $stmt->bindValue(":senha", password_hash($password, PASSWORD_DEFAULT));

    $stmt->execute();
}

function consultar_user($conexao, $email) {

    $sql = "SELECT id, email, senha, papel FROM usuarios WHERE email = :email";

    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(":email", $email);
    $stmt->execute();

    return $stmt->fetch();
}
?>
