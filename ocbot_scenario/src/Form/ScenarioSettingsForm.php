<?php

namespace Drupal\ocbot_scenario\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Форма налаштувань сценаріїв.
 */
class ScenarioSettingsForm extends ConfigFormBase {

  protected $entityTypeManager;

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('entity_type.manager')
    );
  }

  public function __construct(
    $config_factory,
    EntityTypeManagerInterface $entity_type_manager
  ) {
    parent::__construct($config_factory);
    $this->entityTypeManager = $entity_type_manager;
  }

  public function getFormId() {
    return 'ocbot_scenario_settings';
  }

  protected function getEditableConfigNames() {
    return ['ocbot_scenario.settings'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ocbot_scenario.settings');

    $form['default_scenario'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Сценарій за замовчуванням'),
      '#target_type' => 'node',
      '#selection_settings' => [
        'target_bundles' => ['scenario'],
      ],
      '#default_value' => $config->get('default_scenario') ? 
        $this->entityTypeManager->getStorage('node')->load($config->get('default_scenario')) : 
        null,
      '#required' => false,
    ];

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($id = $form_state->getValue('default_scenario')) {
      if (!$this->entityTypeManager->getStorage('node')->load($id)) {
        $form_state->setErrorByName(
          'default_scenario',
          $this->t('Обраний сценарій не існує')
        );
      }
    }
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ocbot_scenario.settings')
      ->set('default_scenario', $form_state->getValue('default_scenario'))
      ->save();

    parent::submitForm($form, $form_state);
  }
}