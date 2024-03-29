# Twig REPEAT Examples

See the following page for examples of filters, functions, and custom validations available after installing this module: 
* __/repeat-twig/examples?keyword=this is a test__

## Help Links
- https://www.drupal.org/docs/theming-drupal/twig-in-drupal/filters-modifying-variables-in-twig-templates
- https://www.drupal.org/docs/theming-drupal/twig-in-drupal/functions-in-twig-templates
- https://www.hashbangcode.com/article/drupal-9-creating-custom-twig-functions-and-filters
- https://php.budgegeria.de/gjvtgrfg

## How add a new filter?
1. Go src/TwigExtension/TwigExtension.php
2. Add a new filter instance inside getFilters() function, in this sample, we call it ***my_new_filter_name***, the instance receives two parameters, the name of the filter and a array with the callable function.
```
  public function getFilters() {
    $filters = [

      // - Media Image Url -
      //
      // @code
      //   {# Basic usage. #}
      //   {{ content.field_media|media_image_url }}
      //
      // @endcode
      new TwigFilter('media_image_url', [$this, 'getMediaImageUrl']),

      // - *** My New Filter *** -
      //
      // @code
      //   {# Basic usage. #}
      //   {{ string|my_new_filter_name }}
      //
      // @endcode
      new TwigFilter('my_new_filter_name', [$this, 'myNewFilterFunction']),
    ];    
```
3. Add function with the logic to the filter
```
  /**
   * It is a example for add a filter.
   *
   * @param string $string
   *   The string to apply the filter, it could be other variable type, 
   *   it is anything place in left side of pipe when the filter is used string|my_filter_name.
   *
   * @param int $key
   *   It could be a optional parameter. string|my_filter_name('my_key')
   *
   * @return string
   *   The string or anything with the filter applied.
   */
  public function myNewFilterFunction(string $string, string $key = '') {
    $processed_string = $string;
    
    if (!empty($key)) {
      $processed_string .= ' - ' . $key;  
    }

    return $processed_string;
  }
```
4. That's all, the new filter could be used from any twig template 
```
{{ string|my_new_filter_name }}
```

## How add a new function?
1. Go src/TwigExtension/TwigExtension.php
2. Add a new function instance inside getFunctions() function, in this sample, we call it ***my_new_function_name***, the instance receives two parameters, the name of the function and a array with the callable function.
```
  public function getFunctions() {
    return [

      // - Query Parameter -
      //
      // @code
      //   {# Basic usage. #}
      //   {{ query_parameter('keyword', 'Default Value') }}
      //
      new TwigFunction('query_parameter', [$this, 'getQueryParameter']),

      // - *** My New Function *** -
      //
      // @code
      //   {# Basic usage. #}
      //   {{ my_new_function_name(string) }}
      //
      // @endcode
      new TwigFunction('my_new_function_name', [$this, 'myNewFunctionName']),
    ];    
```
3. Add function with the logic to make
```
  /**
   * It is a example for add a function twig.
   *
   * @param string $string
   *   The string given, it could be other variable type, 
   *
   * @return string
   *   The string or anything with the logic applied.
   */
  public function myNewFunctionName(string $string) {
    $processed_string = $string;
    
    if (!empty($processed_string)) {
      $processed_string .= ' - Hello world!!';  
    }

    return $processed_string;
  }
```
4. That's all, the new function could be used from any twig template 
```
{{ my_new_function_name('string') }}
```

## How add a new custom validation?
1. Go src/TwigExtension/TwigExtension.php
2. Add a new function instance inside getTests() function, in this sample, we call it ***my_new_test_name***, the instance receives two parameters, the name of the function and a array with the callable function.
```
  public function getTests() {
    return [
      // - *** My New Custom Validaiton *** -
      //
      // @code
      //   {# Basic usage. #}
      //   {% value is my_new_test_name %}
      //
      // @endcode
      new TwigFunction('my_new_test_name', [$this, 'myNewTestName']),
    ];    
```
3. Add function with the logic to make
```
  /**
   * It is a example for add a custom validation on twig.
   *
   * @param mixed $value
   *   The value given, it could be any variable type, 
   *
   * @return boolean
   *   The result of the validation, true or false.
   */
  public function myNewTestName($value) {
    return is_bool($value);
  }
```
4. That's all, the new custom validation could be used from any twig template 
```
{% value is my_new_test_name %} do something {% endif %}
```

## Filters

### Date Range Format
#### Basic Usage
```
{{ start_date|date_range_format(end_date, month_format, day_format) }}
{{ '2020-07-08T16:52:43'|date_range_format('2020-07-09T18:00:00') }}
{{ '2020-07-08T16:52:43'|date_range_format('2020-07-09T18:00:00', 'M', 'jS') }}
```
#### Output
```
Jul 8 - 9, 2020
Jul 8th - 9th, 2020
```

### Truncate Safe
#### Basic Usage
```
{{ string|truncate_safe(max_length, word_safe, add_ellipsis, min_word_safe_length) }}
{{ content.field_summary|render|truncate_safe(10) }}
```
#### Original
```
Lorem ipsum Lorem ipsum dolor sit amet, consectetur
```
#### Output
```
Lorem…
```

### Media Image Style
This example require media field named "field media" in node 1
#### Case of use
It could be useful in scenarios where it is necessary to use different image styles of the same field in the same display. (masonry grid)
#### Basic Usage
```
{{ content.field_media|media_image_style(image_style_name, key) }}
{{ content.field_media|media_image_style('thumbnail') }}
{{ content.field_media|media_image_style('thumbnail', 1) }}
<img src="{{ content.field_media|media_image_style('thumbnail') }} />
```
#### Output
```
https://base_url/sites/default/files/styles/thumbnail/public/2021-08/IMG_20200127_190131.jpg?itok=6VSlvdTT
```

### Media Image Url
This example require media field named "field media" in node 1
####  Basic Usage
```
{{ content.field_media|media_image_url(key) }}
{{ content.field_media|media_image_url }}
{{ content.field_media|media_image_url(1) }}
<div style="background:url({{ content.field_media|media_image_url }})> 
  This is a test 
</div>
```
####  Output
```
/sites/default/files/styles/large/public/2021-08/IMG_20200127_190131.jpg?itok=dGalzc-_
```

### Media Image Attr
This example require media field named "field media" in node 1,
the available attributes are alt, target_id, title, width, and height
####  Basic Usage
```
{{ content.field_media|media_image_attr(attr, key) }}
{{ content.field_media|media_image_attr }}
{{ content.field_media|media_image_attr('', 1) }}
{{ content.field_media|media_image_attr('alt') }}
{{ content.field_media|media_image_attr('alt', 1) }}
<img src="my_image" alt="{{ content.field_media|media_image_attr('alt') }}" />
```
####  Output
```
This is the alternative text
```

## Functions
### Query Parameter
It allow get a variable from url: /repeat-twig/examples?keyword=This is a test

Basic Usage
```
{{ query_parameter('keyword', 'Default Value') }}
```
Output
```
this is a test
```

## Custom Validations
### Numeric
It allows check if the value given is a number

#### Basic Usage
```
{% if value is numeric %} do something {% endif %}
```
Output
```
do something
```

#### Case of use
On this example, it detect the index numeric in array of the drupal field to get information from entities referenced
```
{% for key, reference in content.field_references %}
  {% if key is numeric %}
    <div class="reference--{{ key }} {{ key == 0 ? 'active'}}">
      {{ 
        drupal_field(
          'field_image', 
          'paragraph', 
          reference['#paragraph'].id()
        ) 
      }}   
    </div>             
  {% endif %}
{% endfor %}
```

### Float
It allows check if the value given is a float

Basic Usage
```
{% if value is float %} do something {% endif %}
```
Output
```
do something
```
