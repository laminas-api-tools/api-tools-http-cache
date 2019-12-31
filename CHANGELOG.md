# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

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
