# Ticket Module

This is a Ticket module for a Laravel application (>= 8) 

it use layout module and auth module.



# Installation & configuration

By default, tickets can be created by "user" (role) users and managed by "admin" (role) users. 
A minimal configuration for your config/auth.php file is:

```
'role_permissions' => [
        'admin' => [
            'view tickets', 'edit tickets',
        ],
        'customer' => [
            'view own tickets', 'edit own tickets',
        ],
]
```



this module use livewire charts so you need to publish some assets:

```bash
php artisan vendor:publish --tag=livewire-charts:public
```

if you customize your layout make sure you include this directive 

```
@livewireChartsScripts
```




# Customizing Module
To customize the module code, we recommend forking the original package repository on GitHub and making changes there. This way, you can maintain a separate branch for your changes, while also keeping up-to-date with the latest releases of the package.

To install your forked version of the package in your Laravel application, you can reference your forked repository in the composer.json file of your Laravel application using the "vcs" package type. Here's an example of what you can add to your composer.json:

```json

"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/<your-github-username>/<package-name>"
    }
],
```
Replace `<your-github-username>` with your GitHub username and `<package-name>` with the name of your forked package repository.

After adding your forked repository to composer.json, you can require your customized package in the same way you would require the original package:

```php
composer require <your-github-username>/<package-name>:dev-<your-branch-name>
```
Replace `<your-github-username>`, `<package-name>`, and `<your-branch-name>` with the appropriate values for your forked repository and branch.

By using this approach, you can easily customize the module code while also keeping your code up-to-date with the latest releases of the package.

We encourage developers to make changes that could be useful to the wider community and contribute back to the original package repository via pull requests. This can help improve the package for everyone and ensure that your changes are integrated with the latest releases of the package.
