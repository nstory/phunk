<?php

use Nstory\Phunk\Phunk as f;

/**
 * Runs code examples embedded in README.md.
 */
class DocTest extends \PHPUnit_Framework_TestCase
{
    public function testCodeExample()
    {
        $readme = file_get_contents(__DIR__ . '/../README.md');

        // pull out PHP code blocks; code blocks are surrounded by
        // ```php and ```
        $this->assertTrue(
            (boolean)preg_match_all(
                '/(?<=```php).*?(?=```)/s',
                $readme,
                $matches
            )
        );

        // combine the blocks and run 'em
        eval(implode('', $matches[0]));
    }
}
