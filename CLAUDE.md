# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Joomla system plugin `plg_system_osmylicensesmanager` ("Joomlashack Extension Support"). It stores the customer's Joomlashack license key(s) and appends them to Pro extension download URLs so Joomla's updater can authenticate. It also injects the Joomlashack custom footer into `com_categories` admin pages for Joomlashack extensions. Supports Joomla 3.9+ and PHP 7.4+ (see `src/osmylicensesmanager.xml` `<alledia>` block).

## Building

There are no tests, linters, or Composer dependencies in this repo. Packages are built with Phing via the external **ShackBuilder** project:

```
phing            # default target "build"; output goes to packages/ (git-ignored)
```

`build.xml` is only a thin client: it requires a local, git-ignored `build.properties` that sets `builder.path` (path to ShackBuilder), then imports `${builder.path}/src/build.xml`. See `build.dist.properties` for the template, which also defines paths to sibling repos the build pulls in: `project.ShackFramework.path`, `project.ShackInstaller.path`, `project.ShackFreeDefaultFiles.path`. Setting `builder.phplint=1` lints PHP in the assembled package.

## Files assembled at build time

`src/` is not the complete extension. The manifest references things that do not exist in this repo; ShackBuilder supplies them:
- `library/Installer/include.php` (required by `src/script.installer.php`) and the `Alledia\Installer\AbstractScript` base class come from **ShackInstaller**.
- `form/`, `views/`, and `media/` (including the `customfooter` field) come from the `<include>ShackDefaultFiles</include>` directive.
- **ShackFramework** (`allediaframework` library) is bundled as a related extension.

Don't create these folders in `src/` to "fix" the manifest.

## Runtime architecture

- `src/include.php` loads ShackFramework from `JPATH_SITE/libraries/allediaframework/include.php` and registers the `Alledia\OSMyLicensesManager` namespace autoloader for `library/`. It returns false if the framework is missing.
- `src/osmylicensesmanager.php` aliases `Alledia\OSMyLicensesManager\PluginBase` to the framework's `AbstractPlugin` when the include works, or to Joomla's `CMSPlugin` when it doesn't. `isEnabled()` checks which base was used, so the plugin turns itself off without fatal errors when the framework is missing. Guard any new framework-dependent behavior with `isEnabled()`.
- Event hooks:
  - `onAfterInitialise`: AJAX endpoint (`plugin=system_osmylicensesmanager&task=license.save`, admin, logged in) that saves posted `license-keys` and echoes JSON.
  - `onInstallerBeforePackageDownload`: for non-free download URLs, appends the license keys to the URL.
  - `onAfterRender`: adds the extension footer to category manager pages.
- `src/library/Free/PluginHelper.php` holds the URL and key logic. Download URLs look like `https://deploy.ostraining.com/client/download/{free|pro|paid}/<x>/<y>/<base64(keys)>`. Keys are sanitized to `[a-z0-9,]` (multiple keys are comma-separated) and stored with the framework's `Licensed::storeParams()`.

## Conventions

- PSR-12 style with phpcs disable/enable comments around side effects and the non-namespaced plugin and installer class names. Every PHP file starts with the GPL header block, including the `@copyright 2016-<year>` line, which is updated in bulk ("Update file headers" commits).
- Language strings go in `src/language/en-GB/*.ini`.
- Releases bump `<version>` and `<creationDate>` in `src/osmylicensesmanager.xml`. Work happens on `development`, and PRs target `main`.
