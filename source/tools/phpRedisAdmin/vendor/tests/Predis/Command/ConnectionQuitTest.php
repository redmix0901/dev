<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Command;

/**
 * @group commands
 * @group realm-connection
 */
class ConnectionQuitTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ConnectionQuit';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'QUIT';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array();
        $expected = array();

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame('OK', $this->getCommand()->parseResponse('OK'));
    }

    /**
     * @group connected
     */
    public function testReturnsStatusResponseWhenClosingConnection()
    {
        $redis = $this->getClient();
        $command = $this->getCommand();
        $response = $redis->executeCommand($command);

        $this->assertInstanceOf('Predis\Response\Status', $response);
        $this->assertEquals('OK', $response);
    }
}
