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
 * @group realm-server
 */
class ServerFlushDatabaseTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ServerFlushDatabase';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'FLUSHDB';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $command = $this->getCommand();
        $command->setArguments(array());

        $this->assertSame(array(), $command->getArguments());
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
    public function testFlushesTheEntireLogicalDatabase()
    {
        $redis = $this->getClient();

        $redis->set('foo', 'bar');

        $this->assertEquals('OK', $redis->flushdb());
        $this->assertSame(0, $redis->exists('foo'));
    }
}
