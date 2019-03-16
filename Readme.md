# Jabatan Keretapi Sabah Fares In JSON Format
This is the list of fares in [Jabatan Keretapi Sabah website](http://railway.sabah.gov.my/index.php/info-jkns/perkhidmatanoperasi-keretapi/harga-tambang/) . I scrap it using php script.


## How To Use

### JSON File
Just download the jkns_fares.json file, it can be use directly as data source.

### PHP Script File
This will scrap the Jabatan Keretapi Sabah website and update the jkns_fares.json file. To update the json file, run this command
~~~
$ php crawler.php
~~~

## TODO

* Update code to use PSR.
* Write test.