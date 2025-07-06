<?php

namespace Drupal\ocbot_scenario\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;

class ScenarioController extends ControllerBase {

  public function overview() {
    $build = [];
    
    // Отримуємо активні сценарії
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'scenario')
      ->condition('field_scenario_active', TRUE)
      ->sort('created', 'DESC');
    $nids = $query->execute();

    $scenarios = Node::loadMultiple($nids);

    foreach ($scenarios as $scenario) {
      $build[] = [
        '#theme' => 'scenario_item',
        '#scenario' => $scenario,
      ];
    }

    return $build;
  }
}