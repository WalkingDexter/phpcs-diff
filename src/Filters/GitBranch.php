<?php

namespace PHP_CodeSniffer\Filters;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\DeepExitException;
use PHP_CodeSniffer\Util\Common;

class GitBranch extends ExactMatch
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
        $branch = Config::getConfigData('branch');
        if ($branch === null) {
            $error = 'ERROR: You must specify the branch name.' . PHP_EOL;
            throw new DeepExitException($error, 3);
        }

        $modified = [];

        $cmd = 'git diff --name-only ' . escapeshellarg($branch) . ' -- ' . escapeshellarg($this->basedir);
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
