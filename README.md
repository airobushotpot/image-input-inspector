# Image Input Inspector

A small PHP library for reading local image dimensions, file size, and format,
then comparing them with upload limits supplied by the caller. It can help
prepare a starting frame for [Image to Video AI](https://imagetovideoai.tools/).

The tool does not upload files, call the website, or generate video. Installation and functionality have not been independently tested.

## Usage

Install with `composer require js-upup/image-input-inspector` after the package
is published, and load Composer's autoloader. This example uses a 10,000,000-byte limit; choose limits
for your own workflow and check the target service's current requirements.

```php
<?php
require 'vendor/autoload.php';

use ImageInputInspector\Inspector;

$result = Inspector::inspect(
    '/path/to/frame.jpg',
    10_000_000,
    ['image/jpeg', 'image/png', 'image/webp']
);

if (!$result['accepted']) {
    print_r($result['reasons']);
}
```

The result contains stored width, height, file size in bytes, detected image MIME
and rejection reasons. Invalid or unreadable images raise an exception. Limits
vary by model and may change; this package does not fetch or guarantee current
service settings. Image headers can be readable even if image data is corrupt;
this is not a full security, integrity, or upload validation system. EXIF
orientation is not applied.

Requires PHP 8.1 or later. MIT licensed.
