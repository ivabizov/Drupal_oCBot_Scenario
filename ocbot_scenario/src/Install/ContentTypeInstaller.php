<?php

namespace Drupal\ocbot_scenario\Install;

class ContentTypeInstaller {
  public static function install() {
    $types = [
      'ocbot_scenario' => [
        'name' => 'oCBot - Сценарій',
        'description' => 'Тип контенту для сценаріїв oCBot'
      ],
      'ocbot_controller' => [
        'name' => 'oCBot - Контроллери',
        'description' => 'Тип контенту для контролерів oCBot'
      ]
    ];

    foreach ($types as $id => $type) {
      if (!\Drupal::entityTypeManager()->getStorage('node_type')->load($id)) {
        \Drupal::entityTypeManager()
          ->getStorage('node_type')
          ->create($type + ['type' => $id])
          ->save();
      }
    }
  }

  public static function uninstall() {
    foreach (['ocbot_scenario', 'ocbot_controller'] as $type) {
      if ($entity = \Drupal::entityTypeManager()->getStorage('node_type')->load($type)) {
        $entity->delete();
      }
    }
  }
}