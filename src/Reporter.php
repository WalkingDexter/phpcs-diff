<?php

namespace WalkingDexter\PHP_CodeSniffer\Diff;

use PHP_CodeSniffer\Config;
use PHP_CodeSniffer\Exceptions\DeepExitException;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Reporter as BaseReporter;

class Reporter extends BaseReporter
{
    /**
     * {@inheritdoc}
     */
    public function prepareFileReport(File $phpcsFile)
    {
        $report = parent::prepareFileReport($phpcsFile);

        // Check for errors and warnings.
        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            return $report;
        }

        // Error recording is required.
        if ($this->config->recordErrors === false) {
            return $report;
        }

        // Remove errors not related to the diff.
        if ($diffLines = $this->getDiffLines($phpcsFile)) {
            foreach ($report['messages'] as $line => $lineErrors) {
                if (!array_key_exists($line, $diffLines)) {
                    $this->removeLineErrors($report, $line);
                }
            }
        }

        return $report;
    }

    /**
     * Gets the 'git diff' output for the given file.
     *
     * @param File $phpcsFile The current file being checked.
     *
     * @return array
     * @throws \PHP_CodeSniffer\Exceptions\DeepExitException
     */
    protected function getDiffOutput(File $phpcsFile)
    {
        $path = $phpcsFile->getFilename();
        if ($path === '') {
            return [];
        }

        switch ($phpcsFile->config->filter) {
            case 'GitCommitted':
                $commit = Config::getConfigData('git_diff_commit');
                if ($commit === null) {
                    $error = 'ERROR: You must specify the commit name.' . PHP_EOL;
                    throw new DeepExitException($error, 3);
                }
                $cmd = 'git diff ' . escapeshellarg($commit) . ' -- ' . escapeshellarg($path);
                break;
            case 'GitModified':
                $cmd = 'git diff -- ' . escapeshellarg($path);
                break;
            case 'GitStaged':
                $cmd = 'git diff --cached -- ' . escapeshellarg($path);
                break;
        }

        if (empty($cmd)) {
            $error = 'ERROR: The specified filter is not supported.' . PHP_EOL;
            throw new DeepExitException($error, 3);
        }

        $output = [];
        exec($cmd, $output);
        return $output;
    }

    /**
     * Gets the lines from 'git diff' output for the given file.
     *
     * @param File $phpcsFile The current file being checked.
     *
     * @return array
     */
    protected function getDiffLines(File $phpcsFile)
    {
        $lines = [];

        foreach ($this->getDiffOutput($phpcsFile) as $line) {
            $matches = [];

            // Get the lines from each hunk.
            if (preg_match('/^@@ -[0-9]+,[0-9]+ \+([0-9]+),([0-9]+) @@/', $line, $matches) === 1) {
                $hunkLines = range($matches[1], ($matches[1] + $matches[2]) - 1);
                $lines += array_fill_keys($hunkLines, true);
            }
        }

        return $lines;
    }

    /**
     * Removes errors from the report for the given line.
     *
     * @param array $report Prepared report data.
     * @param int   $line   The line number.
     *
     * @return void
     */
    protected function removeLineErrors(array &$report, $line)
    {
        $lineErrors = $report['messages'][$line];
        unset($report['messages'][$line]);

        foreach ($lineErrors as $colErrors) {
            foreach ($colErrors as $error) {
                if ($error['type'] === 'ERROR') {
                    $report['errors']--;
                } else {
                    $report['warnings']--;
                }

                if ($error['fixable'] === true) {
                    $report['fixable']--;
                }
            }
        }
    }
}
