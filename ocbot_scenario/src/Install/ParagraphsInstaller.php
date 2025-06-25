<?php

namespace Drupal\ocbot_scenario\Install;

class ParagraphsInstaller {
  public static function install() {
    $paragraphs = [
      'ocbot_stage' => [
        'label' => 'Етап',
        'description' => 'Етап сценарію'
      ]
    ];

    foreach ($paragraphs as $id => $data) {
      if (!\Drupal::entityTypeManager()->getStorage('paragraphs_type')->load($id)) {
        \Drupal::entityTypeManager()
          ->getStorage('paragraphs_type')
          ->create($data + ['id' => $id])
          ->save();
      }
    }
  }

  public static function uninstall() {
    foreach (['ocbot_stage'] as $type) {
      if ($entity = \Drupal::entityTypeManager()->getStorage('paragraphs_type')->load($type)) {
        $entity->delete();
      }
    }
  }
}