This app is built in PHP using the Laravel framework.

## Environment

**LAMP stack**
*	php >=7.2.5
*	Apache >=2.2
*	mod_rewrite
*	composer
*	git


## Quick Start

	cd /target/folder

	git clone https://github.com/sarapis/ordirectory .

	composer update



Databook is based on **Laravel 8.x**. In case of troubles during installation please refer to [Laravel 8 Installation Guide](https://laravel.com/docs/8.x/installation#installation-via-composer) 




Edit ``/target/folder/.env.default``, set APP_URL to actual domain name, rename to ``.env``



Set Apache DocumentRoot to ``/target/folder/public``



Set Apache AllowOverride option for section ``<Directory "/target/folder/public">`` ``AllowOverride All``



## Demo Installation

Live deployed system can be found in the [https://dcnext.sarapis.org/](https://dcnext.sarapis.org/).


## Contributing

Please use Github's issue tracking system to log bugs, propose features, offer assistance or start a general conversation about the code!

## License

Copyright 2022 Sarapis Foundation Inc

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

