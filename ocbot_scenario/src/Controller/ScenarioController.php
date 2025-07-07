<?php

namespace Drupal\ocbot_scenario\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Pager\PagerManagerInterface;

/**
 * Контролер для управління сценаріями.
 */
class ScenarioController extends ControllerBase {

  protected $entityTypeManager;
  protected $pagerManager;

  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    PagerManagerInterface $pager_manager
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->pagerManager = $pager_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('pager.manager')
    );
  }

  /**
   * Виводить пагінований список сценаріїв.
   */
  public function list() {
    $query = $this->entityTypeManager->getStorage('node')
      ->getQuery()
      ->condition('type', 'scenario')
      ->sort('created', 'DESC');

    $count = clone $query;
    $this->pagerManager->createPager($count->count()->execute(), 10);

    $nids = $query->range(0, 10)->execute();
    $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);

    $items = array_map(function($node) {
      return $node->toLink();
    }, $nodes);

    return [
      '#theme' => 'item_list',
      '#items' => $items,
      '#title' => $this->t('Сценарії'),
      'pager' => [
        '#type' => 'pager',
      ],
    ];
  }
}