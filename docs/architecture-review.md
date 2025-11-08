# CRM Plugin Architecture Review

## Current State Assessment

The repository currently contains only a minimal placeholder `README.md` and does not yet include any WordPress plugin scaffolding or business logic. There are no PHP source files, custom post type registrations, database migrations, REST endpoints, JavaScript assets, or automated tests. As a result, none of the Bitrix24-inspired capabilities (CRM entities, automation, dashboards, client portal, compliance tooling) are implemented.

## Gap Analysis Against Target Architecture

| Target Capability | Expected Components | Current Presence | Gap Summary |
| --- | --- | --- | --- |
| Core Plugin Skeleton | Plugin bootstrap (`investcrm.php`), autoloader, activation/deactivation hooks, namespaces, folder layout (`includes/`, `admin/`, `public/`, `assets/`). | Not present. | Create full plugin bootstrap with structured directories and autoloading. |
| Data Model (CRM Entities) | Custom post types or custom tables for Leads, Clients, Deals, Companies; relationship metadata; suitability/compliance fields. | Not present. | Design schema for clients, opportunities, products, activities, with foreign keys and metadata for compliance. |
| Pipelines & Kanban | Custom taxonomies for stages, REST endpoints, drag-and-drop UI (React/Vue), Kanban persistence. | Not present. | Implement REST layer and JavaScript admin app for pipeline management. |
| Tasks & Automations | CPT/table for tasks, scheduler hooks (`wp_cron`), workflow engine for triggers (stage change, new lead), notifications. | Not present. | Build automation dispatcher and task queue with cron-backed processing. |
| Reporting & Dashboards | Aggregation queries, Chart.js widgets, KPI calculations, export endpoints. | Not present. | Create reporting services and admin dashboards for KPIs and exports. |
| Client Portal | WordPress pages/templates, shortcodes or block theme, REST-secured API, document library integration. | Not present. | Implement portal templates, ensure capability checks, integrate chat/contact options. |
| Compliance & LGPD | Audit log table, consent tracking, suitability mandatory fields, role-based access control. | Not present. | Introduce audit logging middleware, data retention policies, capability checks. |
| Testing & Tooling | PHPUnit setup, Playwright/Cypress for UI, coding standards (PHPCS), CI workflows. | Not present. | Configure tests and CI pipeline to ensure quality and compliance. |

## Recommended Next Steps

1. **Scaffold the Plugin Foundation**
   - Generate the WordPress plugin entry point (`investcrm.php`) with headers, activation hooks, and autoloading.
   - Establish namespace conventions (e.g., `InvestCRM\`) and PSR-4 autoloading via Composer.
   - Prepare directory structure: `includes/` for core services, `admin/` for back-office UI, `public/` for portal, `assets/` for JS/CSS, `templates/` for shared markup.

2. **Define the Domain Model**
   - Create data transfer objects and repositories for Leads, Clients, Deals, Companies, Products, and Activities.
   - Decide between custom post types vs. dedicated database tables based on reporting/relationship needs. For compliance-heavy data, strongly consider custom tables with schema managed via `dbDelta` or migrations.
   - Map relationships (e.g., client-to-opportunities, opportunity-to-tasks) and enforce suitability/compliance fields as required attributes.

3. **Build CRM and Task Management Features**
   - Register pipelines with configurable stages stored as taxonomies or custom tables.
   - Develop REST controllers to move opportunities between stages and to create/update tasks.
   - Implement a Kanban UI using React (enqueue via `wp_enqueue_script`) mirroring Bitrix24 drag-and-drop experience.
   - Provide automation hooks to trigger tasks or notifications on stage changes, overdue tasks, or time-based events.

4. **Implement Reporting & Client Portal**
   - Aggregate metrics (conversion rates, AUM, time-to-close) using optimized queries or caching layers.
   - Construct dashboards with Chart.js and filter controls for consultants vs. managers.
   - Build client portal templates exposing profile, meetings, AUM, documents, and chat link; secure with capabilities and nonce validation.

5. **Integrate Compliance, Security, and LGPD Controls**
   - Add audit logging service to capture every CRUD event with user, timestamp, and before/after values.
   - Ensure consent tracking for leads/clients and enforce mandatory suitability review before deal closure.
   - Define custom roles (`consultant`, `manager`, `backoffice`) and granular capabilities to restrict data visibility.

6. **Quality Assurance Pipeline**
   - Set up PHPUnit with mockable services for business logic and Playwright/Cypress for admin/client portal flows.
   - Add CI workflows (GitHub Actions) for static analysis (PHPCS, PHPStan), testing, and linting of JavaScript assets.
   - Document coding standards and contribution guidelines in `CONTRIBUTING.md`.

## Suggested Repository Improvements

- Add a `docs/` folder with detailed architecture diagrams, database schema, and user flows for onboarding new contributors.
- Maintain an issue tracker or roadmap (e.g., `ROADMAP.md`) mapping Bitrix24 features to planned milestones.
- Introduce configuration management via environment files for API keys, SMTP, and external integrations.
- Establish localization support using `load_plugin_textdomain` and language files for multi-language readiness.

---
By implementing the scaffolding and domain-driven components outlined above, the project can progress toward a Bitrix24-like CRM tailored for investment consulting while ensuring compliance, scalability, and maintainability.
