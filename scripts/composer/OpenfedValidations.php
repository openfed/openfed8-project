<?php

/**
 * @file
 * Contains \Openfed\composer\OpenfedValidations.
 */

namespace Openfed\composer;

use Composer\Script\Event;
use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;


class OpenfedValidations {

  public static function validateUpdate810(Event $event) {

    if (!self::_isDrupalSite()) {
      return;
    }

    self::_checkProjectVersion();

    self::_checkDeprecatedModules();

    self::_checkTwigTweak2Compatibility();

    // self::_checkTwigTweak3Compatibility();

  }

  private static function _isDrupalSite() {
    $output = trim(shell_exec('drush status --field="Drupal bootstrap"'));
    if (empty($output)) {
      return false;
    }
    return true;
  }

  private static function _checkProjectVersion() {
    $composer_openfed = json_decode(file_get_contents('composer.openfed.json'), true);
    $current_version = $composer_openfed['version'];
    $latest_openfed8_project = explode("\n",trim(shell_exec("git -c 'versionsort.suffix=-' ls-remote --tags --sort='-v:refname' https://github.com/openfed/openfed8-project | cut --delimiter='/' --fields=3 | grep -v -")));

    if(version_compare($latest_openfed8_project[0], $current_version) > 0) {
      throw new \ErrorException("There's a new version of Openfed8 Project available, please update composer files.");
    }

  }

  private static function _checkDeprecatedModules() {
    $modules_to_check = [
      'toolbar_themes',
      'sharemessage',
      'simple_gmap',
      'scheduled_updates',
      'field_default_token',
      'contact_storage_clear',
      'yamlform_clear',
      'features'
    ];
    foreach ($modules_to_check as $module) {
      $output = trim(shell_exec('drush pml --field="status" --filter="' . $module . '"'));
      if ($output == 'Enabled') {
        throw new \ErrorException("You can't proceed with Openfed update until you uninstall $module. See Openfed release notes.");
      }
    }
  }

  private static function  _isTwigTweakEnabled() {
    $module = 'twig_tweak';
    $output = trim(shell_exec('drush pml --field="status" --filter="' . $module . '"'));
    if ($output == 'Enabled') {
      return true;
    }
    return false;
  }

  private static function _initDrupalContainer() {
    $autoloader = require_once getcwd() . '/docroot/autoload.php';
    $request = Request::createFromGlobals();
    $kernel = DrupalKernel::createFromRequest($request, $autoloader, 'prod');
    $kernel->prepareLegacyRequest($request);
  }

  private static function _checkTwigTweak2Compatibility() {
    if (self::_isTwigTweakEnabled()) {
      // Check template for Twig Tweak 2.x compatibility issues.
      // See https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x

      self::_initDrupalContainer();

      // 1. Check Rendering Blocks.
      $available_blocks = array_keys(\Drupal::service('plugin.manager.block')->getDefinitions());
      $search = shell_exec('grep --include \*.twig -r "drupal_block" ./docroot/themes/ ./config/');
      $pattern = '/drupal_block\([\'|"]([a-zA-Z0-9\-_]+)[\'|"]\)/';
      preg_match_all($pattern, $search, $matches);

      foreach ($matches[1] as $block_id) {
        // If one of the block matches is an available block plugin id, we
        // should not proceed.
        if (!in_array($block_id, $available_blocks)) {
          throw new \ErrorException("You should convert your theme to Twig Tweak 2.x before updating Openfed. See https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x#rendering-blocks.");
        }
      }

      // 2. Check preg_replace filter declaration.
      $search = shell_exec('grep --include \*.twig -r "preg_replace" ./docroot/themes/ ./config/');
      $pattern = '/preg_replace\([\'|"]\/?/';
      preg_match_all($pattern, $search, $matches);

      foreach ($matches[0] as $expression) {
        // If one of the block matches is an available block plugin id, we
        // should not proceed.
        if (substr($expression, -1) != '/') {
          throw new \ErrorException("You should convert your theme to Twig Tweak 2.x before updating Openfed. See https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x#preg-replace-filter.");
        }
      }

      // 3. Check Entity access.
      $twig_tweak_check_access = \Drupal::service('settings')->get('twig_tweak_check_access');
      if ($twig_tweak_check_access === FALSE) {
        throw new \ErrorException("You should convert your settings.php to Twig Tweak 2.x before updating Openfed. See https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x#entity-access.");
      }

      // 4. Check Region wrapper.
      // 5. Check Default field language.

      // 6. Check preg_replace filter declaration.
      $search = shell_exec('grep --include \*.twig -r "drupal_set_message" ./docroot/themes/ ./config/');
      $pattern = '/drupal_set_message\([\'|"]/';
      preg_match_all($pattern, $search, $matches);

      if (!empty($matches[0])) {
        throw new \ErrorException("You should convert your theme to Twig Tweak 2.x before updating Openfed. See https://www.drupal.org/docs/contributed-modules/twig-tweak/migrating-to-twig-tweak-2x#default-field-language.");
      }

    }
  }

  private static function _checkTwigTweak3Compatibility() {
    if (self::_isTwigTweakEnabled()) {
      // TODO: for Openfed 11.
    }
  }

}
