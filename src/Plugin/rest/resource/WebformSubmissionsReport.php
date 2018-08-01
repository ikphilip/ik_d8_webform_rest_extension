<?php

namespace Drupal\ik_d8_webform_rest_extension\Plugin\rest\resource;

use Drupal\webform\Entity\WebformSubmission;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ModifiedResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "webform_rest_report",
 *   label = @Translation("Webform Report"),
 *   uri_paths = {
 *     "canonical" = "/webform_rest/{webform_id}/report"
 *   }
 * )
 */
class WebformSubmissionsReport extends ResourceBase {

  /**
   * Constructs a new WebformSubmissionsReport object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
  }

  /**
   * Responds to GET requests.
   *
   * @param string $webform_id
   *   Webform ID.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The HTTP response object.
   *
   * @throws \Symfony\Component\HttpKernel\Exception\HttpException
   *   Throws exception expected.
   */
  public function get($webform_id) {

    if (empty($webform_id)) {
      $errors = [
        'error' => [
          'message' => 'Webform ID is required.'
        ]
      ];
      return new ModifiedResourceResponse($errors);
    }

    // Collect all submissions related to the Webform ID
    $query = \Drupal::entityQuery('webform_submission');
    $query->condition('webform_id', $webform_id);
    $webform_submission_ids = $query->execute();

    // Load all and return them
    $reports = WebformSubmission::loadMultiple($webform_submission_ids);

    if ($reports) {
      $entity = [
        'data' => $reports,
      ];

      return new ModifiedResourceResponse($entity, 200);
    }

    throw new NotFoundHttpException(t("Can't load webform submission."));

  }

}
