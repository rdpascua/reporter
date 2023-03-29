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
Reporter::load('path/to/jasper/file.jasper')->stream('document.pdf');

// Pass parameters to the report and download it
Reporter::load('path/to/jasper/file.jasper', [
    'param1' => 'value1',
    'param2' => 'value2',
])
->download('document.pdf');

// Pass parameters to the report and save it to a file
Reporter::load('path/to/jasper/file.jasper', [
    'param1' => 'value1',
    'param2' => 'value2',
])
->save('document.pdf');

// Generate a report using a database connection
Reporter::load('path/to/jasper/file.jasper')->withDataSource('pgsql')->save('document.pdf');
```

## TODO

- [ ] Add support for jdbc connections
- [ ] Add support for csv
- [ ] Add support for xml
- [ ] Add support for json
