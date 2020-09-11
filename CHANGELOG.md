# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## 1.5.0 - TBD

### Added

- Nothing.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- Nothing.

## 1.4.0 - 2018-05-03

### Added

- [zfcampus/zf-http-cache#17](https://github.com/zfcampus/zf-http-cache/pull/17) adds support for PHP 7.1 and 7.2.

### Changed

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zfcampus/zf-http-cache#17](https://github.com/zfcampus/zf-http-cache/pull/17) removes support for HHVM.

### Fixed

- Nothing.

## 1.3.0 - 2016-07-07

### Added

- [zfcampus/zf-http-cache#11](https://github.com/zfcampus/zf-http-cache/pull/11) adds ETag support.
  A default generator that uses MD5 hashing of content for comparisons; if
  content matches, an empty 304 response is returned with a `Not-Modified`
  header. By default, it will not overwrite any ETag headers you already send,
  though you can override that via the `api-tools-http-cache.etag.override` flag.
  Additionally, you can provide alternate ETag generation mechanisms by
  implementing `Laminas\ApiTools\HttpCache\ETagGeneratorInterface`, and specifying the service
  name of your implementation via the `api-tools-http-cache.etag.generator`
  configuration value.
- [zfcampus/zf-http-cache#13](https://github.com/zfcampus/zf-http-cache/pull/13) and
  [zfcampus/zf-http-cache#14](https://github.com/zfcampus/zf-http-cache/pull/14) add support for Laminas
  Framework v3 components, including laminas-mvc, laminas-servicemanager, and
  laminas-eventmanager.
- [zfcampus/zf-http-cache#14](https://github.com/zfcampus/zf-http-cache/pull/14) marks the package as
  a Laminas module, allowing laminas-component-installer to auto-inject it into
  application configuration.
- [zfcampus/zf-http-cache#12](https://github.com/zfcampus/zf-http-cache/pull/12) adds more
  capabilities around matching routed controllers, including the ability to
  match:
  - the route name
  - `controller::action` concatenations
  - just the controller
  - a regular expression (to match against any of the above items)
  - wildcards
  See the README.md file and the `config/module.config.php` for examples.

### Deprecated

- Nothing.

### Removed

- [zfcampus/zf-http-cache#14](https://github.com/zfcampus/zf-http-cache/pull/14) removes support for
  PHP 5.5.

### Fixed

- Nothing.

## 1.2.3 - 2016-07-06

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- [zfcampus/zf-http-cache#10](https://github.com/zfcampus/zf-http-cache/pull/10) removes the
  dependency on laminas-loader (it was not being used).

### Fixed

- [zfcampus/zf-http-cache#8](https://github.com/zfcampus/zf-http-cache/pull/8) provides some
  performance optimizations for a number of conditional statements.

## 1.2.2 - 2015-11-11

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-http-cache#6](https://github.com/zfcampus/zf-http-cache/pull/6) fixes the
  `HttpCacheListenerFactory` to rename the `createService()` to `__invoke()`,
  as originally intended in zfcampus/zf-http-cache#4.

## 1.2.1 - 2015-11-10

### Added

- Nothing.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-http-cache#5](https://github.com/zfcampus/zf-http-cache/pull/5) fixes missing imports,
  and ensures all code files have appropriate file-level license docblocks.

## 1.2.0 - 2015-11-10

### Added

- [zfcampus/zf-http-cache#3](https://github.com/zfcampus/zf-http-cache/pull/3) updates the minimum
  required PHP version to 5.5, and the minimum Laminas component versions to 2.5.

### Deprecated

- Nothing.

### Removed

- Nothing.

### Fixed

- [zfcampus/zf-http-cache#3](https://github.com/zfcampus/zf-http-cache/pull/3) updates the code to
  be forwards-compatible with the upcoming v3 of laminas-eventmanager.
- [zfcampus/zf-http-cache#3](https://github.com/zfcampus/zf-http-cache/pull/3) updates the code to
  be forwards-compatible with v2.6 and v3 of laminas-servicemanager.
