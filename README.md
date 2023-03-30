# Reporter

An elegant wrapper for JasperStarter using Laravel

## Installation

```bash
composer require rdpascua/reporter
```

## Usage

Loading a report and streaming it to the browser

```php
use Rdpascua\Reporter\Facades\Reporter;

// Stream the report to the browser
Reporter::load('path/to/jasper/file.jasper')->stream('document.pdf');
```

Passing parameters

```php
Reporter::load('path/to/jasper/file.jasper', [
    'param1' => 'value1',
    'param2' => 'value2',
])
->stream('document.pdf');
```

Saving the report to a file

```php
Reporter::load('path/to/jasper/file.jasper', [
    'param1' => 'value1',
    'param2' => 'value2',
])
->save('document.pdf');
```

Generate a report using a database connection

```php
Reporter::load('path/to/jasper/file.jasper')->withDataSource('pgsql')->save('document.pdf');
```

Compiling a jrxml file

```php
Reporter::load('path/to/jasper/file.jrxml')->compile('path/to/jasper/file.jasper');
```

TODO: Compiling multiple jrxml files

```php
Reporter::load([
    'path/to/jasper/file1.jrxml',
    'path/to/jasper/file2.jrxml',
    'path/to/jasper/file3.jrxml',
])->compile();

// Generates the following files
// path/to/jasper/file1.jasper
// path/to/jasper/file2.jasper
// path/to/jasper/file3.jasper
```

## TODO

- [ ] Compiling multiple jrxml files
- [ ] Add support for jdbc connections
- [ ] Add support for csv
- [ ] Add support for xml
- [ ] Add support for json
