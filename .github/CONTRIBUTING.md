# Contributing
**Table Of Contents**
- [Reporting Bugs](#reporting-bugs)
- [Pull Requests](#pull-requests)
    - [Backwards Compatibility](#backwards-compatibility)
- [Styleguides](#styleguides)
    - [Git Commit Messages](#git-commit-messages)
    - [PHP Styleguide](#php-styleguide)

# Reporting Bugs
Bug reports should be opened as [GitHub issues](https://github.com/LartTyler/MHWDB-Docs/issues) on the docs repository.
**Security flaws** should not be opened as an issue, and should instead be emailed directly to tyler@lartonoix.com.

Before submitting a bug report, please make sure that you've tried clearing any local caching you have in place before
attempting to query the API.

# Pull Requests
First off, thanks for your interest in helping to improve the API! At the moment, I'm maintaining this project on my
own, in my spare time, so any help at all is appreciated. So again, thank you!

In order to submit a code change to the API, please
[fork the project first](https://help.github.com/articles/creating-a-pull-request-from-a-fork/). Changes for a single
pull request must be limited to a single feature or change, and should be done on a "topic branch". Accepted topic
prefixes are listed below.

|Topic Prefix|Description|Example|
|:---|:---|:---|
|feature/|Used to indicate that the branch adds a new feature to the API|feature/weapon-assets|
|fix/|Used to indicate that the branch fixes a bug|fix/deleted-elderseal-values|
|refactor/|Used to indicate that the branch refactors existing API data structures*|refactor/charms|

\* Before making any changes to the API data structures, please read the section on
[backwards compatibility](#backwards-compatibility).

When submitting a pull request, please include a detailed changelog describing what changes were made.

## Backwards Compatibility
Maintaining backwards compatibility is very important. Any update that changes how response data is structured MUST do
so in a way that preserves backwards compatibility. A release containing such a change MUST include a notice of
deprecation that gives a future date on which the deprecated representation of the data will be removed.

For example, at the time of writing, a structure change to armor elemental resistances has been proposed, which would
move the `attributes.resist*` fields to a dedicated `resistances` field. When the structure change is made, the new
`resistances` field will be added and the old fields will remain in the `attributes` field for 3 weeks, after which
point they will be removed. The changelog for whatever release this change is part of might look something like so.

-----
### Changelog
- Elemental resistances on armor objects has been added to a dedicated `resistances` field.

**Deprecations:**
- The existing `attributes.resist*` fields have been deprecated, and will be removed from the API on May 10, 2018.
-----

# Styleguides
### Git Commit Messages
- Use present tense ("Add feature" not "Added feature")
- Use imperative mood ("Move fields to" not "Moves fields to")
- Limit the first line to 72 characters or less
- Reference relevant issues or pull requests after the first line (if applicable)
- DO NOT use emoji anywhere in your commit messages

### PHP Styleguide
- Only use tabs to indent, not spaces
- Use spaces around binary operators (such as `+`, `-`, or `&&`)
- Do not use spaces around unary operators (such as `!`)
- Do not use spaces inside parenthesis (such as `if (condition)`)
- Do not use spaces inside short array brackets (such as `[1, 2, 3]`)
- `If` statements with only one line should not be wrapped in braces.
- Curly braces must always be placed on the same line as the statement they match (such as `class MyClass { ...` or
`if (condition) { ...`
- Limit lines to 120 characters
- Arrays should be written using short array syntax (such as `[1, 2, 3]`)
- Equality must be checked using `===` not `==`
- Classes and class members should include phpDoc comments