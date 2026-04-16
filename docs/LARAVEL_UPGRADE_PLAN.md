# HDSPTV Laravel Upgrade Plan (Phased)

This repository is currently a custom PHP application. A direct in-place rewrite to Laravel in one step is high risk for production.

This plan introduces Laravel in **phases** while preserving existing behavior.

## Phase 1 — Routing Parity (current delivery)

- Create Laravel route parity map for all public/admin URLs.
- Add a legacy proxy controller so Laravel can serve existing pages during transition.
- Keep existing PHP pages functional while Laravel bootstraps.

## Phase 2 — Authentication + Sessions

- Replace custom auth handlers with Laravel auth guards.
- Migrate admin/user sessions to Laravel middleware.
- Port CSRF and throttling into Laravel middleware/policies.

## Phase 3 — Data Layer Migration

- Convert `hs_*` DB access calls to Eloquent models/repositories.
- Introduce migrations for schema currently defined in `install/install.sql`.
- Build seeders for settings/users/categories/tags sample data.

## Phase 4 — Controller/View Migration

- Migrate public pages to Laravel controllers + Blade views.
- Migrate admin panel pages to Laravel controllers + Blade layouts.
- Keep legacy proxy endpoints for fallback until each page is fully migrated.

## Phase 5 — Install/Deploy

- Replace custom installer with Laravel environment + migration/seeder commands.
- Update web server config for Laravel `public/index.php`.
- Remove legacy bridge only after route and feature parity is verified.

## Operational Notes

- Do not remove existing legacy files until parity is confirmed.
- Use staged rollouts (public routes first, admin routes after RBAC parity).
- Validate every migrated route against the existing URL contract.

