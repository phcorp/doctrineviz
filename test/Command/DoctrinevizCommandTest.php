<?php

/*
 * This file is part of the doctrineviz package
 *
 * Copyright (c) 2017 Pierre Hennequart
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Feel free to edit as you please, and have fun.
 *
 * @author Pierre Hennequart <pierre@janalis.com>
 */

namespace Janalis\Doctrineviz\Test\Command;

use Janalis\Doctrineviz\Command\DoctrinevizCommand;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Doctrineviz command test.
 *
 * @coversDefaultClass Janalis\Doctrineviz\Command\DoctrinevizCommand
 */
class DoctrinevizCommandTest extends WebTestCase
{
    /**
     * Test configure.
     *
     * @covers ::configure
     * @group command
     */
    public function testConfigure()
    {
        $command = new DoctrinevizCommand();
        $this->assertEquals($command::NAME, $command->getName());
        $this->assertContains('pattern', array_keys($command->getDefinition()->getOptions()));
    }

    /**
     * Test execute: dot formatting.
     *
     * @covers ::execute
     * @group command
     */
    public function testExecuteDotFormatting()
    {
        $expected = 'digraph g {'.PHP_EOL.
            '  rankdir="LR"'.PHP_EOL.
            '  ranksep="3"'.PHP_EOL.
            '  address ['.PHP_EOL.
            '    shape="record"'.PHP_EOL.
            '    width="4"'.PHP_EOL.
            '    label="ADDRESS|<id> id\\l"'.PHP_EOL.
            '  ]'.PHP_EOL.
            '  group ['.PHP_EOL.
            '    shape="record"'.PHP_EOL.
            '    width="4"'.PHP_EOL.
            '    label="GROUP|<id> id\\l"'.PHP_EOL.
            '  ]'.PHP_EOL.
            '  user ['.PHP_EOL.
            '    shape="record"'.PHP_EOL.
            '    width="4"'.PHP_EOL.
            '    label="USER|<id> id\\l|<address_id> address_id\\l"'.PHP_EOL.
            '  ]'.PHP_EOL.
            '  user_group ['.PHP_EOL.
            '    shape="record"'.PHP_EOL.
            '    width="4"'.PHP_EOL.
            '    label="USER_GROUP|<group_id> group_id\\l|<user_id> user_id\\l"'.PHP_EOL.
            '  ]'.PHP_EOL.
            '  user_group:group_id -> group:id;'.PHP_EOL.
            '  user_group:user_id -> user:id;'.PHP_EOL.
            '  user:address_id -> address:id;'.PHP_EOL.
            '}'.PHP_EOL;
        $client = static::createClient();
        $command = new DoctrinevizCommand();
        $command->setContainer($client->getContainer());
        $options = array_combine(array_map(function ($option) {
            return '--'.$option;
        }, array_keys($command->getDefinition()->getOptionDefaults())), array_values($command->getDefinition()->getOptionDefaults()));
        $input = new ArrayInput(array_replace($options, [
            '--format' => 'dot',
        ]), $command->getDefinition());
        $output = new BufferedOutput();
        $command->execute($input, $output);
        $this->assertEquals($expected, $output->fetch());
    }

    /**
     * Test execute: update example.
     *
     * @covers ::execute
     * @group command
     */
    public function testExecuteUpdateExample()
    {
        $client = static::createClient();
        $command = new DoctrinevizCommand();
        $command->setContainer($client->getContainer());
        $options = array_combine(array_map(function ($option) {
            return '--'.$option;
        }, array_keys($command->getDefinition()->getOptionDefaults())), array_values($command->getDefinition()->getOptionDefaults()));
        $input = new ArrayInput(array_replace($options, [
            '--output-path' => __DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'example.png',
            '--format' => 'png',
        ]), $command->getDefinition());
        $output = new BufferedOutput();
        $command->execute($input, $output);
    }
}