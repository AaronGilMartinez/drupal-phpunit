<?php

declare(strict_types=1);

namespace Drupal\rsvplist;

/**
 * @todo Add class description.
 */
interface ListManagerInterface {

  /**
   * Undocumented function
   *
   * @param string $nid
   * @param string $uid
   * @param string $mail
   * @return void
   */
  public function add(string $nid, string $uid, string $mail): void;
}
