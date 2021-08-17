Config
======

Aplus Framework Config Library.

- `Installation`_
- `Config Manipulation`_
- `Configuration Files`_
- `Persistence`_
- `Parsers`_
- `Conclusion`_

Installation
------------

The installation of this library can be done with Composer:

.. code-block::

    composer require aplus/config

Config Manipulation
--------------------

.. code-block:: php

    <?php
    require __DIR__ . '/vendor/autoload.php';

    use Framework\Config\Config;

    $config = new Config();

The structure of a service instance configuration
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: php

    [
        'default' => [],
    ]

Used for the `Language Library <https://docs.aplus-framework.com/guides/libraries/language/>`_
configuration file in the `App Project <https://docs.aplus-framework.com/guides/projects/app/>`_.

.. code-block:: php

    use Framework\Language\Language;

    return [
        'default' => [
            'default' => 'en',
            'supported' => [
                'en',
                'es',
                'pt-br',
            ],
            'fallback_level' => Language::FALLBACK_NONE,
            'directories' => null,
            'negotiate' => false,
        ],
    ];

.. code-block:: php

    [
        'default' => [],
        'custom_instance' => [],
        'other_custom_instance' => [],
    ]

Set and Get
^^^^^^^^^^^

**Set Service Configs**

.. code-block:: php

    $serviceName = 'database';
    $serviceConfigs = [
        'host' => 'localhost',
        'username' => 'root',
    ];
    $config->set($serviceName, $serviceConfigs);

**Get Service Configs**

.. code-block:: php

    $configs = $config->get($serviceName);

.. code-block:: php

    [
        'host' => 'localhost',
        'username' => 'root',
    ]

**Custom Service Instance Names**

.. code-block:: php

    $serviceInstanceName = 'custom';
    $configs = $config->set($serviceName, $serviceConfigs, $serviceInstanceName);

.. code-block:: php

    $serviceInstanceName = 'custom';
    $configs = $config->get($serviceName, $serviceInstanceName);

Add
^^^

.. code-block:: php

    $config->add($serviceName, $serviceConfigs);

.. code-block:: php

    $config->add($serviceName, $serviceConfigs, 'custom');

Set Many
^^^^^^^^

.. code-block:: php

    $config->setMany([
        'database' => [
            'default' => [
                'host' => 'localhost',
                'username' => 'root',
            ],
            'replica' => [
                'host' => '192.168.0.100',
                'username' => 'root',
                'password' => 'P45SwopD',
            ],
        ],
        'cache' => [
            'default' => [
                'handler' => 'memcached',
            ],
        ],
    ]);

Get All
^^^^^^^

.. code-block:: php

    $allConfigs = $config->getAll();

Configuration Files
-------------------

.. code-block:: php

    $directoryPath = __DIR__ . '/configs';
    $config = new Config($directoryPath);

A basic config file must return an *array* that should have the ``default``
key set:

.. code-block:: php

    return [
        'default' => [],
        'custom' => [],
    ];

.. code-block:: php

    $databaseDefaultConfigs = $config->get('database');
    $databaseCustomConfigs = $config->get('database', 'custom');

`Config Manipulation`_

Persistence
-----------

Parsers
-------

Config `Persistence`_  or with the `Set Many`_ method.

Example setting many:

.. code-block:: php

    use Framework\Config\Config;
    use Framework\Config\Parsers\EnvParser;

    $filename = __DIR__ . '/../.env';
    $configs = EnvParser::parse($filename);

    $config = new Config();
    $config->setMany($configs);

Example setting persistence:

.. code-block:: php

    use Framework\Config\Config;
    use Framework\Config\Parsers\EnvParser;

    $filename = __DIR__ . '/../.env';
    $configs = EnvParser::parse($filename);

    $config = new Config(persistence: $configs);

The Config Library provides the following parsers:

- `INI Parser`_
- `YAML Parser`_
- `Database Parser`_
- `JSON Parser`_
- `XML Parser`_
- `Env Parser`_

INI Parser
^^^^^^^^^^

INI syntax

.. code-block:: php

    use Framework\Config\Parsers\IniParser;

    $filename = __DIR__ . '/../config.ini';
    $configs = IniParser::parse($filename);

.. code-block:: ini

    # Service 1
    [service1]
    default.value1 = foo
    default.value2 = 23

    # Service 2
    [service2]
    default.array.0 = True
    custom.array.1 = 'False'

YAML Parser
^^^^^^^^^^^

YAML syntax

.. code-block:: php

    use Framework\Config\Parsers\YamlParser;

    $filename = __DIR__ . '/../config.yaml';
    $configs = YamlParser::parse($filename);

.. code-block:: yaml

    # Service 1
    service1:
      default:
        value1: foo
        value2: 23
    
    # Service 2
    service2:
      default:
        array: [True]
      custom:
        array: ['False']

Database Parser
^^^^^^^^^^^^^^^

Database table

`Database <https://docs.aplus-framework.com/guides/libraries/database/>`_

.. code-block:: php

    use Framework\Config\Parsers\DatabaseParser;

    $databaseConfigs = [
        'username' => 'dbuser'
        'password' => 'p4$$30rT'
        'schema' => 'app'
        'table' => 'Configs'
    ];
    $configs = DatabaseParser::parse($databaseConfigs);

.. code-block:: sql

    USE `app`;

    CREATE TABLE `Configs` (
        `key` varchar(255) NOT NULL PRIMARY KEY,
        `value` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    INSERT INTO `Configs`
    (`key`, `value`)
    VALUES
    ('service1.default.value1', 'foo'),
    ('service1.default.value2', 23),
    ('service2.default.0', 'True'),
    ('service2.custom.0', '"False"');

.. code-block:: php

    use Framework\Database\Database;
    use Framework\Database\Definition\Table\TableDefinition;

    $username = 'dbuser';
    $password = 'p4$$30rT';
    $schema = 'app';
    $table = 'Configs';

    $database = new Database($username, $password, $schema);

    $database->createTable($table)
        ->definition(function (TableDefinition $definition) {
            $definition->column('key')->varchar(255)->primaryKey();
            $definition->column('value')->varchar(255);
        })->run();

    $database->insert($table)
        ->columns('key', 'value')            
        ->values([
            ['service1.default.value1', 'foo'],
            ['service1.default.value2', 23],
            ['service2.default.0', 'True'],
            ['service2.custom.0', '"False"'],
        ])->run();

JSON Parser
^^^^^^^^^^^

JSON syntax

.. code-block:: php

    use Framework\Config\Parsers\JsonParser;

    $filename = __DIR__ . '/../config.json';
    $configs = JsonParser::parse($filename);

.. code-block:: json

    {
        "service1": {
            "default": {
                "value1": "foo",
                "value2": 23
            }
        },
        "service2": {
            "default": {
                "array": [
                    True
                ]
            },
            "custom": {
                "array": [
                    "False"
                ]
            }
        }
    }

XML Parser
^^^^^^^^^^

XML syntax

.. code-block:: php

    use Framework\Config\Parsers\XmlParser;

    $filename = __DIR__ . '/../config.xml';
    $configs = XmlParser::parse($filename);

.. code-block:: xml

    <?xml version="1.0" encoding="UTF-8" ?>
    <config>
        <!-- Service 1 -->
        <service1>
            <default>
                <value1>foo</value1>
                <value2>23</value2>
            </default>
        </service1>
    
        <!-- Service 2 -->
        <service2>
            <default>
                <array>True</array>
            </default>
            <custom>
                <array>'False'</array>
            </custom>
        </service2>
    </config>

Env Parser
^^^^^^^^^^

Dotenv syntax

.. code-block:: php

    use Framework\Config\Parsers\EnvParser;

    $filename = __DIR__ . '/../config.env';
    $configs = EnvParser::parse($filename);

.. code-block:: bash

    # Service 1
    service1.default.value1 = foo
    service1.default.value2 = 23
    
    # Service 2
    service2.default.array.0 = True
    service2.custom.array.1 = 'False'

Conclusion
----------

Aplus Config Library is an easy-to-use tool for, beginners and experienced, PHP developers. 
It is perfect to organize, centralize and manipulate configurations. 
The more you use it, the more you will learn.

.. note::
    Did you find something wrong? 
    Be sure to let us know about it with an
    `issue <https://gitlab.com/aplus-framework/libraries/config/issues>`_. 
    Thank you!
