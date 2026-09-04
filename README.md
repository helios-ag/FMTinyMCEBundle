# FMTinyMCEBundle

MIT-licensed Symfony 7.4/8 bundle for self-hosted [TinyMCE 8 Community Edition](https://www.tiny.cloud/docs/tinymce/latest/).

## Requirements

- PHP 8.2 or newer
- Symfony 7.4 or 8.x
- TinyMCE 8 Community Edition (`tinymce/tinymce:^8.9`)

The bundle is MIT. TinyMCE Community Edition remains GPL-2.0-or-later; use it only where that license is compatible with your application. Tiny Cloud, commercial keys, and premium plugins are not supported.

## Install

```bash
composer require helios-ag/fm-tinymce-bundle
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

```php
use FM\TinyMCEBundle\Form\Type\TinyMCEType;

$builder->add('body', TinyMCEType::class, ['instance' => 'elfinder']);
```

Use `enabled: false` for a plain textarea and `inline: true` on an instance for an inline editor.

## Development

```bash
composer test
composer lint
npm test
```

See [UPGRADE-2.0.md](UPGRADE-2.0.md) for breaking changes.
