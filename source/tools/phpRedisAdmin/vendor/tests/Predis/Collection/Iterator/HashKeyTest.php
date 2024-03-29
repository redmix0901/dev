<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Collection\Iterator;

use Predis\Profile;
use PredisTestCase;

/**
 * @group realm-iterators
 */
class HashKeyTest extends PredisTestCase
{
    /**
     * @group disconnected
     * @expectedException \Predis\NotSupportedException
     * @expectedExceptionMessage The current profile does not support 'HSCAN'.
     */
    public function testThrowsExceptionOnInvalidProfile()
    {
        $client = $this->getMock('Predis\ClientInterface');

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.0')));

        new HashKey($client, 'key:hash');
    }

    /**
     * @group disconnected
     */
    public function testIterationWithNoResults()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->once())
               ->method('hscan')
               ->with('key:hash', 0, array())
               ->will($this->returnValue(array(0, array())));

        $iterator = new HashKey($client, 'key:hash');

        $iterator->rewind();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @link https://github.com/nrk/predis/pull/330
     * @link https://github.com/nrk/predis/issues/331
     * @group disconnected
     */
    public function testIterationWithIntegerFields()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->once())
               ->method('hscan')
               ->with('key:hash', 0, array())
               ->will($this->returnValue(array(0, array(
                    1 => 'a', 2 => 'b', 3 => 100, 'foo' => 'bar',
               ))));

        $iterator = new HashKey($client, 'key:hash');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('a', $iterator->current());
        $this->assertSame(1, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('b', $iterator->current());
        $this->assertSame(2, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame(100, $iterator->current());
        $this->assertSame(3, $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('bar', $iterator->current());
        $this->assertSame('foo', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnSingleFetch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->once())
               ->method('hscan')
               ->with('key:hash', 0, array())
               ->will($this->returnValue(array(0, array(
                    'field:1st' => 'value:1st', 'field:2nd' => 'value:2nd', 'field:3rd' => 'value:3rd',
               ))));

        $iterator = new HashKey($client, 'key:hash');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:3rd', $iterator->current());
        $this->assertSame('field:3rd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->at(1))
               ->method('hscan')
               ->with('key:hash', 0, array())
               ->will($this->returnValue(array(2, array(
                    'field:1st' => 'value:1st', 'field:2nd' => 'value:2nd',
               ))));
        $client->expects($this->at(2))
               ->method('hscan')
               ->with('key:hash', 2, array())
               ->will($this->returnValue(array(0, array(
                    'field:3rd' => 'value:3rd',
               ))));

        $iterator = new HashKey($client, 'key:hash');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:3rd', $iterator->current());
        $this->assertSame('field:3rd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnMultipleFetchesAndHoleInFirstFetch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->at(1))
               ->method('hscan')
               ->with('key:hash', 0, array())
               ->will($this->returnValue(array(4, array())));
        $client->expects($this->at(2))
               ->method('hscan')
               ->with('key:hash', 4, array())
               ->will($this->returnValue(array(0, array(
                    'field:1st' => 'value:1st', 'field:2nd' => 'value:2nd',
               ))));

        $iterator = new HashKey($client, 'key:hash');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationOnMultipleFetchesAndHoleInMidFetch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->at(1))
               ->method('hscan')
               ->with('key:hash', 0, array())
               ->will($this->returnValue(array(2, array(
                    'field:1st' => 'value:1st', 'field:2nd' => 'value:2nd',
               ))));
        $client->expects($this->at(2))
               ->method('hscan')
               ->with('key:hash', 2, array())
               ->will($this->returnValue(array(5, array())));
        $client->expects($this->at(3))
               ->method('hscan')
               ->with('key:hash', 5, array())
               ->will($this->returnValue(array(0, array(
                    'field:3rd' => 'value:3rd',
               ))));

        $iterator = new HashKey($client, 'key:hash');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:3rd', $iterator->current());
        $this->assertSame('field:3rd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionMatch()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->at(1))
               ->method('hscan')
               ->with('key:hash', 0, array('MATCH' => 'field:*'))
               ->will($this->returnValue(array(2, array(
                    'field:1st' => 'value:1st', 'field:2nd' => 'value:2nd',
               ))));

        $iterator = new HashKey($client, 'key:hash', 'field:*');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionMatchOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->at(1))
               ->method('hscan')
               ->with('key:hash', 0, array('MATCH' => 'field:*'))
               ->will($this->returnValue(array(1, array(
                    'field:1st' => 'value:1st',
                ))));
        $client->expects($this->at(2))
               ->method('hscan')
               ->with('key:hash', 1, array('MATCH' => 'field:*'))
               ->will($this->returnValue(array(0, array(
                    'field:2nd' => 'value:2nd',
                ))));

        $iterator = new HashKey($client, 'key:hash', 'field:*');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionCount()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->at(1))
               ->method('hscan')
               ->with('key:hash', 0, array('COUNT' => 2))
               ->will($this->returnValue(array(0, array(
                    'field:1st' => 'value:1st', 'field:2nd' => 'value:2nd',
               ))));

        $iterator = new HashKey($client, 'key:hash', null, 2);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionCountOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->at(1))
               ->method('hscan')
               ->with('key:hash', 0, array('COUNT' => 1))
               ->will($this->returnValue(array(1, array(
                    'field:1st' => 'value:1st',
                ))));
        $client->expects($this->at(2))
               ->method('hscan')
               ->with('key:hash', 1, array('COUNT' => 1))
               ->will($this->returnValue(array(0, array(
                    'field:2nd' => 'value:2nd',
                ))));

        $iterator = new HashKey($client, 'key:hash', null, 1);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionsMatchAndCount()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->at(1))
               ->method('hscan')
               ->with('key:hash', 0, array('MATCH' => 'field:*', 'COUNT' => 2))
               ->will($this->returnValue(array(0, array(
                    'field:1st' => 'value:1st', 'field:2nd' => 'value:2nd',
               ))));

        $iterator = new HashKey($client, 'key:hash', 'field:*', 2);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationWithOptionsMatchAndCountOnMultipleFetches()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->at(1))
               ->method('hscan')
               ->with('key:hash', 0, array('MATCH' => 'field:*', 'COUNT' => 1))
               ->will($this->returnValue(array(1, array(
                    'field:1st' => 'value:1st',
                ))));
        $client->expects($this->at(2))
               ->method('hscan')
               ->with('key:hash', 1, array('MATCH' => 'field:*', 'COUNT' => 1))
               ->will($this->returnValue(array(0, array(
                    'field:2nd' => 'value:2nd',
                ))));

        $iterator = new HashKey($client, 'key:hash', 'field:*', 1);

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }

    /**
     * @group disconnected
     */
    public function testIterationRewindable()
    {
        $client = $this->getMock('Predis\Client', array('getProfile', 'hscan'));

        $client->expects($this->any())
               ->method('getProfile')
               ->will($this->returnValue(Profile\Factory::get('2.8')));
        $client->expects($this->exactly(2))
               ->method('hscan')
               ->with('key:hash', 0, array())
               ->will($this->returnValue(array(0, array(
                    'field:1st' => 'value:1st', 'field:2nd' => 'value:2nd',
               ))));

        $iterator = new HashKey($client, 'key:hash');

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->rewind();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:1st', $iterator->current());
        $this->assertSame('field:1st', $iterator->key());

        $iterator->next();
        $this->assertTrue($iterator->valid());
        $this->assertSame('value:2nd', $iterator->current());
        $this->assertSame('field:2nd', $iterator->key());

        $iterator->next();
        $this->assertFalse($iterator->valid());
    }
}
