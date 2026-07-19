---
name: template-governance
description: Use when the user asks about governance, template rules, CORE vs PROJECT classification, maintaining the relationship between the Origin Template (nox-skeleton) and derived projects, or before starting any implementation that may affect the platform core. This skill establishes mandatory rules for classifying changes and syncing between template and project repositories.
---

# Template Governance — nox-skeleton

## Context

This repository is the **Origin Template** (`nox-skeleton`) — the CORE of the
NOX CMS platform. It is the single source of truth for all generic, reusable,
and structural code. Projects derived from this template must never evolve the
CORE independently without the change being reflected here first.

**Remote:** `template` → `git@github.com:lucaskaiut/nox-skeleton.git`

---

## Core Principle

> Template = Source of Truth

Every change made to a derived project MUST be classified before
implementation. Changes to the CORE must flow **from Template → Project**,
not the reverse.

---

## What the Template Owns (CORE)

### Backend (`api/`)

| Concern | Path |
|---|---|
| Multi-tenancy infrastructure | `api/app/Modules/Tenant/` |
| ACL / RBAC | `api/app/Modules/ACL/` |
| Authentication (Sanctum + session) | `api/app/Modules/Auth/` |
| Shared utilities, base controller, UUID trait, document validators | `api/app/Modules/Shared/` |
| User management (tenant-scoped CRUD) | `api/app/Modules/User/` |
| API token management | `api/app/Modules/ApiToken/` |
| Middlewares (tenant, permission, api-token) | `api/app/Modules/*/Http/Middleware/` |
| Error handling / standardized JSON responses | `api/bootstrap/app.php`, `api/app/Modules/Shared/Http/ApiError.php` |
| Database structure (tenants, users, ACL, api_tokens) | `api/database/migrations/` |
| Factories and seeders | `api/database/factories/`, `api/database/seeders/` |
| Route structure conventions | `api/routes/api.php` |
| PHP configuration and testing setup | `api/phpunit.xml`, `api/config/` |

### Frontend (`web/`)

| Concern | Path |
|---|---|
| Design System (28 components) | `web/src/shared/design-system/` |
| HTTP layer (Axios, interceptors, CSRF) | `web/src/shared/api/` |
| State stores (session, theme, toast, UI) | `web/src/shared/stores/` |
| Shared types and constants (permissions, query keys) | `web/src/shared/types/`, `web/src/shared/constants/` |
| Shared hooks and utilities | `web/src/shared/hooks/`, `web/src/shared/utils/` |
| Guards (Auth, Guest, Permission) | `web/src/app/guards/` |
| Layouts (AppLayout, AuthLayout) | `web/src/app/layouts/` |
| Routing structure | `web/src/app/router/` |
| Providers (Query, Theme, Session) | `web/src/app/providers/` |
| CSS design tokens / theme variables | `web/src/index.css` |
| Auth module (login, register, session) | `web/src/modules/auth/` |
| Dashboard module | `web/src/modules/dashboard/` |
| Users module | `web/src/modules/users/` |
| Roles module | `web/src/modules/roles/` |
| API Tokens module | `web/src/modules/api-tokens/` |

### Infrastructure

| Concern | Path |
|---|---|
| Docker Compose orchestration | `docker-compose.yml` |
| PHP container build + startup | `docker/php/` |
| Nginx configuration | `docker/nginx/` |
| MySQL configuration | `docker/mysql/` |
| Web (Node/Vite) container build + startup | `docker/web/` |
| Environment templates | `api/.env.docker`, `web/.env.docker` |

---

## Classification Rules

Before starting any implementation, the agent MUST classify the change:

### CORE

Changes reusable by any derived project. Examples:

- Design System components
- Layouts, routing, providers
- ACL / permissions
- Authentication flows
- User / Role / API token management
- Multi-tenancy infrastructure
- Middleware
- Shared hooks, helpers, utilities
- API architecture and conventions
- Docker/infrastructure
- Performance improvements
- Bug fixes in framework-level code

**Process:**

1. Identify the change.
2. Assess whether it could impact other derived projects.
3. Implement FIRST in the Origin Template whenever technically feasible.
4. Validate compatibility.
5. Propagate to the current project.
6. Commit to both repos when necessary.
7. Update related documentation (CHANGELOG, this governance file, etc.).

### PROJECT

Changes specific to the current system's business rules. Examples:

- Domain-specific entities (e.g., `Product`, `Invoice`, `School`)
- Business workflows
- Custom integrations (e.g., payment gateways, ERPs)
- Specific reports and dashboards
- Client-specific features
- Domain-specific form fields and validations

**Process:**

1. Implement ONLY in the current project.
2. Do NOT modify the Origin Template.
3. Commit only to the current project.

### HYBRID

Changes containing both generic and specific parts.

**Process:**

1. Identify what is CORE and what is PROJECT.
2. Extract the reusable portion.
3. Implement the reusable portion in the Template.
4. Implement the specific portion only in the project.
5. Commit independently.

---

## Decision Flowchart

Before every implementation, answer these questions:

1. **Would this feature be useful for other derived projects?**
2. **Is there any reusable part?**
3. **Am I altering infrastructure, layout, components, or architecture?**
4. **Am I adding only a domain-specific business rule?**
5. **Should this change exist in every system created from this Template?**

Based on the answers, classify as CORE, PROJECT, or HYBRID.

## When in Doubt

1. Assume **HYBRID**.
2. Document the justification.
3. Explain which parts were considered CORE and why.
4. Explain which parts were considered PROJECT and why.

---

## Preferred Evolution Flow

```
Template → validate → version → propagate → Project → validate
```

Avoid the reverse flow (Project → Template) **except** when:

- The change was already started in the project.
- There are technical impediments preventing Template-first work.
- It is an emergency fix.

In such cases, syncing back to the Template becomes **mandatory before
closing the task**.

---

## Mandatory Checklist Before Closing a Task

- [ ] Was the change classified as CORE, PROJECT, or HYBRID?
- [ ] Was any reusable part identified?
- [ ] If CORE: was the Template updated FIRST?
- [ ] Is there divergence between Template and Project?
- [ ] Is this governance document still accurate?
- [ ] Do the commits respect the classification?

**No CORE-related task is complete while divergence exists between the
Project and the Origin Template.**

---

## Priority

This governance document has priority over implicit project conventions.

All agents, developers, and automations MUST consult and follow these
guidelines before starting any implementation.
