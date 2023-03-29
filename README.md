# Reporter

An elegant wrapper for JasperStarter using Laravel

## Installation

```bash
composer require rdpascua/reporter
```

## Usage

```php
use Rdpascua\Reporter\Facades\Reporter;

// Stream the report to the browser
Reporter::load('path/to/jasper/file.jrxml')->stream('document.pdf');

// Pass parameters to the report and download it
Reporter::load('path/to/jasper/file.jrxml', [
    'param1' => 'value1',
    'param2' => 'value2',
])
->setConnection('pgsql')
->download('document.pdf');

// Pass parameters to the report and save it to a file
Reporter::load('path/to/jasper/file.jrxml', [
    'param1' => 'value1',
    'param2' => 'value2',
], 'mysql')
->save('document.pdf');
```

## TODO

- [ ] Add support for jdbc connections
- [ ] Add support for csv
- [ ] Add support for xml
- [ ] Add support for json
