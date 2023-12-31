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

Check changes relative to the `main` branch:
```bash
vendor/bin/phpcs-diff --filter=GitCommitted --runtime-set git_diff_commit main
```

Check changes relative to a specific commit:
```bash
vendor/bin/phpcs-diff --filter=GitCommitted --runtime-set git_diff_commit 15a5e27
```

If the commit name is set in the configuration options:
```bash
vendor/bin/phpcs-diff --filter=GitCommitted
```

You can use any other options and arguments that the `phpcs` command accepts.

## Limitations

Only reports that use prepared data are supported.
