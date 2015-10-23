FMTinyMCEBundle
================

[TinyMCE](https://github.com/tinymce/tinymce) integration in Symfony2

### Code Quality Assurance ###

| SensioLabs Insight | Travis CI | Scrutinizer CI|
| ------------------------|-------------|-----------------|
|[![SensioLabsInsight](https://insight.sensiolabs.com/projects/604032ab-06ef-4ee2-b0cf-bb5240b9cd17/mini.png)](https://insight.sensiolabs.com/projects/604032ab-06ef-4ee2-b0cf-bb5240b9cd17)|[![Build Status](https://secure.travis-ci.org/helios-ag/FMTinyMCEBundle.png)](http://travis-ci.org/helios-ag/FMElfinderBundle)|[![Build Status](https://scrutinizer-ci.com/g/helios-ag/FMElfinderBundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/helios-ag/FMElfinderBundle/build-status/master) [![Code Coverage](https://scrutinizer-ci.com/g/helios-ag/FMElfinderBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/helios-ag/FMElfinderBundle/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/helios-ag/FMElfinderBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/helios-ag/FMElfinderBundle/?branch=master)

[![Dependency Status](https://www.versioneye.com/user/projects/53db56ae4b3ac897b60001d4/badge.svg?style=flat)](https://www.versioneye.com/user/projects/53db56ae4b3ac897b60001d4)
[![Latest Stable Version](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/v/stable.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle) [![Total Downloads](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/downloads.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle) [![Latest Unstable Version](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/v/unstable.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle) [![License](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/license.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle)
[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/helios-ag/fmtinymcebundle/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

**TinyMCE** is a platform independent web-based JavaScript WYSIWYG HTML editor control released as open source under LGPL.

TinyMCE enables you to convert HTML TEXTAREA fields or other HTML elements to editor instances.

Recommended bundles to use with:

* [FMElfinderBundle](https://github.com/helios-ag/FMElFinderBundle/)

<!-- -->

**Table of contents**

- [Installation](#installation)
    - [Step 1: Installation](#step-1-installation)
    - [Step 2: Enable the bundle](#step-2-enable-the-bundle)
    - [Step 3: Import FMElfinderBundle routing file](#step-3-import-fmtinymcebundle-routing-file)
    - [Step 4: Securing paths](#step-4-configure-your-applications-securityyml)
    - [Step 5: Install assets](#step-5-install-assets)
- [Basic configuration](#basic-configuration)
    - [Add configuration options to your config.yml](#add-configuration-options-to-your-configyml)
    - [Use multiple upload folder by instance](#use-multiple-upload-folder-by-instance)


## Installation

### Step 1: Installation

```sh
    composer require helios-ag/fm-tinymce-bundle
```

Now tell composer to download the bundle by running the command:


```sh
composer update helios-ag/fm-tinymce-bundle
```

### Step 2: Enable the bundle

Enable the bundle in the kernel:

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new FM\TinyMCEBundle\FMTinyMCEBundle(),
    );
}
```

### Step 3: Install assets

Install and dump assets via symfony built-in command:

```sh
app/console assets:install web
```

## Basic configuration

### Add configuration options to your config.yml

```yaml
fm_tinymce:
    instances:
        default:
            locale: %locale% # defaults to current request locale
        my_advanced_configuration:
             locale: ru_RU                   
```
