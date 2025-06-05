<?php

namespace App\Models;

/**
 * Representa uma Vaga.
 * Usada para transferir dados da vaga.
 */
class Vaga {
    public string $id;
    public string $empresa;
    public string $titulo;
    public ?string $descricao; // Descrição é opcional 
    public string $localizacao;
    public int $nivel;

    public function __construct(
        string $id,
        string $empresa,
        string $titulo,
        string $localizacao,
        int $nivel,
        ?string $descricao = null
    ) {
        $this->id = $id;
        $this->empresa = $empresa;
        $this->titulo = $titulo;
        $this->localizacao = $localizacao;
        $this->nivel = $nivel;
        $this->descricao = $descricao;
    }
}