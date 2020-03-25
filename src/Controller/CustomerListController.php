<?php

namespace Drupal\d8_customer\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class CustomerListController.
 */
class CustomerListController extends ControllerBase {

  /**
   * Buildlist.
   *
   * @param string $customer_id
   *
   * @return array
   */
  public function buildList($customer_id = 'all') {
    $data = views_embed_view('customer_list', 'default', $customer_id);
    return [
      '#type' => 'markup',
      '#markup' => render($data)
    ];
  }

}
