<?php

namespace Drupal\ocbot_scenario\Install;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;

/**
 * Installs and configures form displays for the ocbot_scenario content type.
 *
 * This class handles the setup of form displays for the ocbot_scenario content
 * type, ensuring all fields are properly displayed in the node form.
 */
class FormDisplayInstaller {

  /**
   * List of fields to exclude from form display.
   */
  const EXCLUDED_FIELDS = [
    'revision_log',
    'path',
    'uid',
    'status',
    'created',
    'changed',
  ];

  /**
   * Sets up the form display for the ocbot_scenario content type.
   *
   * @param string $bundle
   *   The bundle (content type) to configure. Defaults to 'ocbot_scenario'.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   *   If the form display cannot be saved.
   */
  public static function setupFormDisplay(string $bundle = 'ocbot_scenario') {
    $entity_type = 'node';
    $form_display_id = "$entity_type.$bundle.default";

    $form_display = EntityFormDisplay::load($form_display_id);
    if (!$form_display) {
      $form_display = EntityFormDisplay::create([
        'targetEntityType' => $entity_type,
        'bundle' => $bundle,
        'mode' => 'default',
        'status' => TRUE,
      ]);
    }

    $field_configs = FieldConfig::loadByBundle($entity_type, $bundle);

    $weight = 0;
    foreach ($field_configs as $field_name => $field_config) {
      if (in_array($field_name, self::EXCLUDED_FIELDS)) {
        continue;
      }

      if ($form_display->getComponent($field_name)) {
        continue;
      }

      $widget_type = self::getDefaultWidget($field_config->getType());

      $form_display->setComponent($field_name, [
        'type' => $widget_type,
        'weight' => $weight,
        'settings' => [],
      ]);
      $weight += 10;
    }

    $form_display->save();
  }

  /**
   * Returns the default widget for a given field type.
   *
   * @param string $field_type
   *   The machine name of the field type.
   *
   * @return string
   *   The machine name of the default widget for this field type.
   */
  protected static function getDefaultWidget(string $field_type): string {
    $map = [
      'text' => 'text_textfield',
      'text_long' => 'text_textarea',
      'text_with_summary' => 'text_textarea_with_summary',
      'entity_reference' => 'entity_reference_autocomplete',
      'entity_reference_revisions' => 'paragraphs',
      'image' => 'image_image',
      'boolean' => 'boolean_checkbox',
      'list_string' => 'options_select',
      'list_integer' => 'options_select',
      'integer' => 'number',
      'decimal' => 'number',
      'datetime' => 'datetime_default',
      'email' => 'email_default',
      'link' => 'link_default',
    ];

    return $map[$field_type] ?? 'string_textfield';
  }

}