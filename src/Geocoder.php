<?php

namespace Drupal\geocoder;

use Drupal\geocoder\GeocoderProvider;
use Geocoder\Exception\InvalidCredentials;

class Geocoder {
  /**
   * Geocode a string.
   *
   * @param string|string[] $plugins
   *   The name of the plugin to use or a list of plugins names to use.
   * @param $data
   *   The string to geocode.
   * @param array $options (optional)
   *   The plugin options.
   *
   * @return \Geocoder\Model\AddressCollection|FALSE
   */
  public static function geocode($plugins = array('googlemaps'), $data, array $options = array()) {
    foreach ((array) $plugins as $plugin) {
      $plugin_options = isset($options[$plugin]) ? $options[$plugin] : array();
      $plugin = self::getPlugin('Provider', $plugin, $plugin_options);

      try {
        return $plugin->geocode($data);
      } catch (InvalidCredentials $e) {
        self::log($e->getMessage(), 'error');
      } catch (\Exception $e) {
        self::log($e->getMessage(), 'error');
      }
    }

    $exception = new \Exception(sprintf('No plugin could geocode: "%s".', $data));
    self::log($exception->getMessage(), 'error');

    return FALSE;
  }

  /**
   * Reverse geocode coordinates.
   *
   * @param string|string[] $plugins
   *   The name of the plugin to use or a list of plugins names to use.
   * @param double $latitude
   *   The latitude.
   * @param double $longitude
   *   The longitude.
   * @param array $options (optional)
   *   The plugin options.
   *
   * @return \Geocoder\Model\AddressCollection|FALSE
   */
  public static function reverse($plugins = 'googlemaps', $latitude, $longitude, array $options = array()) {
    foreach ((array) $plugins as $plugin) {
      $plugin_options = isset($options[$plugin]) ? $options[$plugin] : array();
      $plugin = self::getPlugin('Provider', $plugin, $plugin_options);

      try {
        return $plugin->reverse($latitude, $longitude);
      } catch (InvalidCredentials $e) {
        self::log($e->getMessage(), 'error');
      } catch (\Exception $e) {
        self::log($e->getMessage(), 'error');
      }
    }

    $exception = new \Exception(sprintf('No plugin could reverse geocode: "%s %s".', $latitude, $longitude));
    self::log($exception->getMessage(), 'error');

    return FALSE;
  }

  /**
   * Return a Geocoder plugin object.
   *
   * @param string $type
   *   The type of plugin to return.
   * @param string $plugin
   *   The plugin id to return.
   * @param array $options (optional)
   *   The plugin options.
   *
   * @return GeocoderProviderInterface|GeocoderDumperInterface
   *   The Geocoder plugin object.
   */
  public static function getPlugin($type, $plugin, array $options = array()) {
    $plugin = drupal_strtolower($plugin);
    return \Drupal::service('geocoder.' . drupal_ucfirst($type))->createInstance($plugin, $options);
  }

  /**
   * Gets a list of available plugins.
   *
   * @param string $type
   *   The plugin type.
   *
   * @return string[]
   *   The Geocoder plugin type.
   */
  public static function getPlugins($type) {
    $options = array();
    $type = 'geocoder.' . drupal_ucfirst($type);

    foreach (\Drupal::service($type)->getDefinitions() as $data) {
      $name = isset($data['name']) ? $data['name'] : $data['id'];
      $options[$data['id']] = $name;
    }
    asort($options);

    return $options;
  }

  /**
   * Log a message in the Drupal watchdog and on screen.
   *
   * @param string $message
   *   The message
   * @param string $type
   *   The type of message
   */
  public static function log($message, $type) {
    \Drupal::service('logger.dblog')->log($type, $message, array('channel' => 'geocoder'));
    \Drupal::service('messenger')->addMessage($message, $type);
  }

}
