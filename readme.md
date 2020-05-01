This library is heavily inspired by the [bear/qa-tools](https://github.com/bearsunday/BEAR.QATools), [koriym/Koriym.PhpSkeleton](https://github.com/koriym/Koriym.PhpSkeleton)

# A standard PHP project skeleton

## Create Project
   
To create your project, enter the following command in your console.    

```
composer create-project ysato/php-skeleton <project-path>
```

You will be asked a few questions to configure the project:

```
What is the vendor name ?

(MyVendor):Ysato

What is the package name ?

(MyPackage):AwesomePackage
```

## Composer Commands

Once installed, the project will automatically be configured so you can run those commands in the root of your application:

### test

`composer test` run [`phpunit`](https://github.com/sebastianbergmann/phpunit).

### tests

`composer tests` run [`phpcs`](https://github.com/squizlabs/PHP_CodeSniffer), [`php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer), [`phpstan`](https://github.com/phpstan/phpstan), [`psalm`](https://github.com/vimeo/psalm) and [`phpunit`](https://github.com/sebastianbergmann/phpunit). 

### coverage

`composer coverage` builds test coverage report.

### cs-fix

`composer cs-fix` run [`php-cs-fixer`](https://github.com/FriendsOfPHP/PHP-CS-Fixer) and [`phpcbf`](https://github.com/squizlabs/PHP_CodeSniffer/wiki/Fixing-Errors-Automatically) to fix up the PHP code to follow the coding standards. (Check only command `composer cs` is also available.)
