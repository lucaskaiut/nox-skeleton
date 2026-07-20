---
name: template-governance
description: Use when the user asks about governance, template rules, CORE vs PROJECT classification, maintaining the relationship between the Origin Template (nox-skeleton) and derived projects, or before starting any implementation that may affect the platform core. This skill establishes mandatory rules for classifying changes and syncing between template and project repositories.
---

# Template Governance — nox-skeleton

## Context

This repository is the **Origin Template** — a **generic multi-tenant SaaS starter**.
It is NOT a CMS, ERP, or eCommerce. It is a foundation that any domain-specific
application can be built upon.

**Remote:** `template` → `git@github.com:lucaskaiut/nox-skeleton.git`

---

## Core Principle

> Template = Generic Infrastructure. Project = Domain Logic.

Every change in a derived project MUST be classified before implementation.
Generic infrastructure changes must flow **Template → Project**, not the reverse.

---

## What the Template Owns (CORE)

The template provides infrastructure that **any SaaS** needs, regardless of domain:

### Backend (`api/`)

| Concern | Path |
|---|---|
| Multi-tenancy (scope, context, resolver, strategies) | `api/app/Modules/Tenant/` |
| ACL / RBAC (permission enum, roles, HasRoles trait, middleware) | `api/app/Modules/ACL/` |
| Authentication (Sanctum + session SPA, register, login, logout) | `api/app/Modules/Auth/` |
| User management (tenant-scoped CRUD, soft deletes) | `api/app/Modules/User/` |
| API token management (scoped tokens, hashed storage) | `api/app/Modules/ApiToken/` |
| Shared utilities (ApiController, HasUuid, Document validators, ApiError) | `api/app/Modules/Shared/` |
| File upload (generic endpoint) | `api/app/Modules/Shared/Http/Controllers/FileUploadController.php` |
| Middleware (tenant, permission, api-token auth) | `api/app/Modules/*/Http/Middleware/` |
| Error handling / standardized JSON responses | `api/bootstrap/app.php` |
| Database structure (tenants, users, ACL, api_tokens, sessions, jobs) | `api/database/migrations/` |
| Factories and seeders for core entities | `api/database/factories/`, `api/database/seeders/` |
| Route conventions for core modules | `api/routes/api.php` |

### Frontend (`web/`)

| Concern | Path |
|---|---|
| Design System (Button, Card, DataTable, Input, Form, Modal, Sidebar, Topbar, ImageUploader, RichTextEditor, SlugField, TagSelector, SeoCard, etc.) | `web/src/shared/design-system/` |
| HTTP layer (Axios, CSRF, error interceptor) | `web/src/shared/api/` |
| State stores (session, theme, toast, UI) | `web/src/shared/stores/` |
| Shared types/constants (generic API types, query keys) | `web/src/shared/types/`, `web/src/shared/constants/` |
| Shared hooks and utilities (debounce, permissions, CN, format, document) | `web/src/shared/hooks/`, `web/src/shared/utils/` |
| Guards (Auth, Guest, Permission) | `web/src/app/guards/` |
| Layouts (AppLayout, AuthLayout) | `web/src/app/layouts/` |
| Router structure | `web/src/app/router/` |
| Providers (Query, Theme, Session) | `web/src/app/providers/` |
| CSS design tokens / theme variables | `web/src/index.css` |
| Auth module (login, register, session) | `web/src/modules/auth/` |
| Dashboard module (basic stats) | `web/src/modules/dashboard/` |
| Users module (generic CRUD) | `web/src/modules/users/` |
| Roles module (generic CRUD + permission checkboxes) | `web/src/modules/roles/` |
| API Tokens module (generic CRUD) | `web/src/modules/api-tokens/` |

### Infrastructure

| Concern | Path |
|---|---|
| Docker Compose (mysql, php-fpm, nginx, node/vite) | `docker-compose.yml` |
| PHP container (PHP 8.4 + extensions + Composer + startup script) | `docker/php/` |
| Nginx configuration (reverse proxy, security headers) | `docker/nginx/` |
| MySQL configuration (utf8mb4) | `docker/mysql/` |
| Web container (Node 22 + Vite startup script) | `docker/web/` |
| Environment templates | `api/.env.docker`, `web/.env.docker` |

### What the Template does NOT own

The template does NOT include domain-specific modules. Examples of PROJECT code:

- Blog / Posts / Categories / Tags
- eCommerce (Products, Orders, Cart)
- ERP (Invoices, Inventory, Suppliers)
- CRM (Leads, Deals, Pipelines)
- AI content publisher
- Any business-specific entity or workflow

These belong in the **derived project**, never in the template.

---

## Permission Enum — The Boundary

The `Permission` enum (`api/app/Modules/ACL/Enums/Permission.php`) is CORE, but
**only for infrastructure-level permissions**. Current core permissions:

- `user.create`, `user.read`, `user.update`, `user.delete`
- `tenant.read`, `tenant.update`
- `role.create`, `role.read`, `role.update`, `role.delete`
- `api-token.create`, `api-token.read`, `api-token.delete`

Domain-specific permissions (e.g., `post.create`, `product.read`, `invoice.update`)
are defined in the PROJECT, not here. The project should extend the enum or create
its own permission definitions.

---

## Design System — The Exception

Design System components are **always CORE**, regardless of what prompted their
creation. If you build an `ImageUploader` for a blog project, it stays in the
template because any SaaS might need image uploads. Same for `RichTextEditor`,
`SlugField`, `TagSelector`, `SeoCard` — they are UI primitives, not domain logic.

---

## Classification Rules

### CORE
Generic infrastructure reusable by ANY derived project. **Implement in the Template.**

### PROJECT
Domain-specific business logic. **Implement ONLY in the derived project.**

### HYBRID
Extract the generic part to the Template; keep the specific part in the Project.

---

## Decision Questions

1. **Would an ERP, eCommerce, or CRM need this?** If NO → PROJECT.
2. **Is this a UI primitive or infrastructure concern?** If YES → CORE.
3. **Is this tied to a specific business domain?** If YES → PROJECT.
4. **Would this exist in every SaaS regardless of niche?** If YES → CORE.

---

## Priority

This governance document has priority over implicit project conventions.
All agents, developers, and automations MUST consult and follow these
guidelines before starting any implementation.
