# Config Parser

Config Parser parses single JSON files, or multiple merged JSON files and
enables the lookup of values via dot notation.

## Install
```
git clone https://github.com/dozyio/config-chg config-parser
cd config-parser
composer install
```

## Usage example

```
$parser = new ConfigParser();
$parser->load('file1.json', 'file2.json');
if ($parser->hasErrors()) {
    // ... handle errors
    // print_r($parser->errors);
}
$value = $parser->getValue('database.host');
print_r($value);
```

## Testing

### Run all tests
```
vendor/bin/phpunit
```

### Run all tests in parallel
```
vendor/bin/paratest
```

### Run individual tests
```
vendor/bin/phpunit path/to/test
```

## Testing with Docker

### Build image
```
make build
```

### Run tests
```
make test
```

### Build and run test
```
make all
```
