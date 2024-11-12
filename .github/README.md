<div align="center">
    <img alt="logo" src="../.ksinf/logo_ctr_full.png" height="95">
    <h3>KerogsPHP Framework</h3>
    <p><em>Simplifying Web Development with Efficiency and Flexibility</em></p>
</div>

## Presentation
KerogsPHP Framework is an easy-to-use PHP framework for websites. Its architecture is simple to understand yet effective. It also provides a good basis for all kinds of projects, especially webapps. It allows you to be autonomous and free in your choices, without being heavy or cumbersome. It also brings with it a certain notion of basic security.

## Installation
1. **Clone the repository**

### requirements
You'll need ``NodeJS`` and ``Composer`` installed.

### Command

```sh
git clone https://github.com/KSInfinite/KerogsPHP-Framework.git
```

2. **Go in the directory**
```sh
cd KerogsPHP-framework
```

3. **Install composer package**
```sh
composer install
```

4. **Go in the public directory**
```sh
cd ./public
```

5. **Install npm package**
```sh
npm install
```

### Tips for use
#### Use TypeScript
Use this simple command to observe changes made in the file and automatically convert it to JavaScript
```sh
npx tsc --watch
```

#### config.yml
![example config.yml](../.ksinf/configyml_ex.com.svg)

#### .env file
When you log on to the site for the first time, an .env file will be created automatically.

It will contain the database connection information (to be modified as needed), as well as a randomly generated encryption key for later encryption if required. This value can be modified. If you wish, you can modify the ``.env`` file at will. Note that this file will be created automatically each time it is deleted or does not exist.

You can disable automatic generation in the ``config.yml`` file.
example :
```ini
CRYPT_KEY=7927400a-cf78-4977-9a66-36de48d47e09
DB_SERVER=localhost
DB_USERNAME=root
DB_PASSWORD=root
DB_DBNAME=database
```

## Package
### NPM
|Name|Description|
|-|-|
|@splidejs/splide|to create slider/carousel|
|boxicons|a multitude of icons to use|
|typescript|for Typescript only|


### Composer
|Name|Description|required|
|-|-|-|
|erusev/parsedown|markdown to HTML converter|no|
|ramsey/uuid|UUID generator|no|
|symfony/yaml|YAML interpreter|yes|
|simple-icons/simple-icons|Icon for brands/companies|no|
|fakerphp/faker|false information generator|no|
|kerogs/kerogs-php|toolbox just to simplify your life on certain things |no|
|vlucas/phpdotenv|quickly retrieves values from the ``.env'' file and automatically places them in the ``$_ENV'' variable.|yes|

## Features
- PHP preconfiguration
- SEO preconfiguration
- Pre-configured file tree
- Support TypeScript