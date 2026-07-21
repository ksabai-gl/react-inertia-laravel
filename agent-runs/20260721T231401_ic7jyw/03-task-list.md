---
agent: code-takeover-design-spec-agent
cli: Cursor Agent CLI
llm: default (CLI-selected)
run_id: 20260721T231401_ic7jyw
generated_at: 2026-07-21T17:46:26.444Z
---

# Task List: Dashboard Recent Activity Filters

Detected stack: Laravel 12 + Inertia Laravel backend; React 19 + Inertia React + TypeScript/TSX + Vite frontend. Confidence: high.

## Phase 1: Analysis & Design Alignment

- [ ] **T-01 — Confirm client-side filter scope for Recent Activity** (priority: high · complexity: low · depends on: none)
  - **Files:** `frontend/resources/js/Pages/Dashboard.tsx`, `agent-runs/20260721T231401_ic7jyw/02-design-document.md`
  - **Description:** Align implementation with the design decision that status, module, region, and search filters are local React state over the existing `activity` prop, with no Laravel route, API, service, or migration changes.
  - **Acceptance criteria:** Code Gen plan references `Dashboard.tsx` as the only application source file required for implementation; `GET /` and `GET /api/dashboard` contracts remain unchanged; no database migration task is introduced.
  - **Traceability:** design §§I, IV, VIII; analysis `recommendations[0]`, `integration_points`; requirement "Add filter controls above Recent Activity in dashabord".

- [ ] **T-02 — Define activity row and filter state types** (priority: high · complexity: low · depends on: T-01)
  - **Files:** `frontend/resources/js/Pages/Dashboard.tsx`
  - **Description:** Refactor the inline `activity` prop item type into a named `ActivityRow` type and add an `ActivityFilters` type with `search`, `status`, `module`, and `region` fields using `all` sentinel values where appropriate.
  - **Acceptance criteria:** `DashboardProps.activity` uses `ActivityRow[]`; filter state type compiles under TypeScript; existing prop fields `name`, `phone`, `module`, `status`, `region`, and `updated` remain unchanged.
  - **Traceability:** design §III data model; analysis `field_summary`, `field_validations.general`.

## Phase 2: Foundations & Data Layer

- [ ] **T-03 — Add local filter state and derived option lists** (priority: high · complexity: medium · depends on: T-02)
  - **Files:** `frontend/resources/js/Pages/Dashboard.tsx`
  - **Description:** Import React `useMemo` and `useState`, initialize filters to `search=''`, `status='all'`, `module='all'`, and `region='all'`, and derive distinct status/module/region option arrays from `activity`.
  - **Acceptance criteria:** No filter options are hard-coded except user-facing `All` labels and the `all` sentinel; option derivation handles an empty `activity` array; component still renders all rows by default.
  - **Traceability:** design §§III, V, VIII; analysis gap "No canonical filter vocabulary is exported from backend".

- [ ] **T-04 — Implement filtered activity derivation** (priority: high · complexity: medium · depends on: T-03)
  - **Files:** `frontend/resources/js/Pages/Dashboard.tsx`
  - **Description:** Add a memoized `filteredActivity` calculation that applies search, status, module, and region filters with AND semantics and case-insensitive matching across `name`, `phone`, `module`, `region`, and `status`.
  - **Acceptance criteria:** With no active filters, `filteredActivity.length === activity.length`; selecting `failed` status returns only failed rows; combining module and region narrows results; searching `banking` matches `APAC Banking Path Map`; searching a non-existent term returns zero rows without throwing.
  - **Traceability:** design §§IV, V; analysis `validation_classifications.input`, `recommendations[0]`.

## Phase 3: Incremental Implementation

- [ ] **T-05 — Render filter controls above the Recent Activity table** (priority: high · complexity: medium · depends on: T-04)
  - **Files:** `frontend/resources/js/Pages/Dashboard.tsx`
  - **Description:** Add a filter control bar directly below the Recent Activity header and above the table, containing a search input, status select, module select, region select, and a Clear filters button styled with existing Tailwind utility patterns.
  - **Acceptance criteria:** Controls appear visually above the Recent Activity table; labels or `aria-label` attributes are present for accessibility; each control updates the matching filter state; Clear filters resets all controls to defaults.
  - **Traceability:** design §§II, IV, VII, VIII; user requirement "above Recent Activity".

- [ ] **T-06 — Update count badge and active filter feedback** (priority: medium · complexity: low · depends on: T-05)
  - **Files:** `frontend/resources/js/Pages/Dashboard.tsx`
  - **Description:** Change the Recent Activity count badge to display the filtered count and total count when any filter is active, while preserving the existing total-only display when filters are inactive.
  - **Acceptance criteria:** Initial badge shows `10 records` for current data; after selecting `failed`, badge shows `2 of 10 records`; after clearing filters, badge returns to `10 records`; wording handles singular `record` for count 1 if implemented.
  - **Traceability:** design §§IV, V; success criteria count updates.

- [ ] **T-07 — Render filtered rows, stable keys, and no-results state** (priority: high · complexity: low · depends on: T-04)
  - **Files:** `frontend/resources/js/Pages/Dashboard.tsx`
  - **Description:** Render `filteredActivity` in the table instead of `activity`, replace `key={row.name}` with a stable composite key such as `${row.name}-${row.phone}`, and add a single empty-state row when no rows match.
  - **Acceptance criteria:** Table body shows only filtered rows; duplicate `name` values would not collide when `phone` differs; zero-result filters display a readable empty-state row spanning all 5 table columns; empty state offers guidance to adjust or clear filters.
  - **Traceability:** design §§IV, V, VIII; analysis `performance_findings[1]`.

- [ ] **T-08 — Preserve backend contracts and avoid unnecessary source changes** (priority: medium · complexity: low · depends on: T-05, T-07)
  - **Files:** `backend/app/Services/DashboardService.php`, `backend/routes/web.php`, `backend/routes/api.php`, `backend/app/Http/Controllers/DashboardController.php`, `backend/app/Http/Controllers/Api/DashboardController.php`
  - **Description:** Verify no backend source files need modification for this enhancement and leave the existing service, route, controller, and API response contracts untouched.
  - **Acceptance criteria:** No diff exists in the listed backend files; existing `DashboardApiTest.php` expectations remain valid; no new request query parameters are introduced.
  - **Traceability:** design §§I, IV, VIII; analysis `integration_points`, `recommendations[0]`.

## Phase 4: Testing & Quality

- [ ] **T-09 — Run frontend lint and formatting checks** (priority: high · complexity: low · depends on: T-05, T-06, T-07)
  - **Files:** `frontend/package.json`, `frontend/resources/js/Pages/Dashboard.tsx`
  - **Description:** Execute the repository's existing frontend quality scripts after implementation and fix any ESLint or Prettier violations caused by the dashboard changes.
  - **Acceptance criteria:** `npm run lint` passes from `frontend/`; `npm run format:check` passes from `frontend/`; no unrelated formatting churn is introduced outside touched files.
  - **Traceability:** design §VI; analysis detected stack `testing`, `build_deploy`.

- [ ] **T-10 — Run frontend production build** (priority: medium · complexity: low · depends on: T-09)
  - **Files:** `frontend/package.json`, `frontend/resources/js/Pages/Dashboard.tsx`
  - **Description:** Build the React/Inertia/Vite frontend to catch TypeScript, import, and production bundling issues.
  - **Acceptance criteria:** `npm run build` passes from `frontend/`; TypeScript/TSX compile does not report filter state or prop type errors.
  - **Traceability:** design §VI; analysis detected stack `build_deploy`.

- [ ] **T-11 — Run backend dashboard regression tests** (priority: medium · complexity: low · depends on: T-08)
  - **Files:** `backend/tests/Feature/DashboardApiTest.php`, `backend/app/Services/DashboardService.php`, `backend/routes/web.php`, `backend/routes/api.php`
  - **Description:** Run the existing Pest feature tests that assert the dashboard API and Inertia page contracts remain unchanged.
  - **Acceptance criteria:** `DashboardApiTest.php` passes; API still returns 4 stats, 10 activity rows, 4 breakdown rows, and 5 regions; Inertia page still renders `Dashboard` with the same props.
  - **Traceability:** design §§IV, VI; analysis `integration_points`, `testing`.

## Phase 5: Handoff

- [ ] **T-12 — Prepare implementation handoff notes and manual review checklist** (priority: medium · complexity: low · depends on: T-09, T-10, T-11)
  - **Files:** `agent-runs/20260721T231401_ic7jyw/03-task-list.md`, implementation PR description or handoff note created by downstream agent
  - **Description:** Document the completed filter behavior, verification commands, and manual UI checks for reviewers; note that tasks roll up under one dashboard filter Epic/Story unless the team process requires separate tickets.
  - **Acceptance criteria:** Handoff states branch name, changed files, verification results, and manual checks for search/status/module/region/clear/empty-state flows; rollback note says revert `Dashboard.tsx` only; no PR is opened by this agent.
  - **Traceability:** design §§VI, VIII, X; pipeline handoff requirements.
