<?php

namespace Drupal\content_entity_example\Controller;

use Drupal\content_entity_example\Entity\Contact;
use Drupal\content_entity_example\EntTrait;
use Drupal\Core\Controller\ControllerBase;
use Drupal\content_entity_example\Form\ContactForm;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Language\Language;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\EntityFormBuilderInterface;



class Ent extends  ContentEntityForm
{


  public function get_all() {
    global $base_url;
    $lendos = [];
    // here we take all entities from our custom entity, and check its.
    $storage = \Drupal::entityTypeManager()->getStorage('content_entity_example_contact');
    $query = $storage->getQuery()
      ->pager(2)
      ->sort('created' , 'DESC');
    $result = $query->execute();
    $nodes = $storage->loadMultiple($result);

    foreach ($nodes as $node) {

      $images = $node->images->entity;
      $avatars = $node->avatar->entity;
      if ($images != NULL){
        $is_image = file_url_transform_relative(file_create_url($images->getFileUri()));
      }else{
        $is_image = 'none';
      }
      if ($avatars != NULL){
        $is_avatar = file_url_transform_relative(file_create_url($avatars->getFileUri()));
      }else{
        $is_avatar = 'default_avatar.png';
      }
      //after check, we write all needs fields in array
      array_push($lendos, [
        'id' => $node->id->value,
        'name' => $node->name->value,
        'mail' => $node->email->value,
        'image' => $is_image,
        'avatar' => $is_avatar,
        'tell' => $node->tell->value,
        'text' => $node->text->value,
        'created' => date('m/d/Y H:i:s' ,$node->created->value),
        'manage' => $node->link('manage'),

      ]);
    }
    //here we create form from entity
    $ent = Contact::create();
       $add_ent = \Drupal::service('entity.form_builder')->getForm($ent, 'add');



    $data        = [
      'title'  => 'LENDOS',
      'lendos' => $lendos,
    ];

    //create transport array
    $display[] = [
      '#theme' => 'ent_theme',
      '#data' => $data,
      '#add_ent' => $add_ent,
      '#base_url' => $base_url,
    ];

    $display ['paginate']= [
      '#type' => 'pager'
    ];

    return $display ;

  }



}

