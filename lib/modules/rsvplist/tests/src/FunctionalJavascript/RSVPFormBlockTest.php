<?php

namespace Drupal\rsvplist\tests\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

class RSVPFormBlockTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'rsvplist',
    'block'
  ];

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Tests block with AJAX link to form.
   *
   * @return void
   */
  public function testForm(): void {
    // Enable page title to check current node page title.
    $this->drupalPlaceBlock('page_title_block');
    $this->drupalPlaceBlock('rsvplist_rsvp_form');

    $this->createContentType([
      'type' => 'page',
      'name' => 'Page',
    ]);
    $node = $this->createNode(['type' => 'page']);

    $this->drupalLogin($this->createUser(['view rsvplist']));
    /** @var \Drupal\FunctionalJavascriptTests\WebDriverWebAssert $assert_session */
    $assert_session = $this->assertSession();

    $this->drupalGet($node->toUrl());
    $assert_session->pageTextContains($node->getTitle());

    // Click link to open the form and test submission.
    $this->clickLink('Add node to RSVP list.');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->pageTextContains('RSVP to this Event (Add)');
    $assert_session->fieldExists('Email address')->setValue('example@mail.com');
    $assert_session->fieldExists('Node')->setValue("{$node->label()} ({$node->id()})");
    $assert_session->buttonExists('RSVP')->submit();
    $assert_session->statusMessageContains('Thank you for your RSVP, you are on the list for the event!', 'status');
    // @todo check redirection.

    // Test form validation.
    $this->drupalGet($node->toUrl());
    $this->clickLink('Add node to RSVP list.');
    $assert_session->assertWaitOnAjaxRequest();
    $assert_session->pageTextContains('RSVP to this Event (Add)');
    $assert_session->fieldExists('Email address')->setValue('example');
    $assert_session->buttonExists('RSVP')->submit();
    $assert_session->statusMessageContains('It appears that example is not a valid email. Please try again', 'error');
  }

}
