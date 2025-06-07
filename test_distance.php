<?php

// Carrega o autoloader do Composer para que ele encontre sua classe
require 'vendor/autoload.php';

// Importa a sua classe para que possamos usá-la
use App\Utils\DistanceCalculator;

echo "Iniciando testes para DistanceCalculator...\n\n";

// Cria uma instância da sua classe
$calculator = new DistanceCalculator();

// --- Cenários de Teste ---

// Teste 1: Caminho direto (A -> B)
$distancia1 = $calculator->getShortestDistance('A', 'B');
echo "Distância de A para B: " . $distancia1 . " (Esperado: 5)\n";

// Teste 2: Caminho indireto (A -> D)
$distancia2 = $calculator->getShortestDistance('A', 'D');
echo "Distância de A para D: " . $distancia2 . " (Esperado: 8)\n"; // Caminho: A->B->D (5+3)

// Teste 3: Caminho mais longo (A -> F)
$distancia3 = $calculator->getShortestDistance('A', 'F');
echo "Distância de A para F: " . $distancia3 . " (Esperado: 16)\n"; // Caminho: A->B->D->F (5+3+8)

// Teste 4: Caminho com múltiplas opções (B -> E)
$distancia4 = $calculator->getShortestDistance('B', 'E');
echo "Distância de B para E: " . $distancia4 . " (Esperado: 11)\n"; // Caminho mais curto é B->C->E (7+4=11), não B->D->E (3+10=13)

// Teste 5: Mesma origem e destino (C -> C)
$distancia5 = $calculator->getShortestDistance('C', 'C');
echo "Distância de C para C: " . $distancia5 . " (Esperado: 0)\n";

echo "\nTestes finalizados.\n";