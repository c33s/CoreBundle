<?php

/*
* This file is based upon Composer.
*
* (c) Nils Adermann <naderman@naderman.de>
* Jordi Boggiano <j.boggiano@seld.be>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace c33s\CoreBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Process\ExecutableFinder;

/**
* The Input/Output helper.
*
* @author François Pluchino <francois.pluchino@opendisplay.com>
* @author Jordi Boggiano <j.boggiano@seld.be>
*/
class ConsoleIO
{
    protected $input;
    protected $output;
    protected $helperSet;
    protected $lastMessage;
    private $startTime;

    /**
     * Constructor.
     *
     * @param InputInterface $input The input instance
     * @param OutputInterface $output The output instance
     * @param HelperSet $helperSet The helperSet instance
     */
    public function __construct(InputInterface $input, OutputInterface $output, HelperSet $helperSet)
    {
        $this->input = $input;
        $this->output = $output;
        $this->helperSet = $helperSet;
    }

    public function enableDebugging($startTime)
    {
        $this->startTime = $startTime;
    }

    /**
     * {@inheritDoc}
     */
    public function isInteractive()
    {
        return $this->input->isInteractive();
    }

    /**
     * {@inheritDoc}
     */
    public function isDecorated()
    {
        return $this->output->isDecorated();
    }

    /**
     * {@inheritDoc}
     */
    public function isVerbose()
    {
        return $this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
    }

    /**
     * {@inheritDoc}
     */
    public function isVeryVerbose()
    {
        return $this->output->getVerbosity() >= 3; // OutputInterface::VERSOBITY_VERY_VERBOSE
    }

    /**
     * {@inheritDoc}
     */
    public function isDebug()
    {
        return $this->output->getVerbosity() >= 4; // OutputInterface::VERBOSITY_DEBUG
    }

    /**
     * {@inheritDoc}
     */
    public function write($messages, $verboseityLevel = OutputInterface::VERBOSITY_NORMAL, $newline = true)
    {
        if ($this->output->getVerbosity() >= $verboseityLevel)
        {
            if (null !== $this->startTime)
            {
                $messages = (array) $messages;
                $messages[0] = sprintf(
                    '[%.1fMB/%.2fs] %s',
                    memory_get_usage() / 1024 / 1024,
                    microtime(true) - $this->startTime,
                    $messages[0]
                );
            }
            $this->output->write($messages, $newline);
            $this->lastMessage = join($newline ? "\n" : '', (array) $messages);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function overwrite($messages, $newline = true, $size = null)
    {
        // messages can be an array, let's convert it to string anyway
        $messages = join($newline ? "\n" : '', (array) $messages);

        // since overwrite is supposed to overwrite last message...
        if (!isset($size)) {
            // removing possible formatting of lastMessage with strip_tags
            $size = strlen(strip_tags($this->lastMessage));
        }
        // ...let's fill its length with backspaces
        $this->write(str_repeat("\x08", $size), false);

        // write the new message
        $this->write($messages, false);

        $fill = $size - strlen(strip_tags($messages));
        if ($fill > 0) {
            // whitespace whatever has left
            $this->write(str_repeat(' ', $fill), false);
            // move the cursor back
            $this->write(str_repeat("\x08", $fill), false);
        }

        if ($newline) {
            $this->write('');
        }
        $this->lastMessage = $messages;
    }

    /**
     * {@inheritDoc}
     */
    public function ask($question, $default = null)
    {
        return $this->helperSet->get('dialog')->ask($this->output, $question, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function askConfirmation($question, $default = true)
    {
        return $this->helperSet->get('dialog')->askConfirmation($this->output, $question, $default);
    }

    /**
     * {@inheritDoc}
     */
    public function askAndValidate($question, $validator, $attempts = false, $default = null)
    {
        return $this->helperSet->get('dialog')->askAndValidate($this->output, $question, $validator, $attempts, $default);
    }
    
    /**
     * {@inheritDoc}
     */
    public function askHiddenResponse($question, $fallback = true)
    {
        return $this->helperSet->get('dialog')->askHiddenResponse($this->output, $question, $fallback);
    }
}
