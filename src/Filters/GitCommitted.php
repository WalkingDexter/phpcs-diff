<?php

namespace PHP_CodeSniffer\Filters;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\DeepExitException;
use PHP_CodeSniffer\Util\Common;

class GitCommitted extends ExactMatch
{
    /**
     * {@inheritdoc}
     */
    protected function getBlacklist()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     *
     * @throws \PHP_CodeSniffer\Exceptions\DeepExitException
     */
    protected function getWhitelist()
    {
        $commit = Config::getConfigData('git_diff_commit');
        if ($commit === null) {
            $error = 'ERROR: You must specify the commit name.' . PHP_EOL;
            throw new DeepExitException($error, 3);
        }

        $modified = [];

        $cmd = 'git diff --name-only ' . escapeshellarg($commit) . ' -- ' . escapeshellarg($this->basedir);
        $output = [];
        exec($cmd, $output);

        // The code below is identical to the GitModified filter.
        $basedir = $this->basedir;
        if (is_dir($basedir) === false) {
            $basedir = dirname($basedir);
        }

        foreach ($output as $path) {
            $path = Common::realpath($path);

            if ($path === false) {
                continue;
            }

            do {
                $modified[$path] = true;
                $path = dirname($path);
            } while ($path !== $basedir);
        }

        return $modified;
    }
}
