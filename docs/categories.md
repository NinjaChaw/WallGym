# Category administration

The existing category screens now use Laravel resource routes and database records.
The category JavaScript only enhances previews, slug generation, and delete confirmation;
it no longer stores category data in sessionStorage.

## Database setup

Once the configured MySQL server is healthy, run these from the project directory:

```powershell
C:/xampp/php/php.exe artisan migrate --path=database/migrations/2026_09_16_162851_create_categories_table.php --path=database/migrations/2026_09_18_000001_align_category_defaults.php
C:/xampp/php/php.exe artisan db:seed --class=CategorySeeder
C:/xampp/php/php.exe artisan storage:link
```

These commands target only category migrations and sample categories. Do not use
`migrate:fresh` on an existing database. The seeder adds missing slugs and preserves
existing category edits; it does not seed users or other modules. Images are optional.

## Validation and images

CategoryRequest validates HTTP input using the metadata rules shared with Category.
The model also validates normal Eloquent save/create/update operations. Query-builder
updates bypass model events; use Eloquent instances for category writes. The unique
database index remains the final protection against duplicate slugs.

Name: 80 characters; slug: 100, lowercase letters/numbers separated by hyphens;
description: 300; status: boolean; display order: 1–999. Images: JPG, PNG, WebP,
maximum 1 MB. Files are stored under the public disk's categories directory.
Replacement and deletion clean up owned images after successful database writes;
failed writes remove newly uploaded files while retaining the old image.

Admin authentication/authorization is not yet implemented in this project. These
routes retain the existing local admin access model and must be protected before
deployment. Product relationships and storefront category integration are outside
this change; no fictional product counts are shown.

## Tests

Use the isolated SQLite in-memory configuration even if local config is cached:

```powershell
$env:APP_CONFIG_CACHE = Join-Path (Get-Location) 'storage/framework/category-tests-config.php'
C:/xampp/php/php.exe vendor/phpunit/phpunit/phpunit --filter CategoryCrudTest
Remove-Item Env:APP_CONFIG_CACHE
```

The category test class refuses to run against a non-memory database before the
RefreshDatabase setup. Coverage includes CRUD, route binding, input/model validation,
image lifecycle and failed saves, filtering, pagination, and repeatable seeding.
