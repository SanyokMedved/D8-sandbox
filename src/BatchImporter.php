<?php

namespace Drupal\d8_customer;

use Drupal\file\Entity\File;
use Drupal\d8_customer\Entity\Customer;

/**
 * Class CSVBatchImport.
 *
 * @package Drupal\custom_csv_import
 */
class BatchImporter {

  private $batch;

  private $fid;

  private $file;

  private $skip_first_line;

  private $delimiter;

  private $enclosure;

  /**
   * {@inheritdoc}
   */
  public function __construct($fid, $skip_first_line = FALSE, $delimiter = ';', $enclosure = ',', $batch_name = 'Custom CSV import') {
    $this->fid = $fid;
    $this->file = File::load($fid);
    $this->skip_first_line = $skip_first_line;
    $this->delimiter = $delimiter;
    $this->enclosure = $enclosure;
    $this->batch = [
      'title' => $batch_name,
      'finished' => [$this, 'finished'],
      'file' => drupal_get_path('module', 'd8_customer') . '/src/BatchImporter.php',
    ];
    $this->parseCSV();
  }

  /**
   * {@inheritdoc}
   */
  public function parseCSV() {
    if (($handle = fopen($this->file->getFileUri(), 'r')) !== FALSE) {
      if ($this->skip_first_line) {
        fgetcsv($handle, 0, ';');
      }
      while (($data = fgetcsv($handle, 0, ';')) !== FALSE) {
        $this->setOperation($data);
      }
      fclose($handle);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function setOperation($data) {
    $this->batch['operations'][] = [[$this,'processItem'], $data];
    $this->processItem($data[0],$data[1],$data[2]);
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($id, $title, $balance, &$context = NULL) {
    if (!empty($id)) {
      /** @var Customer $entity */
      $entity = Customer::load($id);
    }
    if (empty($entity)) {
      /** @var Customer $entity */
      $entity = Customer::create([
        'uid' => 1,
        'status' => 1,
      ]);
    }

    $entity->set('name', $title);
    $entity->set('field_customer_id', [
      'value' => $id,
    ]);
    $entity->set('field_customer_balance', [
      'value' => $balance,
    ]);
    $entity->save();
    $context['results'][] = $entity->id() . ' : ' . $entity->label();
    $context['message'] = $entity->label();
  }

  /**
   * {@inheritdoc}
   */
  public function setBatch() {

    batch_set($this->batch);
  }

  /**
   * {@inheritdoc}
   */
  public function processBatch() {
    batch_process();
  }

  /**
   * {@inheritdoc}
   */
  public function finished($success, $results, $operations) {
    if ($success) {
      $message = \Drupal::translation()
        ->formatPlural(count($results), 'One customer processed.', '@count customers processed.');
    }
    else {
      $message = t('Finished with an error.');
    }
    \Drupal::messenger()
      ->addMessage($message);
  }

}

