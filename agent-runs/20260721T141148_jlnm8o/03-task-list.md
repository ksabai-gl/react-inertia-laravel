---
agent: task-list-agent
cli: Cursor Agent CLI
llm: gpt-5.3-codex-fast
run_id: 20260721T141148_jlnm8o
generated_at: 2026-07-21T08:47:06.400Z
---

# 03 Task List — Dashboard/API Hardening and Data Path Stabilization

## Repository issues identified for this run

- Open code issue: `/api/dashboard` lacks explicit authentication policy enforcement on `main` (`backend/routes/api.php`).
- Open code issue: `DashboardService` still serves hard-coded in-memory data (`backend/app/Services/DashboardService.php`).
- Open quality issue: no explicit API response-schema contract tests guarding drift (`backend/tests/Feature/DashboardApiTest.php`).
- Open hardening issue: standardized API error envelope is designed but not consistently implemented in exception handling.
- Open repo-level items: two open PR-backed GitHub issues (`#1`, `#2`) remain in review workflow and are not auto-mergeable by this agent.

## Phase 1: Analysis & Design Alignment

- [ ] **T-01 — Reconcile analysis/design findings into an implementation map** (priority: high · complexity: low · depends on: none)
  - **Files:** `agent-runs/20260721T141148_jlnm8o/01-code-analysis.md`, `agent-runs/20260721T141148_jlnm8o/analysis_output.json`, `agent-runs/20260721T141148_jlnm8o/02-design-document.md`
  - **Description:** Validate that every field/validation finding and each design decision (API hardening, service contract, SSR safety, observability) is represented by concrete implementation tasks in this run.
  - **Acceptance criteria:** All findings in `issues`, `security_findings`, and `recommendations` map to at least one task id; no unmapped high/medium item remains.
  - **Traceability:** analysis `issues[*]`, `security_findings[*]`, `recommendations[*]`; design §§IV, V, VII, VIII.

- [ ] **T-02 — Decide and codify `/api/dashboard` access policy** (priority: high · complexity: low · depends on: T-01)
  - **Files:** `backend/routes/api.php`, `backend/app/Http/Controllers/Api/DashboardController.php`, `backend/tests/Feature/DashboardApiTest.php`
  - **Description:** Resolve open design question on API privacy and encode the chosen policy (public-with-constraints or authenticated) so behavior is deterministic and testable.
  - **Acceptance criteria:** Route middleware reflects approved policy; unauthorized path behavior is covered by feature tests and documented in endpoint contract notes.
  - **Traceability:** analysis `security_findings[0]`; design §X (auth requirement open question).

- [ ] **T-03 — Define canonical API error contract and ownership point** (priority: high · complexity: medium · depends on: T-01)
  - **Files:** `backend/bootstrap/app.php`, `backend/app/Http/Controllers/Api/DashboardController.php`, `backend/tests/Feature/DashboardApiTest.php`
  - **Description:** Convert the design error-envelope proposal into a concrete implementation owner (exception layer vs controller fallback) with explicit codes and response shape.
  - **Acceptance criteria:** Error schema (`error.code`, `error.message`, optional `request_id`) is documented and test assertions exist for 429 + internal failure path.
  - **Traceability:** design §§IV–V, VII.

## Phase 2: Foundations & Data Layer

- [ ] **T-04 — Enforce route middleware baseline for dashboard API** (priority: high · complexity: low · depends on: T-02)
  - **Files:** `backend/routes/api.php`
  - **Description:** Apply the final middleware chain (throttle + policy-driven auth/ability middleware) on `/api/dashboard` before business logic changes.
  - **Acceptance criteria:** Route group includes approved middleware; endpoint still returns success payload on authorized happy path.
  - **Traceability:** analysis `issues[2]`; design §§II, VII.

- [ ] **T-05 — Externalize throttle and endpoint hardening settings to config/env** (priority: medium · complexity: low · depends on: T-04)
  - **Files:** `backend/config/app.php`, `backend/.env.example`, `backend/routes/api.php`
  - **Description:** Move hardening knobs (e.g., throttle profile or per-endpoint limit constants) to configuration so environments can tune behavior without code edits.
  - **Acceptance criteria:** No route-level magic numbers remain; environment defaults are documented in `.env.example`; config cache loads successfully.
  - **Traceability:** design §§VI, VIII.

- [ ] **T-06 — Introduce persistent dashboard data path scaffolding with rollback guard** (priority: medium · complexity: high · depends on: T-01)
  - **Files:** `backend/database/migrations/2026_07_21_000001_create_dashboard_summaries_table.php`, `backend/app/Services/DashboardService.php`, `backend/database/seeders/DatabaseSeeder.php`, `backend/config/app.php`
  - **Description:** Add migration-ready storage scaffolding and feature-flagged read-path preparation so the service can evolve from static arrays without breaking current output.
  - **Acceptance criteria:** Migration is reversible; service can run in compatibility mode (existing static output) when flag is disabled; seed path is idempotent.
  - **Traceability:** analysis `performance_findings[1]`, `recommendations[1]`; design §III and §VIII.

## Phase 3: Incremental Implementation

- [ ] **T-07 — Stabilize `DashboardService` response schema with explicit contract checks** (priority: high · complexity: medium · depends on: T-06)
  - **Files:** `backend/app/Services/DashboardService.php`
  - **Description:** Guarantee that `summary()` always returns required keys and normalized value types (`stats`, `activity`, `breakdown`, `regions`) irrespective of data source.
  - **Acceptance criteria:** Missing/invalid internal data does not leak malformed payload; service emits controlled domain exception or normalized fallback.
  - **Traceability:** analysis `recommendations[2]`; design §V validation matrix (`DashboardService` response schema).

- [ ] **T-08 — Implement standardized API response/error mapping in dashboard API controller flow** (priority: high · complexity: medium · depends on: T-03, T-07)
  - **Files:** `backend/app/Http/Controllers/Api/DashboardController.php`, `backend/bootstrap/app.php`
  - **Description:** Wire success and failure paths to the agreed API envelope and ensure throttle/internal errors align with contract.
  - **Acceptance criteria:** Success response remains backward-compatible for existing fields; 429 and internal failures return contract-compliant JSON.
  - **Traceability:** design §§IV–V.

- [ ] **T-09 — Keep Inertia dashboard web contract aligned with service normalization** (priority: medium · complexity: low · depends on: T-07)
  - **Files:** `backend/app/Http/Controllers/DashboardController.php`, `frontend/resources/js/Pages/Dashboard.tsx`
  - **Description:** Ensure web route rendering uses the same normalized data contract as API so frontend behavior remains stable across entry points.
  - **Acceptance criteria:** Dashboard page renders with unchanged core widgets; no undefined prop access from changed service output.
  - **Traceability:** analysis architectural dependency mapping; design §§II, VIII.

- [ ] **T-10 — Tighten frontend dashboard typing against backend schema guarantees** (priority: medium · complexity: low · depends on: T-09)
  - **Files:** `frontend/resources/js/Pages/Dashboard.tsx`, `frontend/resources/js/types/index.d.ts`, `frontend/resources/js/types/global.d.ts`
  - **Description:** Update TypeScript interfaces to mirror stabilized backend contract and prevent silent drift in UI rendering.
  - **Acceptance criteria:** Type checks pass with strict shape compatibility; no `any` fallback introduced for dashboard payload.
  - **Traceability:** design §IV (contract), §V (schema stability).

- [ ] **T-11 — Add request-context logging hooks for dashboard API failures and throttling** (priority: medium · complexity: medium · depends on: T-08)
  - **Files:** `backend/config/logging.php`, `backend/app/Http/Controllers/Api/DashboardController.php`, `backend/bootstrap/app.php`
  - **Description:** Emit structured log fields for route, status, correlation id, and exception class to support production triage and throttle tuning.
  - **Acceptance criteria:** Error and rate-limit scenarios produce traceable log entries without exposing sensitive payload content.
  - **Traceability:** design §§II, VI, VII.

## Phase 4: Testing & Quality Control

- [ ] **T-12 — Add feature tests for auth policy, throttle behavior, and error envelope** (priority: high · complexity: medium · depends on: T-04, T-08)
  - **Files:** `backend/tests/Feature/DashboardApiTest.php`, `backend/tests/Pest.php`
  - **Description:** Extend API tests to cover authorized/unauthorized paths, 429 behavior, and standardized error schema assertions.
  - **Acceptance criteria:** Tests verify policy outcomes and envelope keys (`error.code`, `error.message`); no regression in existing happy path assertions.
  - **Traceability:** analysis `security_findings[0]`, `recommendations[2]`; design §§IV–V.

- [ ] **T-13 — Add service-level contract tests for dashboard summary completeness** (priority: medium · complexity: medium · depends on: T-07)
  - **Files:** `backend/tests/Unit/DashboardServiceTest.php`
  - **Description:** Add unit tests that assert required summary keys, value-shape invariants, and controlled behavior under data-source edge cases.
  - **Acceptance criteria:** Unit tests fail on missing schema keys or invalid structures; pass on normalized expected output.
  - **Traceability:** design §V validation matrix (`DashboardService` response schema).

- [ ] **T-14 — Execute stack-aligned quality gates (lint/type/build/test) and capture regression scope** (priority: high · complexity: low · depends on: T-10, T-12, T-13)
  - **Files:** `frontend/eslint.config.js`, `frontend/package.json`, `backend/composer.json`, `backend/tests/Feature/ExampleTest.php`
  - **Description:** Run project-standard verification (`php artisan test`, frontend lint/type/build) and document impacted regression areas (dashboard web + API paths).
  - **Acceptance criteria:** Required commands pass in CI/local equivalent; regression checklist explicitly includes unchanged-but-adjacent auth/session and dashboard rendering flows.
  - **Traceability:** design §VI CI/CD requirements.

## Phase 5: Handoff Summary

- [ ] **T-15 — Produce release notes, compatibility/rollback guidance, and parent-ticket rollup** (priority: medium · complexity: low · depends on: T-14)
  - **Files:** `README.md`, `agent-runs/20260721T141148_jlnm8o/03-task-list.md`
  - **Description:** Capture deployment notes (middleware/policy changes, migration toggles, rollback path), manual-review flags, and task rollup under one parent tracking issue.
  - **Acceptance criteria:** Handoff includes compatibility statement, rollback steps, and explicit parent ticket note (`Parent: one Story/Task for run 20260721T141148_jlnm8o`).
  - **Traceability:** design §§VIII–X; output rule Phase 5 requirements.
