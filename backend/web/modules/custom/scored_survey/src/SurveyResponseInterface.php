<?php
/**
 * @file ResponseInterface.php
 * Defines the interface for the Survey Response entity
 */

namespace Drupal\scored_survey;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

interface SurveyResponseInterface extends ContentEntityInterface, EntityOwnerInterface, EntityChangedInterface
{

}
