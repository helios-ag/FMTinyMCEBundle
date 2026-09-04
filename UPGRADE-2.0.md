# Upgrade to 2.0

2.0 requires PHP 8.2+, Symfony 7.4/8.x, and TinyMCE 8 Community Edition.

- Move flat profile settings into `instances.<name>.options`.
- Replace `base_path` and `js_path` with `assets.base_path` and `assets.script_path`.
- Replace `filebrowser_*` and JavaScript `file_picker_callback` with `file_picker.type: fm_elfinder`, route, and route parameters.
- Remove legacy plugins (`print`, `paste`, `contextmenu`, `template`, `fullpage`, and `spellchecker`) and `theme: modern`.
- Replace Symfony Templating calls with `tinymce_configuration()` and `tinymce_asset_url()`.
- Register `FM\\TinyMCEBundle\\Composer\\TinyMCEAssetInstaller::copy` in the root application's `post-install-cmd` and `post-update-cmd` Composer scripts.

The bundle remains MIT. TinyMCE Community Edition is GPL-2.0-or-later and always receives `license_key: gpl`.
