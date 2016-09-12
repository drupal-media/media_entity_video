<?php

namespace Drupal\media_entity_video\Plugin\EntityBrowser\Widget;

use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_browser\Plugin\EntityBrowser\Widget\Upload as FileUpload;
use Drupal\media_entity\MediaInterface;

/**
 * Uses upload to create media entity videos.
 *
 * @EntityBrowserWidget(
 *   id = "media_entity_video_upload",
 *   label = @Translation("Upload video files"),
 *   description = @Translation("Upload widget that creates media entity videos.")
 * )
 */
class Upload extends FileUpload {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'extensions' => 'mp4 webm ogg ogv',
      'media bundle' => NULL,
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function getForm(array &$original_form, FormStateInterface $form_state, array $aditional_widget_parameters) {
    /** @var \Drupal\media_entity\MediaBundleInterface $bundle */
    if (!$this->configuration['media bundle'] || !($bundle = $this->entityTypeManager->getStorage('media_bundle')->load($this->configuration['media bundle']))) {
      return ['#markup' => $this->t('The media bundle is not configured correctly.')];
    }

    if ($bundle->getType()->getPluginId() != 'video') {
      return ['#markup' => $this->t('The configured bundle is not using video plugin.')];
    }

    $form = parent::getForm($original_form, $form_state, $aditional_widget_parameters);
    $form['upload']['upload_validators']['file_validate_extensions'] = [$this->configuration['extensions']];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareEntities(array $form, FormStateInterface $form_state) {
    $files = parent::prepareEntities($form, $form_state);

    /** @var \Drupal\media_entity\MediaBundleInterface $bundle */
    $bundle = $this->entityTypeManager
      ->getStorage('media_bundle')
      ->load($this->configuration['media_bundle']);

    $videos = [];
    foreach ($files as $file) {
      /** @var \Drupal\media_entity\MediaInterface $video */
      $video = $this->entityTypeManager->getStorage('media')->create([
        'bundle' => $bundle->id(),
        $bundle->getTypeConfiguration()['source_field'] => $file,
      ]);

      $videos[] = $video;
    }

    return $videos;
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array &$element, array &$form, FormStateInterface $form_state) {
    if (!empty($form_state->getTriggeringElement()['#eb_widget_main_submit'])) {
      $videos = $this->prepareEntities($form, $form_state);
      array_walk($videos, function (MediaInterface $media) {
        $media->save();
      });

      $this->selectEntities($videos, $form_state);
      $this->clearFormValues($element, $form_state);
    }
  }

}
