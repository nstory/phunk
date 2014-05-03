# Phunk

[![Build Status](https://travis-ci.org/nstory/phunk.svg?branch=master)](https://travis-ci.org/nstory/phunk)

Phunk is a fluent library for working PHP arrays. It's, uhh.. "phunktional." This library provides a fun, convenient syntax for mapping, filtering, and otherwise manipulating arrays.

## Example Usage
```php
use Nstory\Phunk\Phunk as f;

$starships = [
    (object)[
        'name' => 'Enterprise',
        'registry' => 'NCC-1701',
        'captain' => (object)[
            'name' => 'James Kirk',
            'born' => 2233
        ]
    ],
    (object)[
        'name' => 'Enterprise',
        'registry' => 'NCC-1701-D',
        'captain' => (object)[
            'name' => 'Jean-Luc Picard',
            'born' => 2305
        ]
    ],
    (object)[
        'name' => 'Voyager',
        'registry' => 'NCC-74656',
        'captain' => (object)[
            'name' => 'Kathryn Janeway',
            // 'born' => unknown...
        ]
    ]
];

$enterprise_captains =
    f::filter($starships, function($o)
        {return preg_match('/enterprise/i', $o->name);})
    ->map(f::path()->captain->name)
    ->asArray();
assert($enterprise_captains == ['James Kirk', 'Jean-Luc Picard']);

```
## Installation
_write me!_

## Tests
Type `phpunit` and press <CR>. That easy.

Also, and this is fun, the test suite runs all the code-snippets in this-here README.md file.

## Contributors
Pull Requests are welcome (:

## License
MIT
