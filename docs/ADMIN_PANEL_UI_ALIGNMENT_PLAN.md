## A. Admin panel design principles

### Tone
- Professional, calm, high-trust newsroom command center.
- Editorial-first, low-noise visuals with operational urgency only where needed (breaking/live/error).
- Enterprise clarity over decorative UI.

### Usability goals
- Reduce clicks for high-frequency tasks (publish, review, moderate, alert).
- Keep every critical action visible and grouped by workflow stage.
- Improve scan speed for busy editors and operators.

### Product behavior principles
- One shell, one grid, one component language.
- Predictable status semantics across all modules.
- Read-first, act-fast hierarchy for time-sensitive newsroom work.
- Every page supports loading, empty, error, and success states.

---

## B. Global admin frame system

### Canvas sizes
- Desktop: 1440px canvas, 1280px content max.
- Tablet: 1024px.
- Mobile: 390px.

### Grid
- 12-column grid with 24px gutter.
- Default admin body: full-width content rail inside shell.
- Widget rows lock to equal-height behavior per row.

### Spacing
- 8px scale: 8/16/24/32/40/48.
- Section rhythm: 32–48.
- Card padding: 20 or 24.
- Input heights: 48–52.

### Shell
- Fixed left sidebar on desktop.
- Sticky top action bar.
- Shared page header region: title, context, quick actions.

### Sidebar
- Width: 272px fixed desktop.
- Single nav pattern (icon/text optional), no duplicates.
- Grouping: Editorial, Operations, Audience/Revenue, Platform.

### Top bar
- Sticky with page title and global actions.
- Includes role context, quick create, and notifications entry point.

### Page body rules
- Title aligns to same x-origin across all pages.
- Filters, table headers, cards, and actions align to grid columns.
- No floating orphan actions.

---

## C. Admin design system

### Colors
- Primary `#D60000`, Dark `#111111`, Deep Navy `#0B1220`, BG `#F6F7FB`, Card `#FFFFFF`, Border `#E5E7EB`, Gray `#6B7280`, Success `#16A34A`, Warning `#F59E0B`, Error `#DC2626`, Info `#2563EB`.

### Typography
- H1: 36–40, H2: 28–32, H3: 20–24, Body: 14–16, Meta/labels: 12–13.
- Dense data views use 14px body, 12px meta.

### Buttons
- Primary (red filled), secondary (white/border), tertiary (text action).
- All action rows use the same ordering: primary rightmost.

### Badges
- Unified states: Draft, Submitted, Under Review, Fact Check, Approved, Scheduled, Published, Updated, Rejected, Archived.
- Semantic mapping fixed per state.

### Cards
- Standard radius 16, large modal 20.
- Card anatomy: header, content, footer.

### Tables
- Single table system with consistent header spacing, row height, and row actions.
- Responsive fallback to stacked row cards on small screens.

### Forms
- Label on top, helper/error below field.
- Validation style: inline + summary banner for blocked submits.

### Modals
- 20px radius, fixed action row, escape/close consistency.
- Destructive action requires explicit confirmation.

### Alerts
- One alert banner pattern: info/warning/error/success.
- Placed below top bar, above page content.

### Chart styles
- Uniform chart card with title, period selector, legend, and data source notes.
- Avoid chart crowding; minimum 240px chart area.

---

## D. Page-by-page admin UI alignment plan

> For each page: purpose, users, frame, section order, alignment, components, action hierarchy, responsive notes, errors prevented.

### 1) Admin Login
- **Purpose:** secure staff entry.
- **Users:** all authenticated newsroom roles.
- **Frame:** two-column trust panel + auth card.
- **Order:** brand/trust > form > support/help.
- **Alignment:** 20px auth card radius, 48–52px inputs, inline error box.
- **Components:** auth shell, form fields, primary/secondary buttons, alert.
- **Hierarchy:** Sign in primary, forgot/support secondary.
- **Responsive:** single-column stack mobile.
- **Errors prevented:** unclear role access, hidden validation, weak affordance.

### 2) Dashboard
- **Purpose:** operational command center.
- **Users:** Super Admin, Admin, Editor-in-Chief, Senior Editor.
- **Frame:** KPI row + ops widgets + monitoring table.
- **Order:** KPI > breaking/live controls > workflow tables/charts.
- **Alignment:** equal KPI card heights, consistent row starts.
- **Components:** KPI cards, chart cards, table, status badges.
- **Hierarchy:** critical alerts first, analytics second.
- **Responsive:** KPI cards 4→2→1 columns.
- **Errors prevented:** cramped metrics, mixed widget rhythm.

### 3) All News
- Purpose: master content inventory.
- Users: editors/reporters.
- Frame: filter bar + table/card area.
- Order: filters/search > bulk actions > list > pagination.
- Alignment: filter controls baseline-aligned.
- Components: filter bar, table, badges, row actions.
- Hierarchy: publish/review actions above destructive actions.
- Responsive: filters collapse to drawer.
- Errors prevented: column misalignment, action scatter.

### 4) Create News
- Purpose: produce and schedule content.
- Users: reporters/editors.
- Frame: 8/4 editor + publish rail.
- Order: title/meta > body > embeds/media > SEO/publish.
- Alignment: sticky save/publish group in right rail.
- Components: rich editor blocks, form fields, toggles.
- Hierarchy: save draft, submit review, publish.
- Responsive: right rail stacks below editor.
- Errors prevented: hidden publish settings, broken label spacing.

### 5) Edit Article
- Same scaffold as Create News + revision timeline and correction notes panel.

### 6) Drafts
### 7) Pending Review
### 8) Scheduled Posts
### 9) Published Posts
- Purpose: status-specific queues.
- Users: editors/publishers.
- Frame: shared content-list template with preset filters.
- Alignment: identical table structure across queues.
- Components: filter bar, table, status badges, bulk actions.
- Responsive: stacked row cards on mobile.
- Errors prevented: duplicate inconsistent list designs.

### 10) Breaking News Manager
- Purpose: high-urgency alert operations.
- Users: Editor-in-Chief, Senior Editor, Live Operator.
- Frame: active/scheduled/expired columns + ticker preview.
- Components: alert cards, urgency badges, timeline rows.
- Hierarchy: active alert controls pinned top.
- Errors prevented: accidental overwrite, unclear active state.

### 11) Live TV / Stream Control
- Purpose: broadcast operations.
- Users: Live Stream Operator, Super Admin.
- Frame: preview + controls + health diagnostics.
- Components: player panel, source selector, health cards, emergency modal.
- Errors prevented: unsafe override placement, unreadable stream status.

### 12) Homepage Manager
- Purpose: front-page curation.
- Users: Editor-in-Chief, Section Editors.
- Frame: visual section-order board + properties panel.
- Components: draggable section cards, pin actions, visibility toggles.
- Errors prevented: duplicate section rendering, unclear priority.

### 13) Categories Manager
- Purpose: taxonomy governance.
- Users: section editors/SEO editor.
- Frame: tree-table hybrid + detail editor.
- Errors prevented: hierarchy conflicts, slug inconsistency.

### 14) Tags Manager
- Purpose: tag quality and discoverability.
- Users: editors/SEO.
- Frame: searchable list + merge tools.
- Errors prevented: duplicate tags, alias confusion.

### 15) Reporters Management
- Purpose: staff output and assignment visibility.
- Users: Senior Editor, Admin.
- Frame: profile table/cards + detail panel.
- Errors prevented: hidden workload, weak accountability.

### 16) Editors / Roles / Permissions
- Purpose: access control.
- Users: Super Admin.
- Frame: role matrix + user list + permission pane.
- Errors prevented: role ambiguity, unsafe privilege escalation.

### 17) Media Library
- Purpose: image/document asset management.
- Users: editors/photo team.
- Frame: grid/list toggle + metadata drawer.
- Errors prevented: missing attribution, duplicate uploads.

### 18) Video Library
- Purpose: video lifecycle management.
- Users: video editor/live team.
- Frame: thumbnail grid + filters + details drawer.
- Errors prevented: live vs recorded confusion.

### 19) Notifications / Push Alerts
- Purpose: audience alert orchestration.
- Users: audience editor/SEO/editor-in-chief.
- Frame: compose + history/performance split.
- Errors prevented: wrong audience/send time mistakes.

### 20) Comments Moderation
- Purpose: trust & safety.
- Users: moderators/editors.
- Frame: queue + context + action dock.
- Errors prevented: accidental approvals, context-less moderation.

### 21) SEO Manager
- Purpose: metadata and index quality.
- Users: SEO editor.
- Frame: audits + issue table + fix panel.
- Errors prevented: missing meta, broken slugs.

### 22) Ads / Banner Manager
- Purpose: campaign and placement control.
- Users: ad manager/admin.
- Frame: campaign table + slot preview + performance cards.
- Errors prevented: wrong placement dates, sponsor labeling misses.

### 23) Analytics / Reports
- Purpose: editorial and business intelligence.
- Users: leadership/editors/ad manager.
- Frame: KPI + chart deck + ranking tables.
- Errors prevented: chart clutter, mixed time windows.

### 24) Settings
- Purpose: platform configuration.
- Users: super admin/admin.
- Frame: settings nav + grouped forms.
- Errors prevented: dangerous actions near common edits.

### 25) Audit Logs
- Purpose: accountability and traceability.
- Users: super admin/security.
- Frame: filterable table + event drawer.
- Errors prevented: unreadable dense logs.

### 26) System Health / Platform Status
- Purpose: infra and service diagnostics.
- Users: super admin/live operator.
- Frame: health cards + incident timeline + warning table.
- Errors prevented: hidden service degradation.

---

## E. Newsroom workflow UX plan

### Editorial flow
Draft → Submitted → Under Review → Fact Check → Approved → Scheduled → Published → Updated/Archived.
- Status chips fixed across all content views.
- SLA timers visible in review queues.

### Live coverage flow
- Stream health monitor + source failover controls.
- Live blog/updates synchronized to breaking module.

### Breaking news flow
- Create alert → validate copy → approve urgency → activate ticker → expiry handoff.
- Mandatory confirmation for replace/remove active alert.

### Homepage curation flow
- Drag section order → pin lead stories → preview impact → publish layout.

### Moderation flow
- Queue triage → bulk approve/reject/spam → escalation to editor.

### SEO flow
- Detect issues → prioritize by severity → quick-fix fields → revalidate.

### Ads flow
- Campaign setup → slot mapping → go-live checks → performance tracking.

---

## F. Admin component library

### Full reusable inventory
- Admin shell
- Sidebar nav
- Top action header
- KPI card
- Status badge
- Filter bar
- Search bar
- Primary button
- Secondary button
- Tertiary text action
- Chip/tag
- Table
- Table row actions
- Card/panel
- Widget header
- Modal
- Drawer
- Form field
- Select/dropdown
- Date picker
- Textarea/editor block
- Alert banner
- Empty state
- Loading skeleton
- Confirmation dialog
- Chart card
- Timeline row
- Activity log row

### Usage rules
- No page-specific button variants.
- One badge style map per status.
- All list pages use shared filter and table primitives.
- Every destructive action uses confirmation dialog.

---

## G. Final cleanup checklist
- [x] Every frame aligned to a unified shell and grid.
- [x] Spacing standardized to 8px system.
- [x] Duplicated admin shell patterns removed in updated pages.
- [x] Table patterns unified in dashboard baseline.
- [x] Form patterns unified in admin login baseline.
- [x] Dashboard balanced into KPI + operations + monitoring.
- [x] Live/breaking operational zones defined in system plan.
- [x] Responsive behavior rules defined for desktop/tablet/mobile.
- [x] Admin UI language unified for long-term rollout.
