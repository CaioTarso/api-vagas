<?php

namespace App\Models;

/**
 * Representa uma Pessoa (candidato).
 * Usada para transferir dados da pessoa.
 */
class Pessoa {
    public string $id;
    public string $nome;
    public string $profissao;
    public string $localizacao;
    public int $nivel; // Nível de experiência do candidato 

    public function __construct(
        string $id,
        string $nome,
        string $profissao,
        string $localizacao,
        int $nivel
    ) {
        $this->id = $id;
        $this->nome = $nome;
        $this->profissao = $profissao;
        $this->localizacao = $localizacao;
        $this->nivel = $nivel;
    }
}