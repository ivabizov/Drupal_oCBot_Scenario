<?php

namespace Drupal\ocbot_scenario\Install;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;

class FormDisplayInstaller {

  public static function setupFormDisplay(string $bundle = 'ocbot_scenario') {
    $display_repository = \Drupal::service('entity_display.repository');
    
    // Створюємо або завантажуємо форму
    $form_display = $display_repository->getFormDisplay('node', $bundle) ?? 
      EntityFormDisplay::create([
        'targetEntityType' => 'node',
        'bundle' => $bundle,
        'mode' => 'default',
        'status' => TRUE
      ]);

    // Жорстко задані налаштування для кожного поля
    $components = [
      'field_controller' => [
        'type' => 'options_buttons',
        'weight' => 0,
        'region' => 'content',
        'settings' => [],
      ],
      'field_scenario_description' => [
        'type' => 'text_textarea',
        'weight' => 10,
        'region' => 'content',
      ],
      'field_activity_direction' => [
        'type' => 'options_buttons',
        'weight' => 20,
      ],
      'field_equipment' => [
        'type' => 'options_buttons',
        'weight' => 30,
      ],
      'field_stages' => [
        'type' => 'paragraphs',
        'weight' => 40,
        'settings' => [
          'title' => 'Етап',
          'title_plural' => 'Етапи',
          'edit_mode' => 'closed',
          'add_mode' => 'dropdown',
          'form_display_mode' => 'default',
        ],
      ],
    ];

    foreach ($components as $field => $settings) {
      $form_display->setComponent($field, $settings);
    }

    $form_display->save();
    \Drupal::service('cache.discovery')->invalidateAll();
  }
}