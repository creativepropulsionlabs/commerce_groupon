<?php

/**
 * @file
 * Post update functions for User module.
 */

use Drupal\user\Entity\User;

/**
 * Ensure that groupon user is exist.
 */
function commerce_groupon_post_update_user_create() {
  $users = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties([
    'name' => 'groupon',
  ]);
  if (!empty($users)){
    return;
  }
  $language = \Drupal::languageManager()->getCurrentLanguage()->getId();
  $user = User::create();

  //Mandatory settings
  $user->setPassword('groupon');
  $user->enforceIsNew();
  $user->setEmail('groupon@example.com');
  $user->setUsername('groupon');

  //Optional settings
  $user->set("init", 'groupon@example.com');
  $user->set("langcode", $language);
  $user->set("preferred_langcode", $language);
  $user->set("preferred_admin_langcode", $language);
  $user->activate();

  //Save user
  $user->save();
}
