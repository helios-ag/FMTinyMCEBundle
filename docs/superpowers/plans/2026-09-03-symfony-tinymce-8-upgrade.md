# FMTinyMCEBundle 2.0 Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Release FMTinyMCEBundle 2.0: an MIT-licensed Symfony 7.4/8.x bundle for PHP 8.2+ that integrates the self-hosted TinyMCE 8 Community Edition safely and without legacy Symfony dependencies.

**Architecture:** Replace the Templating helper, legacy compiler pass, XML service files, and Sensio script handler with PHP DI configuration, focused configuration services, a JSON-only Twig API, and a standalone asset installer. The form theme delegates initialization to a small browser runtime so multiple fields share one TinyMCE script load. The public configuration is intentionally redesigned around named profiles and an `options` map.

**Tech Stack:** PHP 8.2+, Symfony 7.4/8.x, Twig 3, Symfony Form/Config/Asset/Filesystem, Composer, PHPUnit via Symfony PHPUnit Bridge, PHPStan, PHP-CS-Fixer, Node.js test runner, Playwright, TinyMCE 8.9 Community Edition.

---

## Working rules

- Keep the source license as MIT. Do not copy TinyMCE files into `src/` or alter
  the TinyMCE license.
- Support self-hosted Community Edition only. Initialization must always contain
  `license_key: 'gpl'`; do not add support for Tiny Cloud, commercial keys, or
  premium plugins.
- Do not retain a deprecated compatibility branch. Every removed 1.x setting
  gets an explicit migration note in `UPGRADE-2.0.md`.
- Write the named PHPUnit test first, observe the expected failure, then add the
  smallest implementation that makes it pass.
- Run `git diff --check` before every commit. Do not modify unrelated files.

## Target file map

| Path | Action | Responsibility |
| --- | --- | --- |
| `composer.json` | Modify | PHP/Symfony/TinyMCE constraints, scripts, dev tooling, branch alias |
| `src/DependencyInjection/Configuration.php` | Replace | 2.0 schema and defaults |
| `src/DependencyInjection/FMTinyMCEExtension.php` | Modify | PHP services loader, parameters, Twig form-theme prepend |
| `src/Resources/config/services.php` | Create | Service definitions for the modern bundle |
| `src/Configuration/InstanceConfigurationResolver.php` | Create | Select and validate named profiles |
| `src/Configuration/TinyMCEConfigurationBuilder.php` | Create | Build safe TinyMCE init arrays |
| `src/FilePicker/ElfinderFilePicker.php` | Create | Route-based, controlled file-picker configuration |
| `src/Form/Type/TinyMCEType.php` | Replace | Modern Symfony form type |
| `src/Twig/TinyMCEExtension.php` | Replace | Twig functions for init configuration and asset URLs |
| `src/Resources/views/Form/tinymce_widget.html.twig` | Replace | Normal and inline form markup |
| `src/Resources/public/fm-tinymce.js` | Create | One-time script loader, editor initialization, textarea sync |
| `src/Composer/TinyMCEAssetInstaller.php` | Create | Standalone idempotent asset copy command |
| `src/Composer/TinyMCEScriptHandler.php` | Delete | Sensio DistributionBundle-based legacy code |
| `src/Templating/TinyMCEHelper.php` | Delete | Symfony Templating dependency |
| `src/DependencyInjection/Compiler/TwigFormPass.php` | Delete | Legacy form-resource parameter mutation |
| `src/Resources/config/*.xml` | Delete | XML DI service definitions |
| `src/Resources/views/elfinder_helper.html.twig` | Delete | Raw-JavaScript callback helper |
| `tests/Configuration/*` | Create | Resolver and init-builder unit tests |
| `tests/FilePicker/*` | Create | Route adapter tests |
| `tests/Composer/*` | Create | Asset installer tests |
| `tests/Form/Type/TinyMCETypeTest.php` | Replace | Modern form-type behavior tests |
| `tests/Twig/TinyMCEExtensionTest.php` | Create | JSON and asset URL tests |
| `tests/Functional/*` | Create | Kernel and HTTP fixture application tests |
| `tests/JavaScript/*` | Create | Runtime unit tests |
| `tests/Browser/*` | Create | Playwright acceptance tests |
| `package.json`, `package-lock.json`, `playwright.config.mjs` | Create | JavaScript/browser test tooling |
| `.github/workflows/test.yaml` | Replace | PHP/Symfony matrix and quality gates |
| `README.md`, `UPGRADE-2.0.md`, `CHANGELOG.md`, `CONTRIBUTING.md` | Modify/Create | Installation, migration, release and contributor documentation |

## Task 1: Establish the 2.0 dependency baseline and test tooling

**Files:**

- Modify: `composer.json`
- Modify: `phpunit.xml.dist`
- Create: `phpstan.neon.dist`
- Create: `.php-cs-fixer.dist.php`
- Test: `composer validate --strict`

- [ ] **Step 1: Add the Composer-constraint regression test matrix as a CI design check.**

  Add these intended constraints to a temporary test table in the pull request
  description or issue before editing Composer:

  ```text
  PHP 8.2 + Symfony 7.4 + TinyMCE 8.9: must resolve
  PHP 8.4 + Symfony 8.1 + TinyMCE 8.9: must resolve
  PHP 8.1 + this package: must not resolve
  Symfony 6.4 + this package: must not resolve
  TinyMCE 7.x + this package: must not resolve
  ```

- [ ] **Step 2: Update `composer.json` to the new public contract.**

  Replace the relevant sections with:

  ```json
  "require": {
      "php": "^8.2",
      "symfony/asset": "^7.4 || ^8.0",
      "symfony/config": "^7.4 || ^8.0",
      "symfony/dependency-injection": "^7.4 || ^8.0",
      "symfony/filesystem": "^7.4 || ^8.0",
      "symfony/form": "^7.4 || ^8.0",
      "symfony/framework-bundle": "^7.4 || ^8.0",
      "symfony/routing": "^7.4 || ^8.0",
      "symfony/twig-bundle": "^7.4 || ^8.0",
      "tinymce/tinymce": "^8.9"
  },
  "require-dev": {
      "friendsofphp/php-cs-fixer": "^3.0",
      "matthiasnoback/symfony-config-test": "^6.0",
      "matthiasnoback/symfony-dependency-injection-test": "^6.0",
      "phpstan/phpstan": "^2.0",
      "symfony/phpunit-bridge": "^7.4 || ^8.0"
  },
  "scripts": {
      "test": "simple-phpunit",
      "lint": [
          "php-cs-fixer check --diff",
          "phpstan analyse"
      ],
      "copy-tinymce-assets": "FM\\TinyMCEBundle\\Composer\\TinyMCEAssetInstaller::copy"
  }
  ```

  Remove `symfony/templating` and every Sensio package reference. Change the
  branch alias to `dev-main: 2.0-dev`, retain `"license": "MIT"`, and replace
  `minimum-stability: dev` with `minimum-stability: stable` plus
  `prefer-stable: true`.

- [ ] **Step 3: Update PHPUnit configuration before adding tests.**

  Replace the obsolete PHPUnit 6 schema, listeners, whitelist, and
  `SYMFONY_DEPRECATIONS_HELPER=weak` with the current schema and strict
  deprecations:

  ```xml
  <phpunit bootstrap="tests/bootstrap.php" colors="true" cacheDirectory=".phpunit.cache">
      <php>
          <env name="SYMFONY_DEPRECATIONS_HELPER" value="max[total]=0"/>
      </php>
      <testsuites>
          <testsuite name="FMTinyMCEBundle">
              <directory suffix="Test.php">tests</directory>
          </testsuite>
      </testsuites>
      <source>
          <include><directory suffix=".php">src</directory></include>
      </source>
  </phpunit>
  ```

  Create `tests/bootstrap.php` with `require dirname(__DIR__).'/vendor/autoload.php';`
  and delete `tests/autoload.php` only after all tests use the new bootstrap.

- [ ] **Step 4: Add static-analysis and formatting configuration.**

  Create `phpstan.neon.dist`:

  ```neon
  parameters:
      level: max
      paths:
          - src
          - tests
      reportUnmatchedIgnoredErrors: true
  ```

  Create `.php-cs-fixer.dist.php` with PHP 8.2 syntax, Symfony conventions, and
  `declare_strict_types` excluded unless adopted consistently across all source
  files in a dedicated formatting commit.

- [ ] **Step 5: Validate the baseline.**

  Run:

  ```bash
  composer validate --strict
  composer update --with-all-dependencies --no-interaction
  vendor/bin/simple-phpunit --version
  ```

  Expected: Composer reports a valid package and PHPUnit is installed. Existing
  behavior tests may fail at this stage; record the failures without attempting
  to preserve removed APIs.

- [ ] **Step 6: Commit the dependency baseline.**

  ```bash
  git add composer.json composer.lock phpunit.xml.dist tests/bootstrap.php phpstan.neon.dist .php-cs-fixer.dist.php
  git commit -m "build: target Symfony 7.4 and TinyMCE 8"
  ```

## Task 2: Define and test the 2.0 configuration schema

**Files:**

- Modify: `src/DependencyInjection/Configuration.php`
- Create: `tests/DependencyInjection/ConfigurationTest.php`
- Create: `tests/Fixtures/config/valid.yaml`
- Create: `tests/Fixtures/config/invalid-removed-plugin.yaml`
- Create: `tests/Fixtures/config/invalid-legacy-option.yaml`

- [ ] **Step 1: Write failing schema tests.**

  Test the normalized value of this input:

  ```yaml
  fm_tinymce:
      instances:
          default:
              options:
                  language: en
                  plugins: [link, image]
                  toolbar: 'undo redo | link image'
  ```

  Assert defaults for `assets.base_path`, `assets.script_path`,
  `instances.default.enabled`, `instances.default.inline`, and the fixed
  default plugin list. Add data-provider cases asserting that `print`, `paste`,
  `contextmenu`, `template`, and `theme: modern` raise
  `InvalidConfigurationException` containing both the invalid path and `TinyMCE 8`.

- [ ] **Step 2: Run the test to verify it fails.**

  ```bash
  vendor/bin/simple-phpunit tests/DependencyInjection/ConfigurationTest.php -v
  ```

  Expected: FAIL because the legacy tree has no `assets` or `options` node and
  accepts obsolete settings.

- [ ] **Step 3: Replace the configuration tree.**

  Implement the root shape with the following essential nodes:

  ```php
  $rootNode
      ->children()
          ->arrayNode('assets')
              ->addDefaultsIfNotSet()
              ->children()
                  ->scalarNode('base_path')->defaultValue('assets/tinymce')->end()
                  ->scalarNode('script_path')->defaultValue('assets/tinymce/tinymce.min.js')->end()
              ->end()
          ->end()
          ->arrayNode('instances')
              ->useAttributeAsKey('name')
              ->arrayPrototype()
                  ->children()
                      ->booleanNode('enabled')->defaultTrue()->end()
                      ->booleanNode('inline')->defaultFalse()->end()
                      ->variableNode('options')->defaultValue(self::DEFAULT_OPTIONS)->end()
                      ->arrayNode('file_picker')
                          ->addDefaultsIfNotSet()
                          ->children()
                              ->enumNode('type')->values([null, 'fm_elfinder'])->defaultNull()->end()
                              ->scalarNode('route')->defaultNull()->end()
                              ->arrayNode('route_parameters')->variablePrototype()->end()->end()
                          ->end()
                      ->end()
                  ->end()
              ->end()
          ->end()
      ->end();
  ```

  Define `DEFAULT_OPTIONS` as the Community Edition TinyMCE 8 plugins from the
  design: `advlist`, `autolink`, `lists`, `link`, `image`, `charmap`, `preview`,
  `anchor`, `searchreplace`, `visualblocks`, `code`, `fullscreen`,
  `insertdatetime`, `media`, and `table`. Require a `default` instance in a
  post-normalization validation. Reject known removed option keys and known
  removed plugins with specific migration text.

- [ ] **Step 4: Add recursive JSON-value validation.**

  Add a private `assertJsonCompatible(mixed $value, string $path): void` used
  by the `options` validation closure. It accepts `null`, booleans, integers,
  floats, strings, and nested arrays with string/integer keys. It throws
  `InvalidConfigurationException` for objects, resources, closures, and
  non-finite floats. It rejects `file_picker_callback` inside `options` and
  directs consumers to `file_picker.type: fm_elfinder` or their own frontend
  integration.

- [ ] **Step 5: Run the schema test and full DI suite.**

  ```bash
  vendor/bin/simple-phpunit tests/DependencyInjection/ConfigurationTest.php -v
  vendor/bin/simple-phpunit tests/DependencyInjection -v
  ```

  Expected: PASS; legacy fixture tests are replaced rather than adapted.

- [ ] **Step 6: Commit the schema.**

  ```bash
  git add src/DependencyInjection/Configuration.php tests/DependencyInjection/ConfigurationTest.php tests/Fixtures/config
  git commit -m "feat: define TinyMCE 8 configuration schema"
  ```

## Task 3: Build normalized instance and initialization services

**Files:**

- Create: `src/Configuration/InstanceConfigurationResolver.php`
- Create: `src/Configuration/TinyMCEConfigurationBuilder.php`
- Create: `tests/Configuration/InstanceConfigurationResolverTest.php`
- Create: `tests/Configuration/TinyMCEConfigurationBuilderTest.php`

- [ ] **Step 1: Write failing resolver tests.**

  Cover selection of `default` and `compact`, a missing profile, field-level
  `enabled` override, and preservation of nested JSON options. The missing
  profile assertion must include `Unknown TinyMCE instance "missing"` and the
  sorted list `compact, default`.

- [ ] **Step 2: Implement `InstanceConfigurationResolver`.**

  Use an immutable constructor and a single public method:

  ```php
  final class InstanceConfigurationResolver
  {
      /** @param array<string, array<string, mixed>> $instances */
      public function __construct(private readonly array $instances) {}

      /** @return array<string, mixed> */
      public function resolve(string $name, ?bool $enabledOverride = null): array
      {
          if (!isset($this->instances[$name])) {
              $names = array_keys($this->instances);
              sort($names);
              throw new \InvalidArgumentException(sprintf(
                  'Unknown TinyMCE instance "%s". Available instances: %s.',
                  $name,
                  implode(', ', $names),
              ));
          }

          $instance = $this->instances[$name];
          if (null !== $enabledOverride) {
              $instance['enabled'] = $enabledOverride;
          }

          return $instance;
      }
  }
  ```

  Use the exact Symfony `InvalidOptionsException` at the form boundary; the
  resolver remains framework-light and throws `InvalidArgumentException`.

- [ ] **Step 3: Write failing builder tests.**

  Assert that a built array contains `selector`, `license_key => 'gpl'`,
  `inline`, asset-derived `base_url`, and all profile options. Add a malicious
  option value `'</script><script>window.pwned=1</script>'` and assert the JSON
  returned by `encodeForScript()` contains `\u003C/script\u003E` and decodes to
  the original value. Add a test that `license_key` and `selector` supplied by
  application options cannot replace bundle-owned values.

- [ ] **Step 4: Implement `TinyMCEConfigurationBuilder`.**

  Give it this focused API:

  ```php
  final class TinyMCEConfigurationBuilder
  {
      public function __construct(
          private readonly InstanceConfigurationResolver $instances,
          private readonly Packages $packages,
          private readonly ElfinderFilePicker $filePicker,
          private readonly string $basePath,
      ) {}

      /** @return array<string, mixed> */
      public function build(string $fieldId, string $instanceName, ?bool $enabledOverride = null): array;

      /** @param array<string, mixed> $configuration */
      public function encodeForScript(array $configuration): string;
  }
  ```

  Start with `$configuration = $instance['options'];`, remove any user supplied
  `selector`, `license_key`, and `file_picker_callback`, add bundle-owned values
  last, and encode with:

  ```php
  json_encode(
      $configuration,
      JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT,
  );
  ```

  Convert `JsonException` into `LogicException` with the instance name. Do not
  concatenate JavaScript fragments.

- [ ] **Step 5: Run focused tests.**

  ```bash
  vendor/bin/simple-phpunit tests/Configuration -v
  ```

  Expected: PASS, including script-breakout and protected-key tests.

- [ ] **Step 6: Commit the configuration services.**

  ```bash
  git add src/Configuration tests/Configuration
  git commit -m "feat: build safe TinyMCE 8 initialization configs"
  ```

## Task 4: Implement the controlled FMElfinder file-picker adapter

**Files:**

- Create: `src/FilePicker/ElfinderFilePicker.php`
- Create: `tests/FilePicker/ElfinderFilePickerTest.php`
- Delete: `src/Resources/views/elfinder_helper.html.twig`
- Delete: `src/Templating/TinyMCEHelper.php`
- Delete: `tests/Templating/TinyMCEHelperTest.php`

- [ ] **Step 1: Write failing adapter tests.**

  Mock `UrlGeneratorInterface`; assert it receives route `elfinder`, parameters
  `['instance' => 'tinymce']`, and `UrlGeneratorInterface::ABSOLUTE_PATH`.
  Assert the returned configuration contains only this structured value:

  ```php
  ['fm_elfinder_url' => '/elfinder?instance=tinymce']
  ```

  Add cases for `type: null`, `type: fm_elfinder` without a route, and a
  non-string route parameter. The error must name `fm_tinymce.instances.<name>.file_picker`.

- [ ] **Step 2: Implement `ElfinderFilePicker`.**

  Use `UrlGeneratorInterface`, not `Router`. Return an empty array for no
  picker; reject incomplete FMElfinder configuration; never return a JavaScript
  callback string. The initialization builder merges this result into its JSON
  configuration only for the supported picker type.

- [ ] **Step 3: Remove the old Templating integration only after the new tests pass.**

  Delete `TinyMCEHelper`, its test, and `elfinder_helper.html.twig`. Search must
  become empty:

  ```bash
  rg -n 'Symfony\\Component\\Templating|TinyMCEHelper|templating.helper|file_picker_callback:' src tests
  ```

- [ ] **Step 4: Verify focused behavior.**

  ```bash
  vendor/bin/simple-phpunit tests/FilePicker tests/Configuration -v
  ```

  Expected: PASS and no remaining Symfony Templating reference.

- [ ] **Step 5: Commit the adapter and removal.**

  ```bash
  git add src/FilePicker src/Configuration/TinyMCEConfigurationBuilder.php tests/FilePicker
  git rm src/Templating/TinyMCEHelper.php src/Resources/views/elfinder_helper.html.twig tests/Templating/TinyMCEHelperTest.php
  git commit -m "feat: replace raw file callbacks with Elfinder adapter"
  ```

## Task 5: Modernize DI registration and form-theme wiring

**Files:**

- Create: `src/Resources/config/services.php`
- Modify: `src/DependencyInjection/FMTinyMCEExtension.php`
- Modify: `src/FMTinyMCEBundle.php`
- Delete: `src/DependencyInjection/Compiler/TwigFormPass.php`
- Delete: `src/Resources/config/form.xml`
- Delete: `src/Resources/config/templating.xml`
- Delete: `src/Resources/config/twig.xml`
- Delete: `tests/DependencyInjection/Compiler/TwigFormPassTest.php`
- Modify: `tests/DependencyInjection/TinyMCEExtensionTest.php`
- Create: `tests/Functional/ContainerCompilationTest.php`

- [ ] **Step 1: Write failing container tests.**

  Boot a minimal Symfony 7.4-compatible kernel and assert these service ids:

  ```php
  self::assertTrue(self::getContainer()->has(TinyMCEType::class));
  self::assertTrue(self::getContainer()->has(TinyMCEExtension::class));
  self::assertTrue(self::getContainer()->has(TinyMCEConfigurationBuilder::class));
  ```

  Assert `twig.form.resources` is not read or mutated, the Twig configuration
  has `@FMTinyMCE/Form/tinymce_widget.html.twig`, and compilation succeeds with
  no service class under `Symfony\Component\Templating`.

- [ ] **Step 2: Add PHP service configuration.**

  Define services in `src/Resources/config/services.php` using
  `ContainerConfigurator`:

  ```php
  return static function (ContainerConfigurator $container): void {
      $services = $container->services()->defaults()->autowire()->autoconfigure();

      $services->set(InstanceConfigurationResolver::class)
          ->arg('$instances', '%fm_tinymce.instances%');
      $services->set(ElfinderFilePicker::class);
      $services->set(TinyMCEConfigurationBuilder::class)
          ->arg('$basePath', '%fm_tinymce.assets.base_path%');
      $services->set(TinyMCEType::class)
          ->tag('form.type');
      $services->set(TinyMCEExtension::class)
          ->tag('twig.extension');
  };
  ```

- [ ] **Step 3: Load PHP services and prepend the form theme.**

  Make `FMTinyMCEExtension` implement `PrependExtensionInterface`. Its `load()`
  uses `PhpFileLoader` and sets individual parameters
  `fm_tinymce.instances`, `fm_tinymce.assets.base_path`, and
  `fm_tinymce.assets.script_path`. Its `prepend()` calls:

  ```php
  $container->prependExtensionConfig('twig', [
      'form_themes' => ['@FMTinyMCE/Form/tinymce_widget.html.twig'],
  ]);
  ```

  Remove `FMTinyMCEBundle::build()` and the old compiler-pass import. Retain a
  typed `getContainerExtension(): ?ExtensionInterface` only if Symfony's base
  bundle still needs the explicit extension; otherwise remove it and let the
  bundle discover the extension conventionally.

- [ ] **Step 4: Remove XML and compiler-pass artifacts.**

  Delete the files listed above. Do not leave dead legacy files in the package.

- [ ] **Step 5: Run integration checks.**

  ```bash
  vendor/bin/simple-phpunit tests/DependencyInjection tests/Functional/ContainerCompilationTest.php -v
  php tests/Functional/bin/console lint:container
  ```

  Expected: PASS and no XML service loader invocation.

- [ ] **Step 6: Commit DI modernization.**

  ```bash
  git add src/DependencyInjection src/Resources/config/services.php src/FMTinyMCEBundle.php tests/DependencyInjection tests/Functional/ContainerCompilationTest.php
  git rm src/DependencyInjection/Compiler/TwigFormPass.php src/Resources/config/form.xml src/Resources/config/templating.xml src/Resources/config/twig.xml tests/DependencyInjection/Compiler/TwigFormPassTest.php
  git commit -m "refactor: load TinyMCE services from PHP config"
  ```

## Task 6: Replace the form type and Twig API

**Files:**

- Modify: `src/Form/Type/TinyMCEType.php`
- Modify: `src/Twig/TinyMCEExtension.php`
- Modify: `src/Resources/views/Form/tinymce_widget.html.twig`
- Replace: `tests/Form/Type/TinyMCETypeTest.php`
- Create: `tests/Twig/TinyMCEExtensionTest.php`
- Create: `tests/Resources/views/Form/TinyMCEWidgetTest.php`

- [ ] **Step 1: Write failing form-type tests.**

  Assert `TinyMCEType::getParent()` returns `TextareaType::class`; the view
  exposes `tiny_mce_enabled`, `tiny_mce_instance`, `tiny_mce_inline`, and
  `tiny_mce_script_path`; a field-level `enabled: false` wins over the profile;
  and a missing profile raises `InvalidOptionsException` with the profile name.

- [ ] **Step 2: Replace `TinyMCEType` with a Symfony 7.4/8-only implementation.**

  Its essential options are:

  ```php
  public function configureOptions(OptionsResolver $resolver): void
  {
      $resolver->setDefaults([
          'enabled' => null,
          'instance' => 'default',
      ]);
      $resolver->setAllowedTypes('enabled', ['null', 'bool']);
      $resolver->setAllowedTypes('instance', 'string');
  }

  public function getParent(): string
  {
      return TextareaType::class;
  }
  ```

  Inject `InstanceConfigurationResolver` and `scriptPath`. Resolve the profile
  in `buildView()`, convert a missing-profile `InvalidArgumentException` into
  `InvalidOptionsException`, and write only the normalized view variables. Delete
  `getName()`, `getDefaultOptions()`, `Kernel::VERSION_ID`, and `method_exists()`
  branches.

- [ ] **Step 3: Write failing Twig-extension tests.**

  Test a `tinymce_configuration(fieldId, instance, enabled)` function returning
  a JSON string that encodes `license_key: gpl`, an HTML-safe `selector`, and a
  generated picker route. Test `tinymce_asset_url(path)` delegates to `Packages`.
  Assert the function has `is_safe => ['html']` only because its output has
  already passed `JSON_HEX_*` encoding.

- [ ] **Step 4: Replace the Twig extension and form theme.**

  Implement two functions:

  ```php
  public function getFunctions(): array
  {
      return [
          new TwigFunction('tinymce_configuration', $this->configuration(...), ['is_safe' => ['html']]),
          new TwigFunction('tinymce_asset_url', $this->assetUrl(...)),
      ];
  }
  ```

  The normal branch of `tinymce_widget.html.twig` emits a textarea with the
  encoded config in a `data-fm-tinymce-options` attribute and the asset paths in
  data attributes. The disabled branch emits only the standard textarea. The
  inline branch emits a hidden textarea carrying the submitted value plus a
  `<div contenteditable="true">` target; the runtime synchronizes the div back
  to the hidden textarea on editor change and form submit.

  Use Twig's `e('html_attr')` for data attributes. Do not use `|raw` for field
  values. Remove all the old `tinymce_*` fragment functions.

- [ ] **Step 5: Run the form and rendering tests.**

  ```bash
  vendor/bin/simple-phpunit tests/Form tests/Twig tests/Resources/views -v
  ```

  Expected: PASS for normal, disabled, inline, named-profile, and script-breakout
  rendering cases.

- [ ] **Step 6: Commit the public rendering API.**

  ```bash
  git add src/Form/Type/TinyMCEType.php src/Twig/TinyMCEExtension.php src/Resources/views/Form/tinymce_widget.html.twig tests/Form tests/Twig tests/Resources
  git commit -m "feat: render TinyMCE 8 forms from JSON configuration"
  ```

## Task 7: Add the browser runtime and JavaScript unit tests

**Files:**

- Create: `src/Resources/public/fm-tinymce.js`
- Create: `package.json`
- Create: `tests/JavaScript/fm-tinymce.test.mjs`

- [ ] **Step 1: Write failing JavaScript tests.**

  With JSDOM and a mocked `window.tinymce`, test:

  ```text
  two matching fields append one TinyMCE script element and call tinymce.init twice;
  an already loaded script does not append another element;
  a disabled field has no data attribute and is ignored;
  the inline editor's change event updates its hidden textarea;
  an fm_elfinder_url produces TinyMCE's file_picker_callback and returns the selected URL;
  malformed JSON logs one controlled error and does not call tinymce.init.
  ```

- [ ] **Step 2: Create the Node test package.**

  Use ESM and add the smallest dependencies:

  ```json
  {
    "private": true,
    "type": "module",
    "scripts": { "test": "node --test tests/JavaScript/*.test.mjs" },
    "devDependencies": { "jsdom": "^26.0.0" }
  }
  ```

- [ ] **Step 3: Implement the runtime.**

  Expose one idempotent namespace:

  ```js
  window.FMTinyMCE = window.FMTinyMCE || {
    loadScript(scriptUrl) { /* stores one Promise per URL */ },
    initialize(root = document) { /* scans [data-fm-tinymce-options] */ },
  };
  ```

  `loadScript()` appends exactly one `<script data-fm-tinymce-script>` and
  resolves only after `load`; reject on `error`. `initialize()` parses the data
  attribute, sets `target` instead of a selector, adds the controlled Elfinder
  callback only when `fm_elfinder_url` is present, and uses
  `tinymce.init(configuration)`. It marks targets with
  `data-fm-tinymce-initialized="true"` before invoking TinyMCE to avoid duplicate
  initialization.

  On `DOMContentLoaded`, call `initialize()`. The form theme includes this small
  runtime by the installed bundle asset path, while the runtime itself loads the
  configured TinyMCE asset once.

- [ ] **Step 4: Run JavaScript tests.**

  ```bash
  npm install
  npm test
  ```

  Expected: all loader, sync, picker, and malformed-config tests pass without a
  network request.

- [ ] **Step 5: Commit the runtime.**

  ```bash
  git add src/Resources/public/fm-tinymce.js package.json package-lock.json tests/JavaScript
  git commit -m "feat: add idempotent TinyMCE browser runtime"
  ```

## Task 8: Replace the Sensio asset handler with a safe standalone installer

**Files:**

- Create: `src/Composer/TinyMCEAssetInstaller.php`
- Delete: `src/Composer/TinyMCEScriptHandler.php`
- Create: `tests/Composer/TinyMCEAssetInstallerTest.php`
- Modify: `composer.json`

- [ ] **Step 1: Write failing installer tests using temporary directories.**

  Create a fake Composer vendor tree containing
  `tinymce/tinymce/tinymce.min.js` and a plugin file. Verify that the installer:

  ```text
  copies the whole tinymce directory to public/assets/tinymce;
  accepts an explicit extra.tinymce-dir destination;
  uses Composer's configured vendor-dir, not a hard-coded vendor path;
  can run twice with identical file content;
  throws a contextual exception for a missing source and an unwritable destination.
  ```

- [ ] **Step 2: Implement `TinyMCEAssetInstaller::copy(Event $event): void`.**

  Obtain paths from Composer configuration and package extras:

  ```php
  $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
  $source = $vendorDir.'/tinymce/tinymce';
  $destination = $extra['tinymce-dir'] ?? 'public/assets/tinymce';
  ```

  Validate source `tinymce.min.js` exists, make the destination with
  `Filesystem::mkdir()`, mirror source to destination with `Filesystem::mirror()`,
  and emit one IO message. Do not extend a Sensio class, inspect Symfony kernel
  versions, or use `DirectoryIterator` manually.

- [ ] **Step 3: Remove the old handler and validate package references.**

  ```bash
  rg -n 'Sensio|TinyMCEScriptHandler|DistributionBundle' composer.json src tests README.md
  ```

  Expected: no results after documentation is updated in Task 10.

- [ ] **Step 4: Run installer tests.**

  ```bash
  vendor/bin/simple-phpunit tests/Composer/TinyMCEAssetInstallerTest.php -v
  ```

  Expected: PASS, including the repeated-run test.

- [ ] **Step 5: Commit the installer.**

  ```bash
  git add src/Composer/TinyMCEAssetInstaller.php tests/Composer/TinyMCEAssetInstallerTest.php composer.json
  git rm src/Composer/TinyMCEScriptHandler.php
  git commit -m "feat: install TinyMCE assets without SensioBundle"
  ```

## Task 9: Build a real Symfony functional fixture and browser acceptance suite

**Files:**

- Create: `tests/Functional/Kernel.php`
- Create: `tests/Functional/Controller/TinyMCEFixtureController.php`
- Create: `tests/Functional/config/services.php`
- Create: `tests/Functional/config/routes.php`
- Create: `tests/Functional/config/packages/framework.php`
- Create: `tests/Functional/config/packages/twig.php`
- Create: `tests/Functional/FixtureFormType.php`
- Create: `tests/Functional/TinyMCEFormTest.php`
- Create: `tests/Browser/tinymce.spec.mjs`
- Create: `playwright.config.mjs`

- [ ] **Step 1: Write functional HTTP tests before the controller.**

  Test `/tiny-mce/default`, `/tiny-mce/profiles`, `/tiny-mce/inline`,
  `/tiny-mce/disabled`, and `/tiny-mce/elfinder`. Assert HTTP 200; the expected
  data attributes; the absence of editor attributes for disabled fields; two
  unique targets for the profile page; and a generated `/elfinder?instance=tinymce`
  picker URL. Submit the default form and assert the response includes the exact
  posted body, including `<strong>text</strong>`.

- [ ] **Step 2: Implement the fixture kernel, routes, controller, and form.**

  Use `Kernel` plus FrameworkBundle, TwigBundle, and FMTinyMCEBundle. The
  controller creates forms using `TinyMCEType` and renders one Twig template.
  Define the Elfinder route as a harmless fixture endpoint returning a selected
  URL; do not require a running FMElfinderBundle instance for this package's own
  functional suite.

- [ ] **Step 3: Run functional tests.**

  ```bash
  vendor/bin/simple-phpunit tests/Functional/TinyMCEFormTest.php -v
  ```

  Expected: PASS on the active Symfony version before adding browser coverage.

- [ ] **Step 4: Write Playwright tests using actual copied TinyMCE assets.**

  Use a local PHP built-in server or Symfony test server configured by
  `webServer` in `playwright.config.mjs`. Test:

  ```js
  await page.goto('/tiny-mce/default');
  await expect(page.locator('.tox-tinymce')).toHaveCount(1);
  await expect(page.locator('textarea[name="fixture[body]"]')).toHaveValue('<p>Hello</p>');
  ```

  Add separate tests for named profiles, inline synchronization, disabled fields,
  two editors, and the FMElfinder callback. Register listeners for `console`,
  `pageerror`, and `request`; fail the test on errors, failed local assets, or a
  request whose host is not the local fixture host.

- [ ] **Step 5: Run the full acceptance set.**

  ```bash
  vendor/bin/simple-phpunit tests/Functional -v
  npx playwright install --with-deps chromium
  npx playwright test
  ```

  Expected: all user scenarios in the design pass using self-hosted assets only.

- [ ] **Step 6: Commit fixture and acceptance tests.**

  ```bash
  git add tests/Functional tests/Browser playwright.config.mjs
  git commit -m "test: cover TinyMCE 8 user flows in browser"
  ```

## Task 10: Upgrade CI to prove the supported matrix and quality gates

**Files:**

- Modify: `.github/workflows/test.yaml`
- Create: `.github/workflows/release-smoke.yaml`
- Modify: `CONTRIBUTING.md`

- [ ] **Step 1: Write the intended CI matrix into the workflow.**

  Use explicit matrix entries, not accidental dependency selection:

  ```yaml
  include:
    - php: '8.2'
      symfony: '7.4.*'
      flags: '--prefer-lowest'
    - php: '8.2'
      symfony: '7.4.*'
      flags: ''
    - php: '8.3'
      symfony: '7.4.*'
      flags: ''
    - php: '8.4'
      symfony: '7.4.*'
      flags: ''
    - php: '8.5'
      symfony: '7.4.*'
      flags: ''
    - php: '8.4'
      symfony: '8.1.*'
      flags: ''
    - php: '8.5'
      symfony: '8.1.*'
      flags: ''
  ```

  Before each Composer update, run:

  ```bash
  composer config extra.symfony.require "${{ matrix.symfony }}"
  composer update --with-all-dependencies --prefer-dist --no-interaction ${{ matrix.flags }}
  ```

- [ ] **Step 2: Add mandatory PHP quality steps.**

  Every PHP matrix job runs:

  ```bash
  composer validate --strict
  composer audit
  vendor/bin/php-cs-fixer check --diff
  vendor/bin/phpstan analyse
  SYMFONY_DEPRECATIONS_HELPER=max[total]=0 vendor/bin/simple-phpunit -v
  ```

  Run `lint:container` and `lint:twig` against the fixture application after
  Composer installation. Keep coverage in one PHP 8.4/Symfony 8.1 job only.

- [ ] **Step 3: Add a JavaScript/browser job.**

  Use Node LTS, `npm ci`, `npm test`, Playwright's Chromium dependency cache,
  `npx playwright test`, and `npm audit --audit-level=high`. Ensure this job
  calls the standalone TinyMCE asset installer before starting the fixture
  server.

- [ ] **Step 4: Add a release smoke workflow.**

  On tags matching `v2.*`, create two temporary Symfony skeleton applications,
  require the local package through a Composer path repository, and run:

  ```bash
  composer require helios-ag/fm-tinymce-bundle:@dev
  composer run-script copy-tinymce-assets
  php bin/console lint:container
  php bin/console lint:twig templates
  composer archive --format=zip --dir=build
  ```

  One skeleton pins Symfony 7.4, the other the current Symfony 8.x. Verify the
  archive has `LICENSE`, `README.md`, source, and no `tests` or `.github`.

- [ ] **Step 5: Update contributor instructions and validate workflow syntax.**

  Document local equivalents of each CI job in `CONTRIBUTING.md`; validate YAML
  with the repository's available action-linter or a `yamllint` container.

- [ ] **Step 6: Commit CI changes.**

  ```bash
  git add .github/workflows/test.yaml .github/workflows/release-smoke.yaml CONTRIBUTING.md
  git commit -m "ci: verify Symfony 7.4 and 8.x support"
  ```

## Task 11: Publish migration and Community Edition documentation

**Files:**

- Modify: `README.md`
- Create: `UPGRADE-2.0.md`
- Create: `CHANGELOG.md`
- Modify: `CONTRIBUTING.md`

- [ ] **Step 1: Rewrite installation documentation from a clean application.**

  The README must use these commands and explain their result:

  ```bash
  composer require helios-ag/fm-tinymce-bundle:^2.0
  composer run-script copy-tinymce-assets
  php bin/console assets:install
  ```

  Include the canonical configuration from the design document and a form usage
  example:

  ```php
  $builder->add('body', TinyMCEType::class, ['instance' => 'default']);
  ```

- [ ] **Step 2: State licensing precisely.**

  Add a dedicated README section: the bundle source is MIT; TinyMCE Community
  Edition is GPL-2.0-or-later; version 2.0 emits `license_key: 'gpl'`; consumers
  distributing self-hosted TinyMCE must meet the GPL's terms. Link to TinyMCE's
  official license documentation. Do not imply that the bundle grants a TinyMCE
  commercial license.

- [ ] **Step 3: Write `UPGRADE-2.0.md` as an explicit migration table.**

  Include every legacy item and its result:

  | 1.x setting/API | 2.0 action |
  | --- | --- |
  | `base_path`, `js_path` | move to `assets.base_path`, `assets.script_path` |
  | top-level instance options | move under `instances.<name>.options` |
  | `toolbars.toolbar1`, `toolbars.toolbar2` | replace with TinyMCE 8 `options.toolbar` |
  | `theme: modern` | remove; TinyMCE 8 uses its supported default UI |
  | `toolbar_item_size` | remove; no TinyMCE 8 counterpart |
  | `templates` and `template` plugin | remove or migrate to a separately licensed supported plugin |
  | `file_picker_callback` raw JavaScript | remove; use `file_picker.type: fm_elfinder` or own frontend code |
  | `filebrowser` | move route and parameters under `file_picker` |
  | `Symfony\Component\Templating` helper | remove; use Twig API only |
  | `TinyMCEScriptHandler` | use `composer run-script copy-tinymce-assets` |

  Include a full before/after YAML example and a checklist for clearing stale
  TinyMCE 5-7 assets before copying TinyMCE 8.

- [ ] **Step 4: Add a changelog entry and docs regression check.**

  Add `2.0.0 - Unreleased` with Added, Changed, Removed, and Security headings.
  Verify every command shown in README and upgrade guide succeeds in each clean
  release-smoke fixture.

- [ ] **Step 5: Commit documentation.**

  ```bash
  git add README.md UPGRADE-2.0.md CHANGELOG.md CONTRIBUTING.md
  git commit -m "docs: describe TinyMCE 8 Community Edition upgrade"
  ```

## Task 12: Remove residual legacy code and execute the release gate

**Files:**

- Modify: any residual source/test files identified by checks below
- Verify: entire repository

- [ ] **Step 1: Search for prohibited legacy references.**

  Run each command and make its result empty except for intentional historical
  prose in `UPGRADE-2.0.md`:

  ```bash
  rg -n 'Symfony\\Component\\Templating|templating.helper|Sensio\\Bundle\\DistributionBundle|TinyMCEScriptHandler|TwigFormPass|FMTinyMCEBundle:|Kernel::VERSION_ID|getDefaultOptions\(|getName\(' src tests composer.json
  rg -n 'print|paste|contextmenu|template|modern|toolbar_item_size' src tests --glob '!UPGRADE-2.0.md'
  find src/Resources/config -type f -name '*.xml'
  ```

- [ ] **Step 2: Run the fast local verification suite.**

  ```bash
  composer validate --strict
  composer audit
  vendor/bin/php-cs-fixer check --diff
  vendor/bin/phpstan analyse
  SYMFONY_DEPRECATIONS_HELPER=max[total]=0 vendor/bin/simple-phpunit -v
  npm test
  ```

  Expected: all commands exit 0; PHPUnit reports zero direct and indirect
  deprecations.

- [ ] **Step 3: Run fixture and browser verification.**

  ```bash
  php tests/Functional/bin/console lint:container
  php tests/Functional/bin/console lint:twig tests/Functional/templates
  npx playwright test
  npm audit --audit-level=high
  ```

  Expected: form, picker, inline, multiple-editor, safe-JSON, and no-network
  acceptance tests pass.

- [ ] **Step 4: Execute all Composer matrix jobs locally or through CI.**

  For every matrix row in Task 10, use a clean working directory or CI runner,
  run the exact Composer update command with the target Symfony constraint, then
  run PHPUnit and PHPStan. Record the resolved PHP, Symfony, Twig, and TinyMCE
  versions in the pull request.

- [ ] **Step 5: Verify the package archive.**

  ```bash
  rm -rf build
  composer archive --format=zip --dir=build
  unzip -l build/*.zip
  ```

  Expected: the archive includes `LICENSE`, `README.md`, `src/`, and 2.0 docs;
  excludes `tests/`, `.github/`, browser binaries, caches, and local build output.

- [ ] **Step 6: Commit final cleanup and prepare release review.**

  ```bash
  git add -A
  git commit -m "chore: finalize TinyMCE 8 bundle upgrade"
  git status --short
  ```

  Expected: clean working tree. Open a review with the verification log, matrix
  results, upgrade guide, and a note that TinyMCE Community Edition is GPL while
  FMTinyMCEBundle source remains MIT.

## Final acceptance checklist

- [ ] PHP 8.2/Symfony 7.4 `prefer-lowest` resolves and passes.
- [ ] PHP 8.4/Symfony 8.1 resolves and passes.
- [ ] TinyMCE resolves to 8.9+ and local `tinymce.min.js` is copied successfully.
- [ ] Every editor init contains `license_key: 'gpl'` and never contacts Tiny Cloud.
- [ ] Default, named, disabled, inline, multiple-field, FMElfinder, invalid-config,
  and script-injection scenarios pass in PHPUnit and Playwright where applicable.
- [ ] No Symfony Templating, Sensio DistributionBundle, XML DI config, or Symfony
  2-6 compatibility branch remains in distributable source.
- [ ] README, `UPGRADE-2.0.md`, and changelog accurately describe the breaking
  release and licensing boundary.
