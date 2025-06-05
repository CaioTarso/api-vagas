<?php 

namespace App\Utils; 

  class DistanceCalculator {
    private $graph = [
      'A' => ['B' => 5],
      'B' => ['A' => 5, 'C' => 7, 'D' => 3],
      'C' => ['A' => 7, 'E'=> 4],
      'D' => ['B' => 3, 'E' => 10, 'F'=> 8],
      'E'=> ['C'=> 4, 'D'=> 10],
      'F'=> ['D'=> 8,]
  ];
  
  public function getShortestDistance(string $startNode, string $endNode): ?int {
      $distances = [];
      $previousNodes = [];
      $pq = new \SplPriorityQueue();

      foreach (array_keys($this->graph) as $node) {
          $distances[$node] = INF;
          $previousNodes[$node] = null;
      }
      $distances[$startNode] = 0;
        $pq->insert($startNode, 0); 

        while (!$pq->isEmpty()) {
            $currentNode = $pq->extract();

            if ($currentNode === $endNode) {
                
                return $distances[$endNode] === INF ? null : $distances[$endNode];
            }

            if (empty($this->graph[$currentNode])) continue; 

            foreach ($this->graph[$currentNode] as $neighbor => $weight) {
                $altDistance = $distances[$currentNode] + $weight;
                if ($altDistance < $distances[$neighbor]) {
                    $distances[$neighbor] = $altDistance;
                    $previousNodes[$neighbor] = $currentNode;

                    $pq->insert($neighbor, -$altDistance);
                }
            }
        }
        return $distances[$endNode] === INF ? null : $distances[$endNode]; 
    }
}