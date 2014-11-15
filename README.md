PHP based mysql dump library
=========

This is small utlity for making backups of MySQL tables. It works with `mysqli` php extension by default, and fallbacks to old-fashined `mysql` extension whenever `mysqli` is not available.

Features:

 * support for plain text and gzip output
 * support for including just particular tables from the database, excluding tables, and dumping tables with prefix

ToDo:
 
 * add support for views and triggers
 * do some tests with importing foreign keys


## Examples

Dump all tables in `world` database:

    $world_dumper = new Shuttle_Dumper(array(
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

    $wp_dumper = new Shuttle_Dumper(array(
        'host' => '',
        'username' => 'root',
        'password' => '',
        'db_name' => 'wordpress',
    ));
    $wp_dumper->dump('wordpress.sql', 'wp_');

Dump only `country` and `city` tables:
    
    $countries_dumper = new Shuttle_Dumper(array(
        'host' => '',
        'username' => 'root',
        'password' => '',
        'db_name' => 'world',
        'include_tables' => array('country', 'city'),
    ));
    $countries_dumper->dump('world.sql.gz');

Dump all tables except for `city`:

    $world_dumper = new Shuttle_Dumper(array(
        'host' => '',
        'username' => 'root',
        'password' => '',
        'db_name' => 'world',
        'exclude_tables' => array('city'),
    ));
    $world_dumper->dump('world-no-cities.sql.gz');

