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
 * @group realm-set
 */
class SetCardinalityTest extends PredisCommandTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function getExpectedCommand()
    {
        return 'Predis\Command\SetCardinality';
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedId()
    {
        return 'SCARD';
    }

    /**
     * @group disconnected
     */
    public function testFilterArguments()
    {
        $arguments = array('key');
        $expected = array('key');

        $command = $this->getCommand();
        $command->setArguments($arguments);

        $this->assertSame($expected, $command->getArguments());
    }

    /**
     * @group disconnected
     */
    public function testParseResponse()
    {
        $this->assertSame(1, $this->getCommand()->parseResponse(1));
    }

    /**
     * @group connected
     */
    public function testReturnsNumberOfMembers()
    {
        $redis = $this->getClient();

        $redis->sadd('letters', 'a', 'b', 'c', 'd');

        $this->assertSame(4, $redis->scard('letters'));
    }

    /**
     * @group connected
     */
    public function testReturnsZeroOnEmptySet()
    {
        $redis = $this->getClient();

        $this->assertSame(0, $redis->scard('letters'));
    }

    /**
     * @group connected
     * @expectedException \Predis\Response\ServerException
     * @expectedExceptionMessage Operation against a key holding the wrong kind of value
     */
    public function testThrowsExceptionOnWrongType()
    {
        $redis = $this->getClient();

        $redis->set('metavars', 'foo');
        $redis->scard('metavars');
    }
}
