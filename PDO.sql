CREATE DATABASE PDO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE PDO;

CREATE TABLE vagas (
    id VARCHAR(36) PRIMARY KEY,       -- UUID como chave primária
    empresa VARCHAR(255) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NULL,              -- Pode ser nulo, pois é opcional
    localizacao VARCHAR(1) NOT NULL,  -- 'A', 'B', 'C', etc.
    nivel INT NOT NULL                -- 1 a 5
);

CREATE TABLE pessoas (
    id VARCHAR(36) PRIMARY KEY,       -- UUID como chave primária
    nome VARCHAR(255) NOT NULL,
    profissao VARCHAR(255) NOT NULL,
    localizacao VARCHAR(1) NOT NULL,  -- 'A', 'B', 'C', etc.
    nivel INT NOT NULL                -- 1 a 5
);

CREATE TABLE candidaturas (
    id VARCHAR(36) PRIMARY KEY,        -- UUID da candidatura
    id_vaga VARCHAR(36) NOT NULL,
    id_pessoa VARCHAR(36) NOT NULL,
    data_candidatura TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Opcional: para saber quando foi a candidatura

    FOREIGN KEY (id_vaga) REFERENCES vagas(id) ON DELETE CASCADE, -- Se uma vaga for deletada, suas candidaturas são removidas
    FOREIGN KEY (id_pessoa) REFERENCES pessoas(id) ON DELETE CASCADE, -- Se uma pessoa for deletada, suas candidaturas são removidas
    UNIQUE (id_vaga, id_pessoa) -- Garante que uma pessoa não se candidate à mesma vaga múltiplas vezes
                                 -- O PDF diz "NÃO DEVE ter duplicidade nas candidaturas"
);


