# Config

[![Build Status](https://travis-ci.org/ironedgesoftware/config.svg?branch=master)](https://travis-ci.org/ironedgesoftware/config)


This component provides a simple API to handle configuration parameters. It
can handle different **readers** and **writers** to read from and write to any
storage.

Currently supported readers and writers:

* **files**: Using component [**ironedge/file-utils**](https://github.com/ironedgesoftware/file-utils).
* **array**: Useful for testing purposes.

## Usage

To start using the configuration object, you only need to instantiate it. Default reader is **file**, which means it loads
the configuration from a file by default. The default **writer** is also **file**, which means it will save the data to a file.
You can, of course, set the reader and writer you want, as you'll see later.

### Basic Usage

In the following example, we set an array as the initial data of the config object, and start accessing its data
through the API provided by our **IronEdge\Component\Config\Config** class. We use the **DataTrait** provided by our
component [**ironedge/common-utils**](https://github.com/ironedgesoftware/common-utils) to expose some of the
following methods:

``` php
<?php

namespace Your\Namespace;

use IronEdge\Component\Config\Config;

// Create a config instance. You can set initial data and options
// using its constructor.

$data = [
    'user'          => [
        'username'      => 'admin',
        'groups'        => [
            'primary'       => 'administrator',
            'secondary'     => ['development']
        ]
    ]
];

$config = new Config($data);

// Obtain the username. Returns: 'admin'

$config->get('user.username');

// Obtain the primary group. Returns: 'administrator'

$config->get('user.groups.primary');

// This attribute does not exist, so we return the default value used as the second argument: 'defaultProfile'

$config->get('user.profile.name', 'defaultProfile');

// Returns true because this attribute exists

$config->has('user.username');

// Returns false because this attribute does not exist

$config->has('user.profile.name');

// Sets the missing attribute of the last example. Note that it creates any missing attribute, at any depth.

$config->set('user.profile.name', 'myProfile');

// Obtain the newly created attribute. Returns: 'myProfile'

$config->get('user.profile.name');

```

### Load Configuration Data from the Reader

Following the last example, suppose you want to load data from an YML file with
the following contents:

``` yml
user:
  profile:
    name:           newProfile
  createdAt:      2010-01-01 12:00:01
cacheDir:       %root_path%/cache
```

To load this file, use the following code:

``` php
<?php

// ...
// Include here the code of the last examples
// ...

// Load the YML file

$config->load(['file' => '/path/to/config.yml']);

// Returns: 'newProfile'

$config->get('user.profile.name');

// Returns: '2010-01-01 12:00:01'

$config->get('user.createdAt');

// Returns 'administrator'

$config->get('user.groups.primary');

```

The **load** method delegated the loading of the file **/path/to/config.yml** file to the
**file** reader. This reader loaded the YML file, and then the config object
did an **array_replace_recursive** using the current data, and the data loaded from the YML file.

### Replace Template Variables

Did you notice the value of the parameter **cacheDir**, **%root_dir%/cache**? The config object
allows to use template variables to be replace in all the string values found on the configuration data.

By default, we don't provide any template variables, but you can set them with
the **templateVariables** option as shown in the following example:

``` php
<?php

// ...
// Include here the code of the last examples
// ...

// This returns '%root_dir%/cache' since we didn't configure
// any template variables yet

$config->get('cacheDir');

// Now we set the template variable

$config->setOptions(['templateVariables' => ['%root_dir%' => '/my/root/dir']]);

// Now, it will return '/my/root/dir/cache'

$config->get('cacheDir');
```

### Save Configuration Data

As the default writer is **file**, if you call the **save** method,
it will save the configuration data to a file:

``` php
<?php

// ...
// Include here the code of the last examples
// ...

// Save configuration data to file '/path/to/my_config.yml'

$config->save(['file' => '/path/to/my_config.yml']);

```

### Additional Features

These are additional features provided by this component.

#### Load Data into a Specific Key

Sometimes you want to load configuration data into a specific key instead of
replacing the whole data array. You can do it using the following code:}

``` php
<?php

namespace Your\Namespace;

use IronEdge\Component\Config\Config;

$config = new Config(
    [
        'user'      => [
            'username' => 'myUser'
        ]
    ]
);

// Load profiles from file /path/to/profiles.yml
//
// Suppose this file has the following contents
//
// profiles:
//   - administrator
//   - development

$config->load(
    ['file'      => '/path/to/profiles.yml'],
    ['loadInKey' => 'user.profiles']
);

// This would return: ['administrator', 'development']

$config->get('user.profiles');
```

#### Merge, Replace, Merge Recursive, Replace Recursive

You can execute any of this operations using the following methods:

``` php
<?php

namespace Your\Namespace;

use IronEdge\Component\Config\Config;

$config = new Config(
    [
        'user' => [
            'username' => 'admin',
            'groups' => [
                'administrator'
            ]
        ]
    ]
);

// Internally it uses "array_merge".
// Config data will end like: ['user' => 'myUser', 'groups' => ['myGroup']]

$config->merge(['user' => 'myUser', 'groups' => ['myGroup']]);

// Internally it uses "array_merge_recursive".
// Config data will end like: ['user' => 'myUser', 'groups' => ['myGroup', 'myOtherGroup']]

$config->mergeRecursive(['user' => 'myUser', 'groups' => ['myOtherGroup']]);

// Internally it uses "array_replace".
// Config data will end like: ['user' => 'myOtherUser', 'groups' => ['myGroup']]

$config->replace(['user' => 'myOtherUser', 'groups' => ['myGroup']]);

// Internally it uses "array_replace_recursive".
// Config data will end like: ['user' => 'myUser', 'groups' => ['myOtherGroup']]

$config->replaceRecursive(['user' => 'myUser', 'groups' => ['myOtherGroup']]);

```

### Custom Reader and Writer

You can define your own readers and writers in the following way:

``` php
<?php

namespace Your\Namespace;

use IronEdge\Component\Config\Config;

// This must implement IronEdge\Component\Config\Reader\ReaderInterface

$myReader = new MyReader();

// This must implement IronEdge\Component\Config\Writer\WriterInterface

$myWriter = new MyWriter();

$config = new Config(
    [],
    [
        'reader'        => $myReader,
        'writer'        => $myWriter
    ]
);

// Reader method **read** will receive values from **readerOptions**

$config->load(['readerOptions' => ['myReaderOption' => 'myReaderOptionValue']]);

// Writer method **writer** will receive values from **writerOptions**

$config->save(['writerOptions' => ['myWriterOption' => 'myWriterOptionValue']]);

```

Of course, you can use any reader / writer combination you want. For instance,
you could read the configuration from a file, but write it to a database.