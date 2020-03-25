<?php

namespace Drupal\d8_customer\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Customer entities.
 *
 * @ingroup d8_customer
 */
interface CustomerInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Customer name.
   *
   * @return string
   *   Name of the Customer.
   */
  public function getName();

  /**
   * Sets the Customer name.
   *
   * @param string $name
   *   The Customer name.
   *
   * @return \Drupal\d8_customer\Entity\CustomerInterface
   *   The called Customer entity.
   */
  public function setName($name);

  /**
   * Gets the Customer creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Customer.
   */
  public function getCreatedTime();

  /**
   * Sets the Customer creation timestamp.
   *
   * @param int $timestamp
   *   The Customer creation timestamp.
   *
   * @return \Drupal\d8_customer\Entity\CustomerInterface
   *   The called Customer entity.
   */
  public function setCreatedTime($timestamp);

}
