PHP based MySQL dump library
=========

The library provides easy way to create MySQL dumps files. It will try to create dump through:

 0. `mysqldump` shell utility
 1. native PHP code

For native dumps(on hosts without shell access), it works with `mysqli` php extension by default, and fallbacks to old-fashioned `mysql` whenever `mysqli` isn't available.

The aim of the library is to work on as many web-hosts as possible: it requires PHP 5.2 and requires just one `mysql` or `mysqli` libraries to be available. 

Features:

 * support for plain text and gzip output(whenever the dump file has .gz extension, a gzip archive will be produced)
 * support for including just particular tables from the database, excluding tables, and dumping just tables with particular prefix

ToDo:
 
 * add support for views and triggers
 * try how things work with databases with foreign keys constraints

## Examples

Dump all tables in `world` database:

    $world_dumper = Shuttle_Dumper::create(array(
        'host' => '',
        'username' => 'root',
        'password' => '',
        'db_name' => 'world',
    ));
    // dump the database to plain text file
    $world_dumper->dump('world.sql');

    // send the output to gziped file:
    $world_dumper->dump('world.sql.gz');
    
Dump only the tables with `wp_` prefix:

    $wp_dumper = Shuttle_Dumper::create(array(
        'host' => '',
        'username' => 'root',
        'password' => '',
        'db_name' => 'wordpress',
    ));
    $wp_dumper->dump('wordpress.sql', 'wp_');

Dump only `country` and `city` tables:
    
    $countries_dumper = Shuttle_Dumper::create(array(
        'host' => '',
        'username' => 'root',
        'password' => '',
        'db_name' => 'world',
        'include_tables' => array('country', 'city'),
    ));
    $countries_dumper->dump('world.sql.gz');

Dump all tables except for `city`:

    $world_dumper = Shuttle_Dumper::create(array(
        'host' => '',
        'username' => 'root',
        'password' => '',
        'db_name' => 'world',
        'exclude_tables' => array('city'),
    ));
    $world_dumper->dump('world-no-cities.sql.gz');

