# SQuiz
A WordPress plugin for creating flexible quizzes.

## Functionality
Unlike most quiz plugins, this one aims to focus more on data collection,
reporting, and analysis, rather than rating according to a set of pre-determined
correct answers.

- Create quizzes, which aggregate questions and answers into a questionnaire.
- Allow visitors to reply to questionnaires by creating submissions which get recorded, and reflect every answer to every question in the quiz.
- Collect data in the form of multiple choice questions, or free-form text.
- Display any quiz on any page using the `[squiz]` shortcut.

## Requirements
- WP: 4.0+
- PHP: 7.0+

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
[latest-release]: https://github.com/XedinUnknown/squiz/releases/latest
