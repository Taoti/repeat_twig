

# Twig Repeat Examples

See the page with examples of the filters and functions available after of the install the module: 
* __/repeat-twig/examples?keyword=this is a test__

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
#### Basic Usage
```
{{ content.field_media|media_image_style(image_style_name, key) }}
{{ content.field_media|media_image_style('thumbnail') }}
{{ content.field_media|media_image_style('thumbnail', 1) }}
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
```
####  Output
```
/sites/default/files/styles/large/public/2021-08/IMG_20200127_190131.jpg?itok=dGalzc-_
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