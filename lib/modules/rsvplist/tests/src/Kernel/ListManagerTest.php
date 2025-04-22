<?php

declare(strict_types=1);

namespace Drupal\Tests\rsvplist\Kernel;

use Drupal\rsvplist\Exception\RsvpListException;
use Drupal\rsvplist\ListManager;

/**
 * @todo Add description.
 */
class ListManagerTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'rsvplist',
    'datetime_testing',
  ];

  /**
   * @todo Add description.
   */
  public function testListManager(): void {

    // Check that the list is empty.
    $list = \Drupal::database()->select('rsvplist', 'l')->fields('l')->execute()->fetchAll();
    $this->assertCount(0, $list);

    // Add element to the list.
    $list_manager = \Drupal::service('rsvplist.list_manager');
    $list_manager->add('123', '456', 'asd');

    // Check that the list contains the element.
    $list = \Drupal::database()->select('rsvplist', 'l')->fields('l')->execute()->fetchAll();
    $this->assertCount(1, $list);

    $this->assertEquals([
      'id' => '1',
      'uid' => '456',
      'nid' => '123',
      'mail' => 'asd',
      'created' => \Drupal::time()->getCurrentTime(),
      ],
      (array) $list[0]
    );
  }

  public function testDatabaseNotAvailable() {
    // @todo Test exception.
    $list = \Drupal::database()->select('rsvplist', 'l')->fields('l')->execute()->fetchAll();
    $this->assertCount(0, $list);

    $mock_discovery = $this->createMock(ListManager::class);
    $mock_discovery->method('add')
      ->willThrowException(new \Exception('asd'));
    $this->container->set(ListManager::class, $mock_discovery);

    $list_manager = \Drupal::service('rsvplist.list_manager');
    $list_manager->add('123', '456', 'asd');

    $this->expectException(RsvpListException::class);
    $this->expectExceptionMessage('Element could not be added to the list.');
  }
}
