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
class ConnectionPingTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ConnectionPing';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'PING';
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
        $this->assertSame('PONG', $this->getCommand()->parseResponse('PONG'));
    }

    /**
     * @group connected
     */
    public function testAlwaysReturnsStatusResponse()
    {
        $redis = $this->getClient();
        $response = $redis->ping();

        $this->assertInstanceOf('Predis\Response\Status', $response);
        $this->assertEquals('PONG', $response);
    }
}
