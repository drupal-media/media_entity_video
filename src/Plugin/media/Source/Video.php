<?php

namespace Drupal\media_entity_video\Plugin\media\Source;

use Drupal\media\MediaTypeInterface;
use Drupal\media\Plugin\media\Source\File;

/**
 * Provides media type plugin for Video.
 *
 * @MediaSource(
 *   id = "video",
 *   label = @Translation("Video"),
 *   description = @Translation("Provides business logic and metadata for Video Files."),
 *   allowed_field_types = {"file"},
 *   default_thumbnail_filename = "video.png",
 * )
 */
class Video extends File {

  /**
   * {@inheritdoc}
   */
  public function createSourceField(MediaTypeInterface $type) {
    return parent::createSourceField($type)->set('settings', ['file_extensions' => 'mp4 webm ogg ogv']);
  }

}
