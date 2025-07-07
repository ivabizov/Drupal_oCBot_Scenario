<?php

namespace Drupal\ocbot_scenario\Service;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;

/**
 * Сервіс для роботи з даними сценаріїв.
 */
class ScenarioManager {

  const CACHE_PREFIX = 'ocbot_scenario:';

  protected $entityTypeManager;
  protected $cache;
  protected $logger;

  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    CacheBackendInterface $cache,
    LoggerChannelFactoryInterface $logger_factory
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->cache = $cache;
    $this->logger = $logger_factory->get('ocbot_scenario');
  }

  /**
   * Отримує дані сценарію з кешу або бази даних.
   */
  public function getScenarioData(int $scenario_id): ?array {
    $cache_id = self::CACHE_PREFIX . $scenario_id;

    if ($cached = $this->cache->get($cache_id)) {
      return $cached->data;
    }

    try {
      if (!$node = $this->loadScenario($scenario_id)) {
        throw new \InvalidArgumentException('Scenario not found');
      }

      $data = $this->buildScenarioData($node);
      $this->cache->set($cache_id, $data, time() + 3600, ['node:' . $scenario_id]);

      return $data;
    } catch (\Exception $e) {
      $this->logger->error($e->getMessage());
      return null;
    }
  }

  protected function loadScenario(int $id) {
    $node = $this->entityTypeManager->getStorage('node')->load($id);
    return $node && $node->bundle() === 'scenario' ? $node : null;
  }

  protected function buildScenarioData($node): array {
    $data = [
      'id' => $node->id(),
      'title' => $node->label(),
      'steps' => [],
    ];

    if ($node->hasField('field_scenario_steps')) {
      foreach ($node->get('field_scenario_steps')->referencedEntities() as $paragraph) {
        $data['steps'][] = $this->processParagraph($paragraph);
      }
    }

    return $data;
  }

  protected function processParagraph($paragraph): array {
    $step = [
      'name' => $paragraph->get('field_etap_name')->value ?? '',
      'processes' => [],
    ];

    if ($paragraph->hasField('field_etap_processes')) {
      foreach ($paragraph->get('field_etap_processes')->referencedEntities() as $process) {
        $step['processes'][] = $this->extractProcessData($process);
      }
    }

    return $step;
  }

  protected function extractProcessData($process): array {
    return [
      'name' => $process->label(),
      'description' => $process->get('field_process_description')->value ?? '',
    ];
  }
}