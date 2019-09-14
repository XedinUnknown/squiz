# SQuiz
A WordPress plugin for creating flexible quizzes.

## Functionality
Unlike most quiz plugins, this one aims to focus more on data collection,
reporting, and analysis, rather than rating according to a set of pre-determined
correct answers.

## Installation
Download the [latest release][latest-release] **build** (not source!) archive, then
install as a regular composer plugin.

## Development
### Release
This project includes build instructions for [Phing][]. To release version `x.y.z`, run

```
vendor/bin/phing release -Dversion=x.y.z
```

This will create an archive containing the built project with name `squiz-x.y.z-<timestamp>.zip`
in `build/release`. That archive can be installed as a regular WordPress plugin.

[Phing]: https://www.phing.info/
[latest-release]: /releases/latest 


