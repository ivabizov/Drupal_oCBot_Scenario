public static function install() {
  $fields = [
    'field_ocbot_scenario_description' => [
      'type' => 'text_long',
      'label' => 'Опис сценарію',
      'form_display' => [ // Додаємо конфіг для форми
        'type' => 'text_textarea',
        'weight' => 10,
        'settings' => [
          'rows' => 5,
          'placeholder' => 'Введіть опис'
        ]
      ]
    ]
  ];

  foreach ($fields as $name => $config) {
    // Створення схеми поля
    if (!FieldStorageConfig::loadByName('node', $name)) {
      FieldStorageConfig::create([
        'field_name' => $name,
        'entity_type' => 'node',
        'type' => $config['type'],
      ])->save();
    }

    // Прив'язка поля до типу контенту
    if (!FieldConfig::loadByName('node', 'ocbot_scenario', $name)) {
      FieldConfig::create([
        'field_name' => $name,
        'entity_type' => 'node',
        'bundle' => 'ocbot_scenario',
        'label' => $config['label'],
      ])->save();
    }
  }
}