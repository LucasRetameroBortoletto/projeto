-- =============================================================
-- Lapisari: estrutura do banco (PostgreSQL)
--
-- Este arquivo é só referência: o código NÃO executa este script.
-- Rode-o manualmente NO MESMO BANCO configurado em
-- database/connect_postgres.php ($dbname).
--
-- Pode rodar quantas vezes quiser: cada comando só cria/altera
-- o que ainda não existe (IF NOT EXISTS), sem apagar dados.
-- =============================================================


-- -------------------------------------------------------------
-- 1) Usuários
--
-- senha: 255 caracteres porque o password_hash() gera um texto
--        de ~60 caracteres hoje e pode crescer com algoritmos novos.
-- papel: 'cliente' (padrão) ou 'admin'. Só admin vê e usa os
--        controles de cadastrar/editar/excluir lapiseiras.
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id    SERIAL PRIMARY KEY,
    email VARCHAR(120) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL
);

-- Se a tabela já existia na versão antiga (senha com 12 caracteres, sem papel),
-- estas duas linhas a atualizam. Se ela acabou de ser criada, não mudam nada.
ALTER TABLE usuarios ALTER COLUMN senha TYPE VARCHAR(255);
ALTER TABLE usuarios ADD COLUMN IF NOT EXISTS papel VARCHAR(10) NOT NULL DEFAULT 'cliente'
    CHECK (papel IN ('cliente', 'admin'));


-- -------------------------------------------------------------
-- 2) Lapiseiras (substitui a antiga tabela alunos1)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lapiseiras (
    id        SERIAL PRIMARY KEY,
    modelo    VARCHAR(120)  NOT NULL,
    marca     VARCHAR(60)   NOT NULL,
    bitola    NUMERIC(2,1)  NOT NULL CHECK (bitola IN (0.3, 0.5, 0.7, 0.9, 1.3, 2.0)),
    preco     NUMERIC(10,2) NOT NULL CHECK (preco >= 0),
    imagem    VARCHAR(255),                         -- caminho em uploads/lapiseiras/; vazio = imagem padrão
    ativo     BOOLEAN       NOT NULL DEFAULT true,  -- false = some da vitrine para clientes
    criado_em TIMESTAMP     NOT NULL DEFAULT now()  -- usado na ordenação "Novidades"
);


-- -------------------------------------------------------------
-- 3) Dados de exemplo (opcional, preços fictícios)
--    Só entram se a tabela de lapiseiras estiver vazia.
-- -------------------------------------------------------------
INSERT INTO lapiseiras (modelo, marca, bitola, preco)
SELECT * FROM (VALUES
    ('800',                'Rotring',   0.5, 389.90),
    ('Orenz Nero',         'Pentel',    0.3, 259.00),
    ('Graph 1000',         'Pentel',    0.5, 129.90),
    ('Kuru Toga Advance',  'Uni',       0.7, 119.00),
    ('Mars Technico 780C', 'Staedtler', 2.0,  89.90)
) AS exemplos
WHERE NOT EXISTS (SELECT 1 FROM lapiseiras);


-- -------------------------------------------------------------
-- 4) Tornar um usuário administrador
--    Cadastre a conta pelo site e depois rode:
-- -------------------------------------------------------------
-- UPDATE usuarios SET papel = 'admin' WHERE email = 'seu@email.com';
