<?php

namespace Drupal\rsvplist\tests\Functional;

use Drupal\Tests\BrowserTestBase;

class RSVPFormTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'rsvplist'
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests module RSVP form access.
   *
   * @return void
   */
  public function testAccess(): void {
    $this->drupalGet('/rsvplist/add');

    $this->assertSession()->pageTextContains('Access Denied');
    $this->assertSession()->statusCodeEquals(403);

    $this->drupalLogin($this->createUser(['view rsvplist']));
    $this->drupalGet('/rsvplist/add');
    $this->assertSession()->pageTextContains('RSVP to this Event (Add)');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests module RSVP form.
   *
   * @return void
   */
  public function testForm(): void {
    $node = $this->drupalCreateNode();
    $user = $this->createUser(['view rsvplist']);
    $this->drupalLogin($user);
    $this->drupalGet('/node/1');

    $this->drupalGet('/rsvplist/add');
    $assert_session = $this->assertSession();

    $assert_session->pageTextContains('RSVP to this Event (Add)');
    $assert_session->fieldExists('Email address')->setValue('example@mail.com');
    $assert_session->fieldExists('Node')->setValue("{$node->label()} ({$node->id()})");
    $assert_session->buttonExists('RSVP')->submit();
    $assert_session->statusMessageContains('Thank you for your RSVP, you are on the list for the event!', 'status');

    // Check saved data.
    $list = \Drupal::database()
      ->select('rsvplist', 'l')
      ->fields('l')
      ->execute()
      ->fetchAll();
    $this->assertCount(1, $list);
    // @todo Add check for created timestamp.
    array_walk($list, fn($list_element) => $list_element->created = NULL);
    $this->assertEquals([
      'id' => '1',
      'uid' => $user->id(),
      'nid' => $node->id(),
      'mail' => 'example@mail.com',
      'created' => NULL
    ], (array) $list[0]);

    // Test form validation.
    $this->drupalGet('/rsvplist/add');
    $assert_session->pageTextContains('RSVP to this Event (Add)');
    $assert_session->fieldExists('Email address')->setValue('example');
    $assert_session->fieldExists('Node')->setValue("{$node->label()} ({$node->id()})");
    $assert_session->buttonExists('RSVP')->submit();
    $assert_session->statusMessageContains('It appears that example is not a valid email. Please try again', 'error');

    // No data has been added.
    $list = \Drupal::database()
      ->select('rsvplist', 'l')
      ->fields('l')
      ->execute()
      ->fetchAll();
    $this->assertCount(1, $list);
  }

}
