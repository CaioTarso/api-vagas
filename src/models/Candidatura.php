<?php

namespace App\Models;

/**
 * Representa uma Candidatura.
 * Usada para transferir dados da candidatura.
 */
class Candidatura {
    public string $id;
    public string $id_vaga;
    public string $id_pessoa;

    public function __construct(string $id, string $id_vaga, string $id_pessoa) {
        $this->id = $id;
        $this->id_vaga = $id_vaga;
        $this->id_pessoa = $id_pessoa;
    }
}