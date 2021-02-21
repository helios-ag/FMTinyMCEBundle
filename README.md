FMTinyMCEBundle
================

[TinyMCE](https://github.com/tinymce/tinymce) integration in Symfony

The purpose of bundle is to provide seamless integration between elFinder and TinyMCE editor.

### Code Quality Assurance ###

| Travis CI | CoverAlls| License | StyleCI | Version Status |
|-------------|-----------------|-----------------|-----------------|-----------------|
|[![Build Status](https://travis-ci.com/helios-ag/FMTinyMCEBundle.svg?branch=master)](https://travis-ci.com/helios-ag/FMTinyMCEBundle)|[![Coverage Status](https://coveralls.io/repos/helios-ag/FMTinyMCEBundle/badge.svg?branch=master&service=github)](https://coveralls.io/github/helios-ag/FMTinyMCEBundle?branch=master)|[![License](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/license.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle)|[![StyleCI](https://styleci.io/repos/44680984/shield)](https://styleci.io/repos/44680984)|[![Latest Stable Version](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/v/stable.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle) [![Latest Unstable Version](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/v/unstable.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle)


| Downloads |
|-----------|
|[![Total Downloads](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/downloads.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle)


**TinyMCE** is a platform independent web-based JavaScript WYSIWYG HTML editor control released as open source under LGPL.

TinyMCE enables you to convert HTML TEXTAREA fields or other HTML elements to editor instances.


**Table of contents**

- [Installation](#installation)
    - [Step 1: Installation](#step-1-installation)
    - [Step 2: Enable the bundle](#step-2-enable-the-bundle)
- [Basic configuration](#basic-configuration)
    - [Add configuration options to your config.yml](#add-configuration-options-to-your-configyml)

## Installation

### Step 1: Installation

Add FMTinyMCEBundle to your composer.json:

```json
{
    "require": {
        "helios-ag/fm-tinymce-bundle": "~1"
    }
}
```

If you want to override default assets directory of Richfilemanager, add next option.
By default, assets copied to `web/assets/tinymce` or `public/assets/tinymce`
depending on Symfony version

```json
{
    "config": {
        "tinymce-dir": "web/assets/"
    }
}
```

Add composer script

`"FM\\TinyMCEBundle\\Composer\\TinyMCEScriptHandler::copy",`

to scripts section of composer.json

```json
{
  "scripts": {
      "symfony-scripts": [
          "Incenteev\\ParameterHandler\\ScriptHandler::buildParameters",
          "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::buildBootstrap",
          "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::clearCache",
          "FM\\TinyMCEBundle\\Composer\\TinyMCEcriptHandler::copy",
          "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installAssets",
          "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::installRequirementsFile",
          "Sensio\\Bundle\\DistributionBundle\\Composer\\ScriptHandler::prepareDeploymentTarget"
      ]
    }
}
```

Now tell the composer to download the bundle by running the command:

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


## Basic configuration

### Add configuration options to your config.yml

```yaml
fm_tinymce:
    instances:
        first_instance:
            language: en_US
            width: 300
            height: 400
        my_advanced_configuration:
             locale: ru_RU
```

##Advanced Configuration

To make story short, here example of Integration between TinyMCE and Elfinder bundles

```yaml
fm_tinymce:
    instances:            # Required
        elfinder:
            language: ru
            image_advtab:         true
            file_picker_callback: elFinderBrowser
            filebrowser_type:     fm_elfinder
            filebrowser:
                route:                elfinder
                route_parameters:
                    instance: tinymce

```

and configuration for ElFinderBrowser

```yaml
fm_elfinder:
    instances:
        tinymce:
            language: ru
            editor: tinymce4 #
            include_assets: true
            relative_path: true
            connector:
                roots:       # at least one root must be defined
                    uploads:
                        show_hidden: false
                        driver: LocalFileSystem
                        path: uploads
                        upload_allow: ['all']
```

Full configuration reference example


```yaml
fm_tinymce:
    enable:               true
    inline:               false
    base_path:            assets/tinymce/
    js_path:              assets/tinymce/tinymce.min.js
    instances:
        default:
            language:             en_US
            width:                600
            height:               300
            theme:                modern
            toolbar_item_size:    small
            menubar:              file edit insert view format table tools
            image_advtab:         false
            templates:
                templates:
                    title:                ~
                    content:              ~
            plugins:              ""
            relative_urls:        false
            convert_urls:         false
            toolbars:
                toolbar1:         undo redo | styleselect | bold italic | link image
            filebrowser_type:     fm_elfinder
            file_picker_callback: elFinderBrowser
            filebrowser:
                url:                  http://localhost/elfinder
                route:                elfinder
                route_parameters:
                    instance: default
