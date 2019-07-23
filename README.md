This is a project template for Drupal 8 sites built with the Openfed distribution.

## File Structure

There are 3 json files:

### composer.json

This will be the main json file for your project, which you can use to require extra repositories.
You can override this at your will, just make sure that **composer-merge-plugin** settings and package are kept in order to use the json files mentioned bellow.

For more info about **composer-merge-plugin** settings and options check https://github.com/wikimedia/composer-merge-plugin

### composer.openfed.json

This will include all Openfed related settings and should not be changed once you create your project. However, you should update this file regularly based on the most recent version in this repository.

### composer.libraries.json

This will include all the required libraries for your Openfed project and should not be changed. However, you should update this file regularly based on the most recent version in this repository.

## Usage

This project requires the installation of [composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

> Note: The instructions below refer to the [global composer installation](https://getcomposer.org/doc/00-intro.md#globally).
You might need to replace `composer` with `php composer.phar` (or similar)
for your setup.

After that you can create the project:

```
composer create-project openfed/openfed8-project:^6.0 MYPROJECT
```

With `composer require ...` you can download new dependencies to your
installation.

```
cd MYPROJECT
composer require drupal/devel:~1.0
```

The `composer create-project` command passes ownership of all files to the
project that is created. You should create a new git repository, and commit
all files not excluded by the .gitignore file.


## Update from Openfed8-project <=8.5.x

Copy all .json files to your project folder and make sure that your project required packages are copied to the new composer.json file.

## Troubleshooting

### Memory limit errors

When running "composer install" you may get some memory limit issues. This is due to the composer dependency resolver since we have a big list of dependencies. 
To bypass this issue, you have 3 options:

#### option 1

Temporarely increase the memory limit as described at https://getcomposer.org/doc/articles/troubleshooting.md#memory-limit-errors

#### option 2

If you are creating the project for the first time, use the recommended installation procedure by using "composer create-project" command.

#### option 3

Run "composer update" twice. At first it will throw the same error but on the second attempt it will run successfully.
