<?php

namespace Drupal\d8_customer\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManager;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\d8_customer\BatchImporter;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CustomerSettingsForm.
 *
 * @ingroup d8_customer
 */

class CustomerSettingsForm extends FormBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   */
  protected $configFactory;

  /**
   * CustomerSettingsForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManager $entityTypeManager
   */
  public function __construct(EntityTypeManager $entityTypeManager, ConfigFactoryInterface $configFactory) {
    $this->entityTypeManager = $entityTypeManager;
    $this->configFactory = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('config.factory')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'customer_settings';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $fid = $form_state->getValue(['csv_file', 0]);
    if (!empty($fid)) {
      $file = $this->entityTypeManager
        ->getStorage('file')->load($fid);
      $file->setPermanent();
      $this->configFactory
        ->getEditable('d8_customer.settings')
        ->set('csv_file', $fid)
        ->save();
      $file->save();
    }
  }

  /**
   * Defines the settings form for Customer entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $saved_file = $this->configFactory->getEditable('d8_customer.settings')
      ->get('csv_file');

    if ($saved_file) {
      $file = $this->entityTypeManager
        ->getStorage('file')
        ->load($saved_file);
      if ($file) {
        $form['saved_file'] = [
          '#type' => 'markup',
          '#markup' => '<div>' . t('Saved File: ') . $file->label() .
            '<div>' . t('File was uploaded: ') . date('d-m-Y H:m:i', $file->get('created')->value) . '</div>
        </div>',
        ];
      }
    }
    $form['csv_file'] = [
      '#title' => t('CSV File'),
      '#type' => 'managed_file',
      '#description' => t('CSV File for update Customers'),
      '#default_value' => $saved_file ? [$saved_file] : '',
      '#upload_location' => 'public://csv_files/',
      '#upload_validators' => [
        'file_validate_extensions' => ['csv'],
        'file_validate_size' => [25600000],
      ],
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Upload file'),
    ];

    // This is for debug import process.
    /*$form['import'] = [
      '#type' => 'submit',
      '#value' => t('Import Data'),
      '#submit' => ['::d8_customer_import_data'],
    ];*/
    return $form;
  }

  // This is for debug import process.
  function d8_customer_import_data($form, FormStateInterface $form_state) {
    $file_value = $form_state->getValue('csv_file');
    if ($file_value) {
      $batch = new BatchImporter($file_value[0], TRUE, ';');
      $batch->setBatch();
    }
    else {
      drupal_set_message('Upload file please');
    }
  }
}

