This is a project template for Drupal 8 sites built with the Openfed distribution.

## Usage

This project requires the installation of [composer](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx).

> Note: The instructions below refer to the [global composer installation](https://getcomposer.org/doc/00-intro.md#globally).
You might need to replace `composer` with `php composer.phar` (or similar)
for your setup.

After that you can create the project:

```
composer create-project openfed/openfed8-project:8.x MYPROJECT --stability dev --no-interaction
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
