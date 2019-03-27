# Change log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [[*next-version*]] - YYYY-MM-DD

## [0.1.0-alpha3] - 2019-03-27
### Added
- Shortcode received optional attributes `quiz_template` and `result_template`,
whilch allow overriding the default template.
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
