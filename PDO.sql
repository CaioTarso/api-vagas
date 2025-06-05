-- Remove o banco de dados existente para garantir um estado limpo (opcional, mas bom para testes repetidos)
DROP DATABASE IF EXISTS PDO; -- caso exista um banco de dados com o mesmo nome

-- Cria o banco de dados
CREATE DATABASE PDO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Seleciona o banco de dados para usar
USE PDO;

-- Criação da tabela vagas (conforme seu arquivo)
CREATE TABLE vagas (
    id VARCHAR(36) PRIMARY KEY,
    empresa VARCHAR(255) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NULL,
    localizacao VARCHAR(1) NOT NULL,
    nivel INT NOT NULL
);

-- Criação da tabela pessoas (conforme seu arquivo)
CREATE TABLE pessoas (
    id VARCHAR(36) PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    profissao VARCHAR(255) NOT NULL,
    localizacao VARCHAR(1) NOT NULL,
    nivel INT NOT NULL
);

-- Criação da tabela candidaturas (conforme seu arquivo)
CREATE TABLE candidaturas (
    id VARCHAR(36) PRIMARY KEY,
    id_vaga VARCHAR(36) NOT NULL,
    id_pessoa VARCHAR(36) NOT NULL,
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_vaga) REFERENCES vagas(id) ON DELETE CASCADE,
    FOREIGN KEY (id_pessoa) REFERENCES pessoas(id) ON DELETE CASCADE,
    UNIQUE (id_vaga, id_pessoa)
);

-- Inserindo dados de exemplo

-- 1. Vaga de Exemplo
INSERT INTO vagas (id, empresa, titulo, descricao, localizacao, nivel) VALUES
('11111111-1111-1111-1111-111111111111', 'Empresa Exemplo Teste', 'Dev Pleno para Teste de Ranking', 'Vaga específica para testar a funcionalidade de ranking da API.', 'A', 3);

-- 2. Pessoas (Candidatos) de Exemplo
INSERT INTO pessoas (id, nome, profissao, localizacao, nivel) VALUES
('aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'Carlos Andrade', 'Engenheiro de Software Pleno', 'B', 3),
('bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', 'Beatriz Pinheiro', 'Desenvolvedora Frontend Júnior', 'D', 2),
('eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee', 'Fernanda Lima', 'Analista de QA', 'F', 1);

-- 3. Candidaturas de Exemplo (ligando as pessoas à vaga de exemplo)
INSERT INTO candidaturas (id, id_vaga, id_pessoa) VALUES
('cccccccc-cccc-cccc-cccc-cccccccccccc', '11111111-1111-1111-1111-111111111111', 'aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa'),
('dddddddd-dddd-dddd-dddd-dddddddddddd', '11111111-1111-1111-1111-111111111111', 'bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb'),
('ffffffff-ffff-ffff-ffff-ffffffffffff', '11111111-1111-1111-1111-111111111111', 'eeeeeeee-eeee-eeee-eeee-eeeeeeeeeeee');

-- Mensagem final (opcional, apenas para feedback se executado em alguns clientes)
SELECT 'Banco de dados PDO e tabelas criadas com dados de exemplo.' AS Status;