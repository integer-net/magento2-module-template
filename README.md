# integer_net Magento 2 Module Template

This is a GitHub template.

**Create your module now with the "use this template" button:**

[![Use this template](dev/use-this-template.png)](https://github.com/integer-net/magento2-module-template/generate)

After that, clone it on your machine and run `/dev/init` for automatic configuration. For more information, see [dev/README.md](dev/README.md)

<div align="center"><img src="https://www.integer-net.de/wp-content/uploads/2012/11/firmenprofil.jpg" alt="Waschbär Approved Module" /></div>

---

<!-- TEMPLATE -->

# :module-namespace_:module-name Magento Module
<div align="center">

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
![Supported Magento Versions][ico-compatibility]

[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Maintainability](ico-maintenability)](link-maintainability)
</div>

---

:description

## Installation

1. Install it into your Magento 2 project with composer:
    ```
    composer require :vendor/:package
    ```

2. Enable module
    ```
    bin/magento setup:upgrade
    ```

## Configuration

## Usage

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Testing

### Unit Tests

```
vendor/bin/phpunit tests/unit
```

### Magento Integration Tests

0. Configure test database in `dev/tests/integration/etc/install-config-mysql.php`. [Read more in the Magento docs.](https://devdocs.magento.com/guides/v2.4/test/integration/integration_test_execution.html) 

1. Copy `tests/integration/phpunit.xml.dist` from the package to `dev/tests/integration/phpunit.xml` in your Magento installation.

2. In that directory, run
    ``` bash
    ../../../vendor/bin/phpunit
    ```


## Security

If you discover any security related issues, please email :author-email instead of using the issue tracker.

## Credits

- [:author-name][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/:vendor/:package/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/:vendor/:package?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/:vendor/:package.svg?style=flat-square
[ico-maintainability]: https://img.shields.io/codeclimate/maintainability/:vendor/:package?style=flat-square
[ico-compatibility]: https://img.shields.io/badge/magento-:version-badge-brightgreen.svg?logo=magento&longCache=true&style=flat-square

[link-packagist]: https://packagist.org/packages/:vendor/:package
[link-travis]: https://travis-ci.org/:vendor/:package
[link-scrutinizer]: https://scrutinizer-ci.com/g/:vendor/:package/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/:vendor/:package
[link-maintainability]: https://codeclimate.com/github/:vendor/:package
[link-author]: https://github.com/:author-github
[link-contributors]: ../../contributors
