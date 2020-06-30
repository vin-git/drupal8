<?php

namespace Drupal\hook_site_information\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Drupal\Core\Controller\ControllerBase;

/**
 * Class JsonApiPageController
 * @package Drupal\hook_site_information\Controller
 */
class JsonApiPageController extends ControllerBase{
  /**
   * @return JsonResponse
   */
  public function index($siteapikey, $nodeid) {
  // GET Site API Key from 'Site Information' section
    $actual_siteapikey = \Drupal::config('siteapikey.configuration')->get('siteapikey');

    $result=[];
    //Pass the node id and the node type page in where condition to check whether the corresponding node is available 
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'page') // node type is page
      ->condition('nid', $nodeid)
      ->condition('status', 1) //published or not
      ->sort('title', 'DESC');
    $nodes_ids = $query->execute();
   // Check the Site API key matches with the supplied one and if the supplied node exists
    if ($nodes_ids && $siteapikey == $actual_siteapikey) {
        $node = \Drupal\node\Entity\Node::load($nodeid);
        $json_array['data'][] = array(
        'type' => $node->get('type')->target_id,
        'id' => $node->get('nid')->value,
        'attributes' => array(
          'title' =>  $node->get('title')->value,
          'content' => $node->get('body')->value,
        ),
       );
    }else{
      $json_array['data'][] = array(
        'message' => 'Access Denied'
      );
    }
   return new JsonResponse($json_array);
  }
}
