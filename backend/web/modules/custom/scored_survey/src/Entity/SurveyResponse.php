<?php
/**
 * @file Response.php
 */


namespace Drupal\scored_survey\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\scored_survey\SurveyResponseInterface;
use Drupal\user\UserInterface;

  /**
   * Defines the Survey Response entity.
   *
   * @ContentEntityType(
   *   id = "scored_survey_response",
   *   label = @Translation("Survey Response"),
   *   handlers = {
   *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
   *     "list_builder" = "Drupal\scored_survey\Entity\Controller\ResponseListBuilder",
   *     "views_data" = "Drupal\views\EntityViewsData",
   *   },
   *   base_table = "SurveyResponse",
   *   admin_permission = "administer scored survey response",
   *   fieldable = TRUE,
   *   entity_keys = {
   *     "id" = "id",
   *     "uuid" = "uuid",
   *     "user_id" = "user_id",
   *     "survey" = "survey"
   *   },
   *   links = {
   *     "canonical" = "/scored_survey_response/{scored_survey_Response}",
   *     "edit-form" = "/scored_survey_response/{scored_survey_Response}/edit",
   *     "delete-form" = "/Response/{scored_survey_response}/delete",
   *     "collection" = "/scored_survey_response/list"
   *   },
   *   field_ui_base_route = "scored_survey.response_settings",
   * )
   */
class SurveySurveyResponse extends ContentEntityBase implements SurveyResponseInterface {

  /**
   * {@inheritdoc}
   *
   * When a new entity instance is added, set the user_id entity reference to
   * the current user as the creator of the instance.
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTime() {
    return $this->get('changed')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setChangedTime($timestamp) {
    $this->set('changed', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getChangedTimeAcrossTranslations()  {
    $changed = $this->getUntranslated()->getChangedTime();
    foreach ($this->getTranslationLanguages(FALSE) as $language)    {
      $translation_changed = $this->getTranslation($language->getId())->getChangedTime();
      $changed = max($translation_changed, $changed);
    }
    return $changed;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   *
   * Define the field properties here.
   *
   * Field name, type and size determine the table structure.
   *
   * In addition, we can define how the field and its content can be manipulated
   * in the GUI. The behaviour of the widgets used can be determined here.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Name field for the survey response.
    // We set display options for the view as well as the form.
    // Users with correct privileges can change the view and edit configuration.

    $fields['survey_node_reference'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('survey'))
      ->setDescription(t('Reference to the survey this is a response for'))
      ->setSetting('target_type','node')
      ->setSetting('handler','default')
      ->setSetting('handler_settings',['target_bundles' =>['survey' => 'survey']])
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -6,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'placeholder' => 'Title of the survey'
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['questions'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Questions'))
      ->setSettings(array(
        'default_value' => '',
        'max_length' => 255,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'above',
        'type' => 'string',
        'weight' => -5,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -5,
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }
}