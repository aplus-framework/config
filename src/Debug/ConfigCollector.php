<?php declare(strict_types=1);
/*
 * This file is part of Aplus Framework Config Library.
 *
 * (c) Natan Felles <natanfelles@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Config\Debug;

use Framework\Config\Config;
use Framework\Debug\Collector;
use Framework\Debug\Debugger;
use Framework\Helpers\ArraySimple;

class ConfigCollector extends Collector
{
    protected Config $config;

    public function setConfig(Config $config) : static
    {
        $this->config = $config;
        return $this;
    }

    public function getActivities() : array
    {
        $activities = [];
        foreach ($this->getData() as $data) {
            $activities[] = [
                'collector' => $this->getName(),
                'class' => static::class,
                'description' => 'Load config file ' . \htmlentities($data['name']),
                'start' => $data['start'],
                'end' => $data['end'],
            ];
        }
        return $activities;
    }

    public function getContents() : string
    {
        if (!isset($this->config)) {
            \ob_start();
            echo '<p>This collector has not been added to a Config instance.</p>';
            return \ob_get_clean(); // @phpstan-ignore-line
        }
        $count = \count($this->getConfigs());
        \ob_start();
        $dir = $this->config->getDir();
        if ($dir !== null):
            ?>
            <p><strong>Config directory:</strong> <?= \htmlentities($dir) ?></p>
        <?php
        endif;
        ?>
        <p><?= $count ?> configuration<?= $count === 1 ? '' : 's' ?> have been set.</p>
        <?php
        if ($count === 0) {
            return \ob_get_clean(); // @phpstan-ignore-line
        }
        echo $this->getTable();
        return \ob_get_clean(); // @phpstan-ignore-line
    }

    protected function getTable() : string
    {
        \ob_start();
        ?>
        <table>
            <thead>
            <tr>
                <th>Name</th>
                <th>Instances</th>
                <th>Values</th>
                <th title="Milliseconds">Time to Load</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->getConfigs() as $config): ?>
                <?php
                $count = \count($config['instances']);
                ?>
                <tr>
                    <td rowspan="<?= $count ?>"><?= $config['name'] ?></td>
                    <td><?= $config['instances'][0]['name'] ?></td>
                    <td rowspan="1">

                        <table>
                            <thead>
                            <tr>
                                <th>Key</th>
                                <th>Type</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($config['instances'][0]['values'] as $key => $value): ?>
                                <tr>
                                    <td><?= \htmlentities((string) $key) ?></td>
                                    <td><?= \htmlentities((string) $value) ?></td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>

                    </td>
                    <td rowspan="<?= \count($config['instances']) ?>">
                        <?php
                        $found = false;
                        foreach ($this->getData() as $value) {
                            if ($value['name'] === $config['name']) {
                                echo Debugger::roundSecondsToMilliseconds($value['end'] - $value['start']);
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            echo 0;
                        }
                        ?>
                    </td>
                </tr>
                <?php for ($i = 1; $i < $count; $i++): ?>
                    <tr>
                        <td><?= $config['instances'][$i]['name'] ?></td>
                        <td>

                            <table>
                                <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Type</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($config['instances'][$i]['values'] as $key => $value): ?>
                                    <tr>
                                        <td><?= \htmlentities((string) $key) ?></td>
                                        <td><?= \htmlentities((string) $value) ?></td>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>

                        </td>
                    </tr>

                <?php endfor ?>

            <?php endforeach ?>
            </tbody>
        </table>
        <?php
        return \ob_get_clean(); // @phpstan-ignore-line
    }

    /**
     * @return array<mixed>
     */
    protected function getConfigs() : array
    {
        $result = [];
        foreach ($this->config->getAll() as $name => $instances) {
            $count = \count($result);
            $result[$count]['name'] = $name;
            $result[$count]['instances'] = [];
            $counter = 0;
            foreach ($instances as $instance => $values) {
                $result[$count]['instances'][$counter]['name'] = $instance;
                $result[$count]['instances'][$counter]['values'] = ArraySimple::convert($values);
                foreach ($result[$count]['instances'][$counter]['values'] as &$value) {
                    $value = \get_debug_type($value);
                }
                unset($value);
                $counter++;
            }
        }
        return $result;
    }
}
