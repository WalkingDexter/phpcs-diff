<?php

namespace WalkingDexter\PHP_CodeSniffer\Diff;

use PHP_CodeSniffer\Runner as BaseRunner;

class Runner extends BaseRunner
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // Set the GitModified filter as default.
        if ($this->config->filter === null) {
            $this->config->filter = 'GitModified';
        }

        parent::init();
    }

    /**
     * {@inheritdoc}
     */
    public function processFile($file)
    {
        // Replace the reporter before processing.
        if (!$this->reporter instanceof Reporter) {
            $this->reporter = new Reporter($this->config);
        }

        parent::processFile($file);
    }
}
