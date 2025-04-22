<?php

declare(strict_types=1);

namespace Drupal\rsvplist;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Database\Connection;
use Drupal\rsvplist\Exception\RsvpListException;

/**
 * @todo Add class description.
 */
class ListManager implements ListManagerInterface {

  /**
   * Constructs a ListManager object.
   */
  public function __construct(
    private readonly Connection $connection,
    private readonly TimeInterface $time,
  ) {}

  /**
   * @todo Add method description and interface.
   */
  public function add(string $nid, string $uid, string $mail): void {
    try {
      $query = $this->connection->insert('rsvplist');

      // Specify the fields that the query will insert into.
      $query->fields([
        'uid',
        'nid',
        'mail',
        'created',
      ]);

      // Set the values of the fields we selected.
      // Note that they must be in the same order as we defined them
      // in the $query->fields([...]) above.
      $query->values([
        $uid,
        $nid,
        $mail,
        $this->time->getCurrentTime(),
      ]);

      $query->execute();

    } catch (\Exception $e) {
      throw new RsvpListException('Element could not be added to the list.', previous:$e);
    }
  }

}
