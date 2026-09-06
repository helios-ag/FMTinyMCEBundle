# FMTinyMCEBundle

MIT-licensed Symfony 7.4/8 bundle for self-hosted [TinyMCE 8 Community Edition](https://www.tiny.cloud/docs/tinymce/latest/).

### Code Quality Assurance

| Tests | Coverage | License | Version |
|---|---|---|---|
| [![Tests - Linux](https://github.com/helios-ag/FMTinyMCEBundle/actions/workflows/test.yaml/badge.svg)](https://github.com/helios-ag/FMTinyMCEBundle/actions/workflows/test.yaml) | [![Codacy Coverage](https://app.codacy.com/project/badge/Coverage/04a3e972e6af4351b83ccf402b4cebf1)](https://app.codacy.com/gh/helios-ag/FMTinyMCEBundle/dashboard) | [![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE) | [![Latest Stable Version](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/v/stable.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle) |

| Downloads |
|---|
| [![Total Downloads](https://poser.pugx.org/helios-ag/fm-tinymce-bundle/downloads.svg)](https://packagist.org/packages/helios-ag/fm-tinymce-bundle) |

## Requirements

- PHP 8.2 or newer
- Symfony 7.4 or 8.x
- TinyMCE 8 Community Edition (`tinymce/tinymce:^8.9`)

The bundle is MIT. TinyMCE Community Edition remains GPL-2.0-or-later; use it only where that license is compatible with your application. Tiny Cloud, commercial keys, and premium plugins are not supported.

## Install

```bash
composer require helios-ag/fm-tinymce-bundle
```

This bundle does not currently provide a Symfony Flex recipe. Register it in `config/bundles.php`:

```php
<?php

use FM\TinyMCEBundle\FMTinyMCEBundle;

return [
    // ...
    FMTinyMCEBundle::class => ['all' => true],
];
```

Register the bundle installer in the consuming application's root `composer.json`; Composer does not execute scripts supplied by dependencies:

```json
{
  "scripts": {
    "post-install-cmd": ["FM\\TinyMCEBundle\\Composer\\TinyMCEAssetInstaller::copy"],
    "post-update-cmd": ["FM\\TinyMCEBundle\\Composer\\TinyMCEAssetInstaller::copy"]
  }
}
```

Run `composer install` or `composer update` after registering it. The installer mirrors TinyMCE from Composer's vendor directory into `public/assets/tinymce`. Override the destination with root-level `extra.tinymce-dir`.

## Configure

Create `config/packages/fm_tinymce.yaml`. The smallest valid configuration uses the built-in TinyMCE 8 defaults:

```yaml
fm_tinymce:
  instances:
    default: {}
```

The bundle automatically registers `@FMTinyMCE/Form/tinymce_widget.html.twig` as a Twig form theme. Do not add it to `framework.form_themes` yourself.

### Default values

| Option | Default |
|---|---|
| `assets.base_path` | `assets/tinymce` |
| `assets.script_path` | `assets/tinymce/tinymce.min.js` |
| `instances.<name>.enabled` | `true` |
| `instances.<name>.inline` | `false` |
| `instances.<name>.options.language` | `en` |
| `instances.<name>.options.plugins` | `advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table` |
| `instances.<name>.options.toolbar` | `undo redo \| blocks \| bold italic \| link image` |
| `instances.<name>.file_picker.type` | `null` |

Every configuration must contain an instance named `default`. Add other named instances when a form needs different TinyMCE options:

```yaml
fm_tinymce:
  instances:
    default:
      options:
        plugins: [link, image, lists, code]
        toolbar: 'undo redo | bold italic | link image | code'
    elfinder:
      file_picker:
        type: fm_elfinder
        route: elfinder
        route_parameters: { instance: tinymce }
      options: { }
```

### Use in a form

Select an instance with the `instance` form option:

```php
use FM\TinyMCEBundle\Form\Type\TinyMCEType;

$builder->add('body', TinyMCEType::class, [
    'instance' => 'default',
]);
```

Set the form option `enabled: false` to render a plain textarea. Configure `inline: true` on an instance to use TinyMCE inline mode.

### Use with FMElfinderBundle

Configure the matching FMElfinder instance to use its callback editor. The callback name is fixed and must match the runtime API below:

```yaml
fm_elfinder:
  instances:
    tinymce:
      editor: callback
      callback_function: FMTinyMCE.receiveFiles
      connector:
        roots:
          uploads:
            driver: LocalFileSystem
            path: '%kernel.project_dir%/public/uploads'
            url: /uploads
```

Use the `elfinder` instance in the form with `'instance' => 'elfinder'`.

## Development

```bash
composer test
composer lint
npm test
```

See [UPGRADE-2.0.md](UPGRADE-2.0.md) for breaking changes.
