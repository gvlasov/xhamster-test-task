# xhamster-test-task

### Running

```sh
composer install --ignore-platform-reqs
vendor/bin/sail up -d --wait
vendor/bin/sail exec laravel.test php artisan test
```
