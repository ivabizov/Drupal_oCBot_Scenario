<?php

namespace Drupal\ocbot_scenario\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Scenario settings form.
 */
class ScenarioSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ocbot_scenario_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ocbot_scenario.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('ocbot_scenario.settings');

    $form['default_scenario'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Default scenario'),
      '#target_type' => 'node',
      '#selection_settings' => [
        'target_bundles' => ['scenario'],
      ],
      '#default_value' => $config->get('default_scenario'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ocbot_scenario.settings')
      ->set('default_scenario', $form_state->getValue('default_scenario'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}