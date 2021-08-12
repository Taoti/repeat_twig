<?php

namespace Drupal\repeat_twig\TwigExtension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Drupal\Component\Utility\Unicode;
use Drupal\image\Entity\ImageStyle;

/**
 * Twig extension with some useful functions and filters.
 */
class TwigExtension extends AbstractExtension {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return 'repeat_twig.twig_extension';
  }

  /**
   * {@inheritdoc}
   */
  public function getFunctions() {
    return [
      // - Get Query Parameter -
      //
      // @code
      //   {# Basic usage. #}
      //   {{ get_query_parameter('keyword', 'Default Value') }}
      //
      new TwigFunction('get_query_parameter', [$this, 'getQueryParameter']),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFilters() {
    $filters = [

      // - date_range_format -
      //
      // @code
      //   {# Basic usage. #}
      //   {{ '2020-07-08T16:52:43'|date_range_format('2020-07-09T18:00:00') }}
      //
      // @endcode
      new TwigFilter('date_range_format', [$this, 'dateRangeFormat']),

      // - truncate -
      //
      // @code
      //   {# Basic usage. #}
      //   {{ 'lorem ipsum lorem ipsum'|truncate_safe(100) }}
      //
      // @endcode
      new TwigFilter('truncate_safe', [$this, 'truncateSafe']),

      // - Media Image Style -
      //
      // @code
      //   {# Basic usage. #}
      //   {{ content.field_media|media_image_style('thumbnail') }}
      //
      // @endcode
      new TwigFilter('media_image_style', [$this, 'mediaImageStyle']),

      // - Media Image Url -
      //
      // @code
      //   {# Basic usage. #}
      //   {{ content.field_media|media_image_url }}
      //
      // @endcode
      new TwigFilter('media_image_url', [$this, 'getMediaImageUrl']),
    ];

    return $filters;
  }

  /**
   * Get a query variable from url.
   *
   * @param string $key
   *   The name of variable.
   * @param string $default_value
   *   The default value for the parameter give.
   *
   * @return null|string
   *   The value of the variable.
   */
  public function getQueryParameter(string $key, string $default_value = '') {
    $param = \Drupal::request()->query->get($key);

    if (empty($param)) {
      $param = $default_value;
    }

    return $param;
  }

  /**
   * Date range format.
   *
   * @param string $start_date
   *   An date string to be used like start date.
   * @param string $end_date
   *   An date string to be used like end date.
   * @param string $month_format
   *   An string to be used like month format.
   * @param string $day_format
   *   An string to be used like day format.
   *
   * @return string
   *   The entered HTML text to date range format
   */
  public function dateRangeFormat($start_date, $end_date = NULL, $month_format = 'M', $day_format = 'j') {

    if (empty($start_date) && empty($end_date)) {
      return '';
    }

    $year_format = 'Y';
    $full_format = $month_format . ' ' . $day_format . ', Y';
    $medium_format = $month_format . ' ' . $day_format;

    $start_datetime = (!empty($start_date)) ? new \DateTime($start_date) : NULL;
    $end_datetime = (!empty($end_date)) ? new \DateTime($end_date) : NULL;

    if (empty($end_datetime)) {
      return $start_datetime->format($full_format);
    }

    if ($start_datetime->format($full_format) == $end_datetime->format($full_format)) {
      // Same date.
      return $start_datetime->format($full_format);
    }
    elseif ($start_datetime->format($month_format) == $end_datetime->format($month_format)) {
      // Same month.
      return t('@start_medium_format - @end_day, @end_year', [
        '@start_medium_format' => $start_datetime->format($medium_format),
        '@end_day' => $end_datetime->format($day_format),
        '@end_year' => $end_datetime->format($year_format),
      ]);
    }
    elseif ($start_datetime->format($year_format) == $end_datetime->format($year_format)) {
      // Same year.
      return t('@start_medium_format - @end_medium_format, @end_year', [
        '@start_medium_format' => $start_datetime->format($medium_format),
        '@end_medium_format' => $end_datetime->format($medium_format),
        '@end_year' => $end_datetime->format($year_format),
      ]);
    }
    else {
      return t('@start_full_format - @end_full_format', [
        '@start_full_format' => $start_datetime->format($full_format),
        '@end_full_format' => $end_datetime->format($full_format),
      ]);
    }

    return '';
  }

  /**
   * Truncates a UTF-8-encoded string safely to a number of characters.
   *
   * @param string $string
   *   The string to truncate.
   * @param int $max_length
   *   An upper limit on the returned string length, including trailing ellipsis
   *   if $add_ellipsis is TRUE.
   * @param bool $wordsafe
   *   (optional) If TRUE, attempt to truncate on a word boundary.
   * @param bool $add_ellipsis
   *   (optional) If TRUE, add '...' to the end of the truncated string.
   * @param int $min_wordsafe_length
   *   (optional) If TRUE, the minimum acceptable length for truncation.
   *
   * @return string
   *   The truncated string.
   *
   * @see \Drupal\Component\Utility\Unicode::truncate()
   */
  public function truncateSafe($string, $max_length, $wordsafe = TRUE, $add_ellipsis = TRUE, $min_wordsafe_length = 1) {

    if (empty($string)) {
      return '';
    }

    $string = trim(strip_tags($string));

    return Unicode::truncate($string, $max_length, $wordsafe, $add_ellipsis, $min_wordsafe_length);
  }

  /**
   * It generate url with the media and image style given.
   *
   * @param array $element
   *   The element from view mode.
   *
   * @param string $image_style
   *   The machine name of the image style.
   *
   * @param int $key
   *   The index of the element in the array given.
   *
   * @return string
   *   The url of the image with the image style given.
   */
  public function mediaImageStyle(array $element, string $image_style = '', int $key = 0) {
    $url = '';
    if ($uri = $this->getMediaImageUri($element, $key)) {
      if ($image_style_entity = ImageStyle::load($image_style)) {
        $path = $image_style_entity->buildUrl($uri);
      }

      $url = file_create_url($path);
    }

    return $url;
  }

  /**
   * It generate url with the media and image style given.
   *
   * @param array $element
   *   The element from view mode.
   *
   * @param int $key
   *   The index of the element in the array given.
   *
   * @return string
   *   The url of the image in media entity with the configuration from view mode.
   */
  public function getMediaImageUrl(array $element, int $key = 0) {
    $url = '';
    if (isset($element[$key])) {
      $markup = \Drupal::service('renderer')->render($element[$key]);
      $html = $markup->__toString();
      preg_match('@src="([^"]+)"@', $html, $match);
      $src = array_pop($match);

      if (!empty($src)) {
        $url = $src;
      }
    }

    return $url;
  }

  /**
   * It get uri from media entity.
   *
   * @param array $element
   *   The element from view mode.
   *
   * @param int $key
   *   The index of the element in the array given.
   *
   * @return string
   *   The uri of the image in media entity.
   */
  public function getMediaImageUri($element, $key = 0) {
    $uri = '';
    if ($media = $this->getMediaEntity($element, $key)) {
      if ($media->hasField('field_media_image')) {
        $uri = $media->field_media_image->entity->getFileUri();
      }
    }
    return $uri;
  }

  /**
   * It get the media entity from element given.
   *
   * @param array $element
   *   The element from view mode.
   *
   * @param int $key
   *   The index of the element in the array given.
   *
   * @return bool|MediaInterface
   *   The media entity.
   */
  public function getMediaEntity($element, $key = 0) {
    $entity = FALSE;

    if (gettype($element) == 'array') {
      if (isset($element[$key]) && isset($element[$key]['#media'])) {
        $entity = $element[$key]['#media'];
      }
      elseif (isset($element['#media'])) {
        $entity = $element['#media'];
      }
    }
    return $entity;
  }

}
