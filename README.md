FMTinyMCEBundle
================

[TinyMCE](https://github.com/tinymce/tinymce) integration in Symfony 2/3/4

The purpose of bundle is to provide seamless integration between elFinder and TinyMCE editor.

### Code Quality Assurance ###

| Travis CI | CoverAlls| License | StyleCI | Version Status |
|-------------|-----------------|-----------------|-----------------|-----------------|
|[![Build Status](https://secure.travis-ci.org/helios-ag/FMTinyMCEBundle.png)](http://travis-ci.org/helios-ag/FMTinyMCEBundle)|[![Coverage Status](https://coveralls.io/repos/helios-ag/FMTinyMCEBundle/badge.svg?branch=master&service=github)](https://coveralls.io/github/helios-ag/FMTinyMCEBundle?branch=master)|[![License](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/license.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle)|[![StyleCI](https://styleci.io/repos/44680984/shield)](https://styleci.io/repos/44680984)|[![Latest Stable Version](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/v/stable.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle) [![Latest Unstable Version](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/v/unstable.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle)


| Downloads |
|-----------|
|[![Total Downloads](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/downloads.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle)


**TinyMCE** is a platform independent web-based JavaScript WYSIWYG HTML editor control released as open source under LGPL.

TinyMCE enables you to convert HTML TEXTAREA fields or other HTML elements to editor instances.


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

By default TinyMCE assets copied to `web/assets` directory

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
