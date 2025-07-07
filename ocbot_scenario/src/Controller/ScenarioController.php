<?php

namespace Drupal\ocbot_scenario\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller for scenario management.
 */
class ScenarioController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new ScenarioController.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Displays list of scenarios.
   */
  public function list() {
    $nodes = $this->entityTypeManager->getStorage('node')
      ->loadByProperties(['type' => 'scenario']);

    $items = [];
    foreach ($nodes as $node) {
      $items[] = $node->toLink();
    }

    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#title' => $this->t('Scenarios'),
    ];
  }

}