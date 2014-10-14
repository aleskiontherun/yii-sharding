yii-sharding
============

Extends Yii active record model to use with multiple tables in multiple databases.

See examples/example.php for more information

## Table schema caching ##

To get the table schema caching to work you need to create a base table without any suffixes.
Only this base table will be queried to retrieve the table schema cache to use with all other sharded tables with suffixes.
In the example above it will be:

* UserPhotos
* UserPhotos0
* UserPhotos1
* UserPhotos2
* ...
* UserPhotos99
