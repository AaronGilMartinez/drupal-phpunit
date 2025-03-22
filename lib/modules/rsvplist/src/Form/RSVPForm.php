<?php

/**
 * @file
 * A form to collect an email address for RSVP details.
 */

namespace Drupal\rsvplist\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxFormHelperTrait;
use Drupal\Core\Ajax\AjaxHelperTrait;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\RedirectCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

class RSVPForm extends FormBase {

  use AjaxHelperTrait;
  use AjaxFormHelperTrait;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'rsvplist_email_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?NodeInterface $node = NULL) {
    $form['email'] = [
      '#type' => 'textfield',
      '#title' => t('Email address'),
      '#size' => 25,
      '#description' => t("We will send updates to the email address you provide."),
      '#required' => TRUE,
    ];

    $form['nid'] = [
      '#type' => 'entity_autocomplete',
      '#target_type' => 'node',
      '#title' => t('Node'),
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('RSVP'),
    ];

    // Module library.
    $form['#attached']['library'][] = 'core/drupal.ajax';

    if ($this->isAjax()) {
      // Due to https://www.drupal.org/node/2897377 we have to declare a fixed
      // ID for the form.
      // Since only one modal can be opened at the time, we can rely on the
      // form ID as HTML ID.
      // @todo Remove this workaround once https://www.drupal.org/node/2897377
      //   is fixed.
      $form['#id'] = Html::getId($form_state->getBuildInfo()['form_id']);
      $form['submit']['#ajax'] = ['callback' => '::ajaxSubmit'];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $value = $form_state->getValue('email');
    if ( !(\Drupal::service('email.validator')->isValid($value)) ) {
      $form_state->setErrorByName(
        'email',
        $this->t('It appears that %mail is not a valid email. Please try again',['%mail' => $value])
    );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    try {
      // Begin Phase 1: initiate variables to save.

      // Get current user ID.
      $uid = \Drupal::currentUser()->id();

      // Obtain values as entered into the Form.
      $nid = $form_state->getValue('nid');
      $email = $form_state->getValue('email');

      $current_time = \Drupal::time()->getRequestTime();
      // End Phase 1

      // Begin Phase 2: Save the values to the database

      // Start to build a query builder object $query.
      // https://www.drupal.org/docs/8/api/database-api/insert-queries
      $query = \Drupal::database()->insert('rsvplist');

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
        $email,
        $current_time,
      ]);

      // Execute the query!
      // Drupal handles the exact syntax of the query automatically!
      $query->execute();
      // End Phase 2

      // Begin Phase 3: Display a success message

      // Provide the form submitter a nice message.
      \Drupal::messenger()->addMessage(
        t('Thank you for your RSVP, you are on the list for the event!')
      );
      // End Phase 3
    }
    catch (\Exception $e) {
      \Drupal::messenger()->addError(
        t('Unable to save RSVP settings at this time due to database error.
           Please try again.')
      );
    }

    $form_state->setRedirectUrl(Url::fromRoute('<front>'));
  }

  /**
   * {@inheritdoc}
   */
  protected function successfulAjaxSubmit(array $form, FormStateInterface $form_state)
  {
    // We need to retrieve the redirect URL set in ::formSubmit(), but
    // the getRedirect() method will return false if redirects are disabled.
    // Form redirects are normally disabled during AJAX requests by the form
    // builder.
    // @see \Drupal\Core\Form\FormBuilder::buildForm()
    $is_redirect_disabled = $form_state->isRedirectDisabled();
    $form_state->disableRedirect(FALSE);
    $redirect = $form_state->getRedirect();
    $form_state->disableRedirect($is_redirect_disabled);
    $response = new AjaxResponse();
    $url = new RedirectCommand($redirect->toString());

    return $response->addCommand($url);
  }
}
