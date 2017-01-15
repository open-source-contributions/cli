<?php
/**
 * This file is part of SebastianFeldmann\Cli.
 *
 * (c) Sebastian Feldmann <sf@sebastian-feldmann.info>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianFeldmann\Cli\Process;

use RuntimeException;
use SebastianFeldmann\Cli\Command\Result;
use SebastianFeldmann\Cli\Process;

/**
 * Class ProcOpen
 *
 * @package SebastianFeldmann\Cli
 */
class ProcOpen implements Process
{
    /**
     * Execute the command.
     *
     * @param  string $cmd
     * @return \SebastianFeldmann\Cli\Command\Result
     */
    public function execute(string $cmd) : Result
    {
        $old            = error_reporting(0);
        $descriptorSpec = [
            ['pipe', 'r'],
            ['pipe', 'w'],
            ['pipe', 'w'],
        ];

        $process = proc_open($cmd, $descriptorSpec, $pipes);
        if (!is_resource($process)) {
            throw new RuntimeException('can\'t execute \'proc_open\'');
        }

        $stdOut = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stdErr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $code = proc_close($process);
        error_reporting($old);

        return new Result($cmd, $code, $stdOut, $stdErr);
    }
}
