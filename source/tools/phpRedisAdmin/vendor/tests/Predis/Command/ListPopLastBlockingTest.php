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
 * @group realm-list
 */
class ListPopLastBlockingTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\ListPopLastBlocking';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'BRPOP';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key1', 'key2', 'key3', 10);
        $expected = array('key1', 'key2', 'key3', 10);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testFilterArgumentsKeysAsSingleArray()
    {
        $arguments = array(array('key1', 'key2', 'key3'), 10);
        $expected = array('key1', 'key2', 'key3', 10);

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $raw = array('key', 'value');
        $expected = array('key', 'value');

        $command = $this->getCommand();

        $this->assertSame($expected, $command->parseResponse($raw));
    }
}
