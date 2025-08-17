## PHPUnit

### Get started

1. Install
    ```bash
    composer require phpunit/phpunit
    ```
2. Create the 'tests' folder in the root directory and repeat project tree.

3. composer.json
    ```json
    {
      "autoload-dev": {
        "psr-4": {
          "Tests\\": "tests/"
        }
      }
    }
    ```

4. Create a test class in the "tests" directory.

5. Create the settings file "phpunit.xml" in the root directory.

6. phpunit.xml:
    ```xml
    <phpunit
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.5/phpunit.xsd"
        bootstrap="tests\bootstrap.php"
        displayDetailsOnTestsThatTriggerDeprecations="true"
        displayDetailsOnPhpunitDeprecations="true"
        colors="true"
        cacheDirectory="tests/info"
    >
        <testsuites>
            <testsuite name="unit">
                <directory>tests/unit</directory>
            </testsuite>
        </testsuites>

        <source>
            <include>
                <directory>src</directory>
            </include>
            <exclude>
                <directory>vendor</directory>
                <directory>coverage</directory>
                <directory>tests</directory>
            </exclude>
        </source>

        <coverage>
            <report>
                <clover outputFile="tests/info/coverage.xml"/>
                <html outputDirectory="tests/info/coverage"/>
                <text outputFile="php://stdout"/>
            </report>
        </coverage>

        <logging>
            <junit outputFile="tests/info/log/junit.xml"/>
        </logging>
    </phpunit>
    ```

7. For working coverage you need to enable coverage mode:
    ```php.ini
    xdebug.mode=debug,coverage
    ```

8. ExampleTest.php
    ```php
    class FunctionsTest extends TestCase {}
    ```

9. Start tests:
    ```bash
    vendor/bin/phpunit [tests]
    ```