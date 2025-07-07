<?php

namespace Drupal\ocbot_scenario\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Manages scenario operations.
 */
class ScenarioManager {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new ScenarioManager.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Gets structured scenario data.
   */
  public function getScenarioData($scenario_id) {
    $node = $this->entityTypeManager->getStorage('node')->load($scenario_id);
    
    if (!$node || $node->bundle() !== 'scenario') {
      return NULL;
    }

    $data = [
      'title' => $node->label(),
      'steps' => [],
    ];

    if ($node->hasField('field_scenario_steps')) {
      foreach ($node->get('field_scenario_steps')->referencedEntities() as $paragraph) {
        $step = [
          'name' => $paragraph->get('field_etap_name')->value,
          'processes' => [],
        ];
        
        if ($paragraph->hasField('field_etap_processes')) {
          foreach ($paragraph->get('field_etap_processes')->referencedEntities() as $process) {
            $step['processes'][] = $this->extractProcessData($process);
          }
        }
        
        $data['steps'][] = $step;
      }
    }

    return $data;
  }

  /**
   * Extracts process data from paragraph.
   */
  protected function extractProcessData($process) {
    // Implement process data extraction
    return ['name' => $process->label()];
  }

}