# Template Governance — nox-skeleton

> The template is a **generic multi-tenant SaaS starter** — not a CMS, ERP, or eCommerce.

**Remote:** `template` → `git@github.com:lucaskaiut/nox-skeleton.git`

---

## Template = Generic Infrastructure

The template provides what **any SaaS** needs: multi-tenancy, ACL, auth, user/role/token management, Design System, layouts, guards, Docker.

**Project** code is domain-specific: posts, products, invoices, leads, AI publishers.

---

## CORE (Template)

| Layer | Concern |
|---|---|
| **Backend** | Tenant resolver, ACL (RBAC), Auth (Sanctum), User/Token CRUD, Shared utils, File upload, Middleware, Error handling, DB structure for core entities |
| **Frontend** | Design System components, HTTP layer, Stores, Guards, Layouts, Router, Providers, Auth/Dashboard/Users/Roles/Tokens modules |
| **Infra** | Docker Compose, PHP/Nginx/MySQL/Node containers |

## PROJECT (Application)

| Concern |
|---|
| Blog / Posts / Categories |
| Products / Orders / eCommerce |
| Invoices / ERP |
| CRM entities |
| AI content publisher |
| Any domain-specific entity or workflow |

---

## Key Rules

- **Permission enum** in template contains only infrastructure permissions (user.*, tenant.*, role.*, api-token.*). Domain permissions go in the project.
- **Design System components** are always CORE — UI primitives, not domain logic.
- **Decision test:** "Would an ERP, eCommerce, or CRM need this?" No → PROJECT.

---

## Closing Checklist

- [ ] Change classified?
- [ ] If CORE: Template updated first?
- [ ] No divergence between Template and Project?
