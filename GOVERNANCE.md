# Template Governance — nox-skeleton

> Priority: This document overrides implicit project conventions.
> All agents and developers must consult it before any implementation.

## Context

This repository is the **Origin Template** — the CORE of the NOX CMS platform.
It is the single source of truth for all generic, reusable, and structural code.

- **Remote:** `template` → `git@github.com:lucaskaiut/nox-skeleton.git`

## Core Principle

Every change in a derived project MUST be classified before implementation.
CORE changes must flow **Template → Project**, not the reverse.

---

## CORE vs PROJECT

### Template owns (CORE)

| Layer | Concern |
|---|---|
| **API** | Multi-tenancy, ACL/RBAC, Auth (Sanctum + session), User CRUD, Role CRUD, API tokens, Shared utilities, Middleware, Error handling, DB structure, Factories/Seeders, Route conventions |
| **Web** | Design System (28 components), HTTP layer (Axios/CSRF), State stores, Guards, Layouts, Router, Providers, CSS tokens/themes, Auth/Dashboard/Users/Roles/API Tokens modules |
| **Infra** | Docker Compose, PHP/Nginx/MySQL/Node containers, Environment templates |

### Project owns (PROJECT)

| Concern |
|---|
| Domain entities (Product, Invoice, School…) |
| Business workflows |
| Custom integrations (ERP, payments…) |
| Specific reports |
| Client-specific features |

---

## Classification

### CORE
Reusable by any derived project. **Implement FIRST in the Template**, then
propagate to the project.

### PROJECT
Specific to the current system. Implement **only in the project**. Never
modify the Template.

### HYBRID
Both generic and specific parts. Extract the reusable portion to the
Template; keep the specific portion in the project.

---

## Decision Questions

1. Would this be useful for other derived projects?
2. Is there any reusable part?
3. Am I altering infrastructure, layout, components, or architecture?
4. Am I adding only a domain-specific business rule?
5. Should this exist in every system created from this Template?

If unsure → classify as **HYBRID** and document the reasoning.

---

## Preferred Flow

```
Template → validate → version → propagate → Project → validate
```

Reverse flow (Project → Template) is allowed only for emergency fixes
or when work was already started in the project. **Syncing back to the
Template is mandatory before closing the task.**

---

## Closing Checklist

- [ ] Change classified (CORE / PROJECT / HYBRID)?
- [ ] Reusable part identified?
- [ ] If CORE: Template updated first?
- [ ] No divergence between Template and Project?
- [ ] Commits respect the classification?

No CORE task is complete while divergence exists between Project and Template.
