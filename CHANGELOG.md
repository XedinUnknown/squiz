# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [0.1.0] - 2019-09-14
Stable release.

## [0.1.0-alpha6] - 2019-03-28
### Added
- Questions can now be one of two types: Text (new), or Multiple Choice (original) (#10).

## [0.1.0-alpha5] - 2019-03-27
### Fixed
- Submissions will be shown even if the user is not logged in (#8).

## [0.1.0-alpha4] - 2019-03-27
### Fixed
- Results not displaying (#6).

## [0.1.0-alpha3] - 2019-03-27
### Added
- Shortcode received optional attributes `quiz_template` and `result_template`,
whilch allow overriding the default template (#4).
- Plugin templates can now be overridden from the theme, including child theme.

### Changed
- Changed some config keys.

## [0.1.0-alpha2] - 2019-03-26
### Changed
- All simple post retrieval operations use `get_posts()`.

### Fixed
- Retrieving posts, like quiz answers, used default WP limit (#2).

## [0.1.0-alpha1] - 2019-03-17
Initial release
