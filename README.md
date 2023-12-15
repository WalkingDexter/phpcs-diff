## About

Removes non-diff violations from [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) reports. This is a lightweight solution with minimum requirements.

## Installation

```bash
composer require --dev walkingdexter/phpcs-diff
```

## Usage

Check current changes:
```bash
vendor/bin/phpcs-diff
```

This is a shortcut for:
```bash
vendor/bin/phpcs-diff --filter=GitModified
```

Check changes that have been staged for commit:
```bash
vendor/bin/phpcs-diff --filter=GitStaged
```

Check changes between current and target branches:
```bash
vendor/bin/phpcs-diff --filter=GitBranch --runtime-set branch target
```

If the branch name is set in the configuration options:
```bash
vendor/bin/phpcs-diff --filter=GitBranch
```

You can use any other options and arguments that the `phpcs` command accepts.

## Limitations

Only reports that use prepared data are supported.
