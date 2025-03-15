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
    $this->drupalCreateNode();
    $this->drupalGet('/rsvplist/1/add');
    $this->assertSession()->pageTextContains('Access Denied');
    $this->assertSession()->statusCodeEquals(403);

    $this->drupalLogin($this->createUser(['view rsvplist']));
    $this->drupalGet('/rsvplist/1/add');
    $this->assertSession()->pageTextContains('RSVP to this Event (Add)');
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * Tests module RSVP form.
   *
   * @return void
   */
  public function testForm(): void {
    $this->drupalCreateNode();
    $this->drupalLogin($this->createUser(['view rsvplist']));
    $this->drupalGet('/rsvplist/1/add');
    $assert_session = $this->assertSession();

    $assert_session->pageTextContains('RSVP to this Event (Add)');
    $assert_session->fieldExists('Email address')->setValue('example@mail.com');
    $assert_session->buttonExists('RSVP')->submit();
    $assert_session->statusMessageContains('Thank you for your RSVP, you are on the list for the event!', 'status');


    $assert_session->pageTextContains('RSVP to this Event (Add)');
    $assert_session->fieldExists('Email address')->setValue('example');
    $assert_session->buttonExists('RSVP')->submit();
    $assert_session->statusMessageContains('It appears that example is not a valid email. Please try again', 'error');
  }

}
