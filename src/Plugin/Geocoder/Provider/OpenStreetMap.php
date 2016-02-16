<?php

/**
 * @file
 * Contains \Drupal\geocoder\Plugin\Geocoder\Provider\OpenStreetMap.
 */

namespace Drupal\geocoder\Plugin\Geocoder\Provider;

use Drupal\geocoder\ProviderWithHttpAdapterBase;

/**
 * Provides an OpenStreetMap geocoder provider plugin.
 *
 * @GeocoderProvider(
 *   id = "openstreetmap",
 *   name = "OpenStreetMap",
 *   handler = "\Geocoder\Provider\OpenStreetMap"
 * )
 */
class OpenStreetMap extends ProviderWithHttpAdapterBase {}
