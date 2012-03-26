Migrations
==========

Migrations are a convenient way for you to alter your database in a structured and organized manner. 
You could edit fragments of SQL by hand, but you would then be responsible for telling other developers 
that they need to go and run them. You’d also have to keep track of which changes need to be run against 
the production machines next time you deploy. [by rubyonrails.org](http://guides.rubyonrails.org/migrations.html)


Getting started & Documentation
===========

This is a tool, which we, PHP developers, use to migrate MySQL databases. 
The idea was borrowed from the migration system built into Ruby on Rails. 

Newest Zend Framework version (1.11.11) is used as the example application, but lower versions should work as well.

See the [Wiki](https://github.com/travelplanet24/mysql-versioning-for-zend-framework/wiki/Integration) 
for the complete documentation!

Commands
========

Below you can find all the commands, which are available.

### Generate migration
<pre><code>$ php scripts/db/generate_migration.php -t "create table countries"
Created new migration in /home/$USER/projects/my_app/db/migrations/20120326212655_create_table_countries.sql
</code></pre>

### List pending migrations
<pre><code>$ php scripts/db/migrate.php -p
Connected as username@localhost/development

The following migrations are pending: 
 * 20120326212127_create_countries.sql
</code></pre>

### Run migrations
<pre><code>$ php scripts/db/migrate.php 
Connected as username@localhost/development
Running 20120326212655_create_table_countries.sql...
-- Put your up migration here
CREATE TABLE `countries` (
  `countryCode` varchar(3) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`countryCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
:: started: 09:29:53=>09:29:53 min:sec 00:00 
:: Affected rows: 0
Finished running 20120326212655_create_table_countries.sql.
</code></pre>

### Rollback migrations
<pre><code>$ php scripts/db/migrate.php -d
Connected as username@localhost/development
Running 20120326212655_create_table_countries.sql...
-- Put your down migration here
DROP TABLE `countries`;
:: started: 09:30:39=>09:30:39 min:sec 00:00 
:: Affected rows: 0
Finished running 20120326212655_create_table_countries.sql.
</code></pre>


Contribution
===========

Updates and improvements are more than welcome!



Copyright (C) 2012 travelplanet24 S.A.

Permission is hereby granted, free of charge, to any person obtaining a copy of this software 
and associated documentation files (the "Software"), to deal in the Software without restriction, 
including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, 
and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, 
subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or 
substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING 
BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, 
WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH 
THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
