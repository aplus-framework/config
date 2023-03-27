Config
======

.. image:: image.png
    :alt: Aplus Framework Config Library

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

The Config Library allows you to manipulate configurations to be used by
services, storing them in a single place.

To instantiate the Config class, we can do as follows:

.. code-block:: php

    <?php
    require __DIR__ . '/vendor/autoload.php';

    use Framework\Config\Config;

    $config = new Config();

The structure of a service instance configuration
#################################################

All configurations are stored in arrays, in which there are keys with the name
of the service instances, such as ``default``:

.. code-block:: php

    [
        'default' => [],
    ]

And in these keys are inserted the configs of each service instance.

Let's look at a configuration file used to instantiate database services:

.. code-block:: php

    <?php

    return [
        'default' => [
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'password',
        ],
    ];

Note that the file returns an array with the ``default`` key.

It is possible to define more configurations, adding new keys, which are the
name of the service instances.

Let's see how to define the configurations for the ``default`` and ``replica``
instances:

.. code-block:: php

    [
        'default' => [
            'host' => 'localhost',
            'username' => 'root',
            'password' => 'password',
        ],
        'replica' => [
            'host' => '192.168.0.100',
            'username' => 'root',
            'password' => 'foo',
        ],
    ]

Set and Get
###########

In the Config instance we can set and get configurations with the ``set`` and
``get`` methods.

Set Service Configs
^^^^^^^^^^^^^^^^^^^

Let's see how to set the **database** service configs with host and username
information:

.. code-block:: php

    $serviceName = 'database';
    $serviceConfigs = [
        'host' => 'localhost',
        'username' => 'root',
    ];
    $config->set($serviceName, $serviceConfigs); // array

Get Service Configs
^^^^^^^^^^^^^^^^^^^

So, we can get the information through the ``get`` method. Let's see:

.. code-block:: php

    $serviceName = 'database';
    $configs = $config->get($serviceName); // array or null

And, in the ``$configs`` variable, the database information will be defined:

.. code-block:: php

    [
        'host' => 'localhost',
        'username' => 'root',
    ]

Custom Service Instance Names
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The default instance is the ``default``. However, you can manipulate information
from other instances.

To set a non-default instance, use the third parameter of the ``set`` method.

Let's see how to add information to the ``replica`` instance:

.. code-block:: php

    $serviceInstanceName = 'replica';
    $configs = $config->set($serviceName, $serviceConfigs, $serviceInstanceName);

And to get information, we use the second parameter of the ``get`` method.

.. code-block:: php

    $serviceInstanceName = 'replica';
    $configs = $config->get($serviceName, $serviceInstanceName); // array or null

Add
###

Above, we saw how to set configurations that overwrite existing instances.

But, it is possible to add only new configs, which will be merged.

For this, we use the ``add`` method:

.. code-block:: php

    $config->add($serviceName, $serviceConfigs); // array

And, in the third parameter, you can define in which instance the configs will
be added:

.. code-block:: php

    $config->add($serviceName, $serviceConfigs, 'default'); // array

Set Many
########

It is possible to set several configurations at once through the ``setMany`` method.

Let's see how to set two instances of database configurations (default and
replica) and one instance for the cache service (default):

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
    ]); // static

Get All
#######

To get all the configurations use the ``getAll`` method:

.. code-block:: php

    $allConfigs = $config->getAll(); // array

Configuration Files
-------------------

Above, we saw how to set configurations individually by instances and also
several at once.

In addition to being able to modify the configurations by methods, it is also
possible to define configurations in files that contain the name of the services
and return an array with the instances.

To do this, use Config passing the directory where the configuration files will
be in the first argument:

.. code-block:: php

    $directoryPath = __DIR__ . '/configs';
    $config = new Config($directoryPath);

It is desirable that all configuration files have the ``default`` instance.

In the file below we have two instances, ``default`` and ``custom`` and the file
name must be the name of the service, for example, **database.php**:

.. code-block:: php

    return [
        'default' => [],
        'custom' => [],
    ];

When there is a directory defined, the configuration files will be loaded
automatically and the service settings will be filled in.

In the example below, let's get the database service information with the
``default`` instance and then with the ``custom`` instance:

.. code-block:: php

    $databaseDefaultConfigs = $config->get('database'); // array or null
    $databaseCustomConfigs = $config->get('database', 'custom'); // array or null

If you try to get configs from a service that hasn't been set up yet and the
service file doesn't exist, an exception will be thrown.

Persistence
-----------

In the second argument of the Config class it is possible to set persistent
configurations, which will not be overwritten by the ``add``, ``load``, ``set``
and ``setMany`` methods:

.. code-block:: php

    use Framework\Config\Config;

    $directory = __DIR__ . '/../configs';
    $persistence = [
        'database' => [
            'host' => 'localhost',
        ]
    ]
    $config = new Config($directory, $persistence);

Parsers
-------

The library has several parses for different types of files. With which it is
possible to set `Persistence`_ or several settings at once using the
`Set Many`_ method.

Let's see an example parsing a file of type **env** and setting various
configurations:

.. code-block:: php

    use Framework\Config\Config;
    use Framework\Config\Parsers\EnvParser;

    $filename = __DIR__ . '/../.env';
    $configs = EnvParser::parse($filename); // array

    $config = new Config();
    $config->setMany($configs); // static

The same can be done to set persistent configurations:

.. code-block:: php

    use Framework\Config\Config;
    use Framework\Config\Parsers\EnvParser;

    $filename = __DIR__ . '/../.env';
    $configs = EnvParser::parse($filename); // array

    $config = new Config(persistence: $configs);

The Config Library provides the following parsers:

- `INI Parser`_
- `YAML Parser`_
- `Database Parser`_
- `JSON Parser`_
- `XML Parser`_
- `Env Parser`_

INI Parser
##########

Files of type **INI** can be parsed as shown below:

.. code-block:: php

    use Framework\Config\Parsers\IniParser;

    $filename = __DIR__ . '/../config.ini';
    $configs = IniParser::parse($filename); // array

The syntax of **INI** files is as follows:

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
###########

Files of type **YAML** can be parsed as follows:

.. code-block:: php

    use Framework\Config\Parsers\YamlParser;

    $filename = __DIR__ . '/../config.yaml';
    $configs = YamlParser::parse($filename); // array

And below is an example of the syntax of a **YAML** file:

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
###############

In addition to files, configurations of a **database** table can also be
obtained using the `Database Library <https://docs.aplus-framework.com/guides/libraries/database/>`_.

Instead of passing the file path to the ``parse`` method, you pass the
database connection information:

.. code-block:: php

    use Framework\Config\Parsers\DatabaseParser;

    $databaseConfigs = [
        'username' => 'dbuser'
        'password' => 'p4$$30rT'
        'schema' => 'app'
        'table' => 'Configs'
    ];
    $configs = DatabaseParser::parse($databaseConfigs); // array

The configuration table in the database can be created as shown below:

.. code-block:: sql

    USE `app`;

    CREATE TABLE `Configs` (
        `key` varchar(255) NOT NULL PRIMARY KEY,
        `value` varchar(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

And the values of the services must have the service name as a prefix, followed
by a period and the name of the instance and after another period the name
of the configuration key.

Let's see how to enter example configurations:

.. code-block:: sql

    INSERT INTO `Configs`
    (`key`, `value`)
    VALUES
    ('service1.default.value1', 'foo'),
    ('service1.default.value2', 23),
    ('service2.default.0', 'True'),
    ('service2.custom.0', '"False"');

Below is an example file to create the Configs table and insert sample data
using the Database Library:

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
###########

Configurations can also be stored in **JSON** files.

To get the configs, just use JsonParser:

.. code-block:: php

    use Framework\Config\Parsers\JsonParser;

    $filename = __DIR__ . '/../config.json';
    $configs = JsonParser::parse($filename); // array

Below is an example with the **JSON** syntax:

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
##########

Configurations can also be stored in **XML**.

.. code-block:: php

    use Framework\Config\Parsers\XmlParser;

    $filename = __DIR__ . '/../config.xml';
    $configs = XmlParser::parse($filename); // array

Example **XML** file with configs:

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
##########

Also, you can use files with the **ENV** syntax:

.. code-block:: php

    use Framework\Config\Parsers\EnvParser;

    $filename = __DIR__ . '/../config.env';
    $configs = EnvParser::parse($filename); // array

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
