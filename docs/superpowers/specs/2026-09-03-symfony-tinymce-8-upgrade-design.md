# FMTinyMCEBundle 2.0 Upgrade Design

## Purpose

FMTinyMCEBundle 2.0 modernizes the bundle for maintained Symfony versions and
the current TinyMCE Community Edition. The release targets PHP 8.2 or newer,
Symfony 7.4 LTS and Symfony 8.x, and self-hosted TinyMCE 8.9 or newer within the
8.x line.

The bundle source remains MIT-licensed. TinyMCE is an external dependency under
GPL-2.0-or-later. Version 2.0 operates in Community Edition mode and always adds
`license_key: 'gpl'` to the TinyMCE initialization configuration. Commercial
keys, Tiny Cloud, hybrid deployments, and premium plugins are outside this
release's scope.

## Supported versions

The package contract is:

- PHP: `^8.2`;
- Symfony components: `^7.4 || ^8.0`;
- TinyMCE Composer package: `^8.9`;
- Twig: the version selected by the supported Symfony framework packages;
- PHPUnit and development tools: versions compatible with the complete PHP and
  Symfony CI matrix.

The test matrix covers the currently maintained Symfony 7.4 and 8.x branches.
The Composer constraint permits future compatible Symfony 8 minor releases;
CI explicitly tests the current stable Symfony 8 release and may be advanced as
new 8.x minors are released.

## Release and compatibility policy

This work is released as version 2.0 because it intentionally removes the
Symfony 2-6 compatibility layer and changes the bundle configuration contract.
Version 1.x applications must follow `UPGRADE-2.0.md`; legacy configuration is
not silently normalized.

Version 2.0 removes:

- `symfony/templating` and the `templating.helper` integration;
- the dependency on Sensio DistributionBundle's Composer script handler;
- XML dependency-injection service definitions;
- version checks and compatibility methods for old Symfony Form APIs;
- TinyMCE plugins and options removed between TinyMCE 5 and TinyMCE 8;
- arbitrary JavaScript callbacks embedded as YAML strings.

The existing public ideas remain recognizable: named editor instances, a form
type, per-field enable/disable behavior, inline editing, self-hosted assets, and
FMElfinderBundle file picking.

## Architecture

### Bundle extension and services

`FMTinyMCEExtension` loads one PHP dependency-injection configuration file. The
PHP file registers the form type, Twig extension, configuration resolver, and
Elfinder integration service. It replaces `form.xml`, `templating.xml`, and
`twig.xml`; the XML files are deleted.

The old `TinyMCEHelper` no longer extends a Symfony class. Its rendering and
configuration responsibilities are separated:

- an instance configuration resolver selects and validates a named instance;
- an initialization builder combines the selected instance, field-specific
  options, generated selector, asset URLs, and the fixed GPL license mode;
- Twig serializes the resulting array with JSON-safe options and emits the
  initialization script.

These services depend on interfaces such as `Packages` and
`UrlGeneratorInterface`, not concrete router implementations.

### Form integration

`TinyMCEType` extends `AbstractType` and returns `TextareaType::class` from
`getParent()`. It implements only APIs supported by Symfony 7.4 and 8.x:
`configureOptions()`, `buildForm()`, `buildView()`, and `getBlockPrefix()` where
needed.

The field exposes two bundle-specific options:

- `enabled` (`bool`, default `true`) determines whether TinyMCE is initialized;
- `instance` (`string`, default `default`) selects a configured profile.

Inline editing is configured in the selected instance. The form view receives
only normalized values required by the form theme. Unknown instances fail with
an `InvalidOptionsException` containing the requested name and available names.

The form theme uses modern Twig bundle notation and is registered through the
`twig.form_themes` configuration mechanism rather than the legacy
`twig.form.resources` parameter compiler pass. The final resource name is
`@FMTinyMCE/Form/tinymce_widget.html.twig`.

### TinyMCE initialization

The bundle generates a PHP array and serializes it as JSON instead of joining
JavaScript fragments. The builder always injects:

```json
{
  "license_key": "gpl",
  "selector": "#generated-form-field-id"
}
```

Application options cannot override either value. JSON encoding must escape
HTML-significant characters so values containing `</script>`, quotes, Unicode,
or user-controlled text cannot break out of the script element.

TinyMCE options are otherwise passed through from the `options` mapping. Values
must be JSON-compatible scalars, lists, and maps. PHP objects, resources, and
raw JavaScript expressions are rejected during configuration normalization.

### Assets

TinyMCE is self-hosted from the `tinymce/tinymce` Composer dependency. Default
paths are:

```yaml
assets:
    base_path: 'assets/tinymce'
    script_path: 'assets/tinymce/tinymce.min.js'
```

The legacy script handler inherited from Sensio DistributionBundle is deleted.
A standalone Composer script handler copies the distributable TinyMCE directory
into `public/assets/tinymce`, supports an explicitly configured destination,
uses Composer's vendor directory rather than assuming `vendor/`, and is
idempotent. It does not perform network access. Documentation also explains how
applications with their own frontend pipeline can omit the copy hook and point
`script_path` at their built asset.

Failure to locate the TinyMCE package or write the destination produces a
non-zero Composer script result with the resolved source and destination paths.

### FMElfinderBundle integration

The only supported callback integration in 2.0 is a controlled adapter selected
with `file_picker.type: fm_elfinder`. The adapter generates the configured route
through `UrlGeneratorInterface`, passes route parameters, opens the picker, and
returns the selected URL through TinyMCE 8's `file_picker_callback` contract.

The bundle does not accept callback function bodies from YAML. Applications
requiring custom JavaScript callbacks register them in their own frontend code
and extend the generated TinyMCE configuration outside the bundle.

## Configuration contract

The canonical configuration is:

```yaml
fm_tinymce:
    assets:
        base_path: 'assets/tinymce'
        script_path: 'assets/tinymce/tinymce.min.js'

    instances:
        default:
            enabled: true
            inline: false
            options:
                language: en
                width: 600
                height: 300
                menubar: 'file edit insert view format table tools'
                plugins:
                    - advlist
                    - autolink
                    - lists
                    - link
                    - image
                    - charmap
                    - preview
                    - anchor
                    - searchreplace
                    - visualblocks
                    - code
                    - fullscreen
                    - insertdatetime
                    - media
                    - table
                toolbar: 'undo redo | blocks | bold italic | link image'
            file_picker:
                type: fm_elfinder
                route: elfinder
                route_parameters:
                    instance: tinymce
```

`instances` must contain a `default` profile. Each instance supports:

- `enabled`: profile-level default, overridable by the form field;
- `inline`: TinyMCE inline mode;
- `options`: JSON-compatible TinyMCE 8 options;
- `file_picker`: optional controlled integration configuration.

The default plugins exclude plugins removed in TinyMCE 6-8, including `print`,
`paste`, `contextmenu`, `textcolor`, `template`, `fullpage`, `bbcode`,
`legacyoutput`, `tabfocus`, and `spellchecker`. Explicit use of a known removed
plugin fails during container compilation and names the replacement or removal
reason. Unknown plugin names are allowed because applications may ship custom
Community Edition plugins.

`UPGRADE-2.0.md` maps every 1.x setting to its 2.0 equivalent. The guide calls
out settings that cannot be migrated, particularly raw callbacks, removed
plugins, `theme: modern`, `toolbar_item_size`, and the old nested `templates`
shape.

## User scenarios and acceptance criteria

### Default self-hosted editor

Given an application with the minimal bundle configuration and installed
assets, rendering a `TinyMCEType` field loads the local TinyMCE 8 script and
creates one editable instance. Its configuration contains `license_key: gpl`
and makes no request to Tiny Cloud.

### Named profiles

Given `default` and `compact` profiles, fields selecting each profile receive
the corresponding plugins, toolbar, dimensions, language, and inline mode. A
field requesting an absent profile fails before template rendering and lists the
valid profile names.

### Disabled field

Given `enabled: false` at field level, the form renders a normal textarea, does
not load or initialize TinyMCE for that field, and submits its value normally.

### Inline mode

Given an instance with `inline: true`, the rendered target is valid for TinyMCE
inline mode, initializes successfully, and synchronizes edited content back to
the form field before submission.

### FMElfinder file selection

Given an instance with the FMElfinder picker and route parameters, invoking the
image picker opens the generated route. Selecting a file returns its URL through
the TinyMCE 8 callback without changing CKEditor or other FMElfinder behavior.

### Multiple fields

Given two TinyMCE fields on one page, each receives a unique selector and exactly
one editor instance. Loading the TinyMCE script is deduplicated.

### Safe configuration rendering

Given configuration containing quotes, non-ASCII text, HTML, or `</script>`, the
page remains syntactically valid, the value reaches TinyMCE unchanged, and no
additional script executes.

### Asset installation

Given a non-standard Composer vendor directory and a configured public
destination, the asset installer copies TinyMCE 8 completely, can be run twice,
and leaves an identical result on the second run. Missing sources and unwritable
destinations fail with actionable diagnostics.

### Invalid and obsolete configuration

Given a removed 1.x option or a known removed TinyMCE plugin, container
compilation fails with the exact configuration path and migration guidance.

## Error handling

Configuration errors use Symfony Config's invalid-configuration exceptions so
they include the full YAML path. Runtime option errors use Symfony OptionsResolver
exceptions. Asset installation converts filesystem failures into a Composer
script failure with contextual paths. No error path silently falls back to a
different editor profile, CDN asset, commercial mode, or remote service.

## Test strategy

### Unit tests

Fast PHPUnit tests cover:

- configuration defaults and normalization;
- all invalid configuration branches;
- the removed-plugin diagnostic map;
- instance selection and option precedence;
- form type defaults, overrides, inline mode, and disabled mode;
- URL generation for the FMElfinder adapter;
- JSON serialization and script-breakout payloads;
- asset source/destination resolution and idempotent copying using temporary
  directories.

Framework classes are not mocked when their real lightweight implementation is
available. Only owned collaborators and injected interfaces are mocked.

### Kernel integration tests

A minimal test kernel verifies:

- the bundle and PHP service config compile on supported Symfony versions;
- the form type, Twig extension, resolver, and picker adapter are registered;
- the form theme is active;
- no service references Symfony Templating or Sensio DistributionBundle;
- route generation and Twig rendering work together;
- `lint:container` and `lint:twig` report no errors or deprecations.

### Functional tests

A fixture application renders and submits real Symfony forms. HTTP-level tests
cover default, named, disabled, inline, multiple-field, invalid-instance, and
FMElfinder-route scenarios. Assertions focus on rendered contracts and submitted
values rather than private service internals.

### JavaScript and browser tests

Small JavaScript unit tests exercise script-load deduplication and the Elfinder
callback with a mocked `tinymce` global. A Playwright smoke suite serves the
fixture application with the actual self-hosted TinyMCE 8 assets and verifies:

- editor creation;
- content editing and form synchronization;
- named and inline profiles;
- disabled fields;
- two editors on one page;
- FMElfinder callback insertion;
- absence of browser console errors, license warnings, failed assets, and
  outbound Tiny Cloud requests.

## CI matrix and quality gates

The GitHub Actions matrix includes:

- PHP 8.2 with Symfony 7.4 and `--prefer-lowest`;
- PHP 8.2 and 8.3 with the latest Symfony 7.4 dependencies;
- PHP 8.4 and 8.5 with the latest Symfony 7.4 dependencies;
- PHP 8.4 and 8.5 with the current stable Symfony 8.x dependencies.

Composer constraint jobs use explicit Symfony version constraints so a green job
proves the intended branch rather than whatever Composer happens to select.
Jobs run without coverage except for one current-platform coverage job.

Required checks are:

```shell
composer validate --strict
composer audit
vendor/bin/php-cs-fixer check --diff
vendor/bin/phpstan analyse
SYMFONY_DEPRECATIONS_HELPER=max[total]=0 vendor/bin/simple-phpunit
php bin/console lint:container
php bin/console lint:twig src/Resources/views
npm test
npx playwright test
npm audit --audit-level=high
```

The release workflow additionally installs the bundle through a Composer path
repository into clean Symfony 7.4 and current Symfony 8.x fixture applications,
runs asset installation, compiles each container, renders the sample form, and
checks the contents of `composer archive`.

## Documentation and migration deliverables

The release includes:

- an updated README for Symfony 7.4/8.x and TinyMCE 8 Community Edition;
- a complete minimal installation example;
- examples for multiple profiles, disabled and inline fields, custom asset
  paths, and FMElfinder;
- explicit GPL Community Edition disclosure and `license_key: gpl` behavior;
- `UPGRADE-2.0.md` with a key-by-key migration table and before/after examples;
- a changelog entry listing all removed APIs and configuration keys;
- contributor commands matching CI.

## Definition of done

FMTinyMCEBundle 2.0 is ready to tag when:

- every required CI job passes on the minimum and maximum supported dependency
  sets;
- PHPUnit reports no direct or indirect Symfony deprecations;
- the real-browser acceptance scenarios pass without external network access;
- the clean Symfony 7.4 and Symfony 8.x installations reproduce the documented
  setup;
- the package contains no XML DI config, Symfony Templating reference, Sensio
  DistributionBundle reference, or legacy Symfony-version branch;
- the generated JavaScript passes the injection regression tests;
- the upgrade guide accounts for every public 1.x configuration key and service
  intentionally removed or renamed;
- the package archive contains only intended distributable files and retains the
  MIT license notice while documenting TinyMCE's separate GPL license.
