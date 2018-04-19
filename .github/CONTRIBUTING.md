# Contributing
**Table Of Contents**
- [Reporting Bugs](#reporting-bugs)
- [Pull Requests](#pull-requests)
    - [Backwards Compatibility](#backwards-compatibility)

# Reporting Bugs
Bug reports should be opened as [GitHub issues](https://github.com/LartTyler/MHWDB-Docs/issues) on the docs repository.
**Security flaws** should not be opened as an issue, and should instead be emailed directly to tyler@lartonoix.com.

Before submitting a bug report, please make sure that you've tried clearing any local caching you have in place before
attempting to query the API.

# Pull Requests
First off, thanks for your interest in helping to improve the API! At the moment, I'm maintaining this project on my
own, in my spare time, so any help at all is appreciated. So again, thank you!

In order to submit a code change to the API, please fork the project first. Changes for a single pull request should be
limited to a single feature or change, and should be done on a "topic branch". Accepted topic prefixes are listed below.

|Topic Prefix|Description|Example|
|:---|:---|:---|
|feature/|Used to indicate that the branch adds a new feature to the API|feature/weapon-assets|
|fix/|Used to indicate that the branch fixes a bug|fix/deleted-elderseal-values|
|refactor/|Used to indicate that the branch refactors existing API data structures*|refactor/charms|

\* Before making any changes to the API data structures, please read the section on
[backwards compatibility](#backwards-compatibility).

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