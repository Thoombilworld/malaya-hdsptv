## A. Public website design principles

### Brand tone
- International, trustworthy, calm-under-pressure editorial voice.
- Premium but practical: clarity first, visual polish second.
- Live/breaking urgency is focused and never chaotic.

### Usability goals
- Fast scanning on homepage and category surfaces.
- Frictionless reading on article pages.
- One predictable navigation/search/auth pattern across all pages.

### Reader experience principles
- Clear hierarchy: lead story, supporting stories, context widgets.
- Mobile-first readability with desktop depth.
- Minimal UI noise around long-form content.

### Trust and editorial clarity principles
- Persistent metadata (author/date/category/update state).
- Consistent live/breaking badges and timestamp formatting.
- Clean spacing and alignment to avoid “template fatigue.”

---

## B. Global frontend frame system

### Canvas sizes
- Desktop 1440px canvas, max content width 1280px.
- Tablet 1024px.
- Mobile 390px.

### Grid
- 12-column grid, 24px gutters.
- Primary page split: 8 columns main + 4 columns support rail.

### Spacing
- 8px scale (8/16/24/32/40/48).
- Section spacing: 32–48.
- Card padding: 20/24.

### Header shell
- Single sticky header with logo, nav, search, language, account, live CTA.
- Optional top strip for date/weather/global edition.

### Page body rules
- All section titles align to same start edge.
- Reusable section blocks with title row + content + optional view-all.
- No duplicate header/nav rows.

### Sidebar rules
- Uniform widget width and card anatomy.
- Sidebar complements main story rail; never dominates.
- On tablet/mobile sidebar stacks below main content.

### Footer rules
- One consistent footer structure and spacing.
- Clear endpoint with no floating whitespace.

---

## C. Frontend design system

### Colors
- Primary `#D60000`, Dark `#111111`, Deep Navy `#0B1220`, BG `#F6F7FB`, Card `#FFFFFF`, Border `#E5E7EB`, Text Gray `#6B7280`, Success `#16A34A`, Warning `#F59E0B`, Info `#2563EB`.

### Typography
- H1 36–40, H2 28–32, H3 20–24, Body 14–16, Meta 12–13.
- Compact headline rhythm + readable body measure.

### Buttons
- Primary red filled, secondary outlined, tertiary text action.

### Badges
- Unified badge styles for LIVE/BREAKING/VIDEO/status indicators.

### Cards
- Standard radius 16, hero radius 20.
- Consistent header/content/meta spacing.

### Metadata
- Single row pattern: category · timestamp · author/context.

### Forms
- 48–52px input height, top labels, inline validation + summary messages.

### Search
- One global search bar pattern in header + reusable results filter bar.

### Alerts
- Top-level alert/ticker pattern for breaking/live updates.

### Section patterns
- Hero, section grid, sidebar widget, newsletter, gallery, and list-table hybrids.

---

## D. Homepage UI/UX alignment plan

### Homepage purpose
- Editorial command front page for rapid discovery and trust.

### Section order
1) top strip, 2) sticky header, 3) breaking strip, 4) hero lead, 5) top stories, 6) latest grid, 7) video module, 8) live promo, 9) sidebar widgets (trending/most-viewed/editors/gallery/newsletter), 10) footer.

### Frame structure
- 12-col layout with 8/4 split.
- Equalized row rhythm between main rail and sidebar widgets.

### Hero alignment
- Lead image + headline + meta + CTA within 20px hero container.
- Secondary cards lock to shared heights where grouped.

### Sidebar alignment
- One widget card style with consistent padding and heading treatment.
- Widget stack spacing locked to 24px.

### Category block system
- Repeatable section template with title row and card/list variant.

### Module spacing
- 32px between major modules, 24px within module internals.

### Responsive notes
- Tablet keeps hierarchy with reduced columns.
- Mobile stacks modules cleanly; header utilities collapse in order.

### Common errors prevented
- Duplicate nav rows, blank hero gaps, unbalanced sidebars, inconsistent card heights, floating utility buttons.

---

## E. Page-by-page public website UI alignment plan

### 1) Homepage
- Purpose: editorial discovery.
- Key users: all readers.
- Frame: 8/4 split with hero-first hierarchy.
- Section order: as defined in section D.
- Alignment: grid-locked modules and shared card anatomy.
- Components: header, ticker, hero, article cards, widgets, footer.
- Responsive: sidebar below main on small viewports.
- Errors prevented: duplicated nav, broken rhythm.

### 2) Breaking News page
- Purpose: urgent updates.
- Key users: real-time followers.
- Frame: lead alert + timeline + related stories.
- Alignment: urgency color constrained to key surfaces.
- Components: breaking badge, update stream, article cards.
- Responsive: timeline compresses into stacked cards.
- Errors prevented: panic-heavy visual clutter.

### 3) Live TV page
- Purpose: live broadcast consumption.
- Key users: live audience.
- Frame: main player + schedule + updates + related coverage.
- Alignment: player and metadata card baselines match.
- Components: live badge, player card, timeline rows.
- Responsive: player stays first; controls stack below.
- Errors prevented: disjoint player/control layout.

### 4) Category listing page
- Purpose: topic browsing.
- Key users: readers by interest.
- Frame: category hero + feature + story grid + pagination.
- Alignment: card gutters and metadata baselines standardized.
- Components: section header, article cards, pagination.
- Responsive: 3→2→1 card columns.
- Errors prevented: repetitive but misaligned card rhythms.

### 5) Subcategory page
- Purpose: narrower taxonomy exploration.
- Key users: focused readers.
- Frame: breadcrumb + lead + filtered list.
- Alignment: same template as category, scoped filters.
- Components: chips/filters, cards, pagination.
- Responsive: filter chips become horizontal scroll.
- Errors prevented: duplicate layouts diverging from category page.

### 6) Article detail page
- Purpose: deep reading.
- Key users: engaged readers.
- Frame: centered reading column + optional support rail.
- Alignment: readable text measure, strong paragraph spacing.
- Components: metadata row, pull quote, related cards, tags, share tools.
- Responsive: support rail moves below body.
- Errors prevented: cramped text and over-distraction.

### 7) Search results page
- Purpose: content retrieval.
- Key users: intent-based readers.
- Frame: query summary + filters + result list.
- Alignment: search and filters share baseline.
- Components: search bar, chips, result cards.
- Responsive: filters collapse to drawer/stack.
- Errors prevented: floating filters and weak empty states.

### 8) Trending page
- Purpose: popularity discovery.
- Key users: quick scanners.
- Frame: ranked list + supporting cards.
- Alignment: rank markers and headlines align consistently.
- Components: rank badge, article cards.
- Responsive: ranked cards stack cleanly.
- Errors prevented: noisy visual competition.

### 9) Video news page
- Purpose: video-first discovery.
- Key users: video audience.
- Frame: featured player + video grid + shorts rail.
- Alignment: thumbnail ratios standardized.
- Components: video card, tabs, metadata row.
- Responsive: featured player remains top priority.
- Errors prevented: inconsistent thumbnail and meta spacing.

### 10) Photo gallery page
- Purpose: visual storytelling.
- Key users: image-led readers.
- Frame: featured gallery + gallery grid.
- Alignment: fixed aspect tile strategy.
- Components: gallery card, metadata overlay.
- Responsive: 3→2→1 image columns.
- Errors prevented: uneven masonry gaps.

### 11) Special reports page
- Purpose: long-form editorial packages.
- Key users: depth readers.
- Frame: premium cover module + chapters/related packages.
- Alignment: strong vertical rhythm and chapter spacing.
- Components: cover cards, long-form modules.
- Responsive: preserve hierarchy while stacking.
- Errors prevented: generic category styling on premium content.

### 12) Reporter profile page
- Purpose: author trust and expertise.
- Key users: credibility-seeking readers.
- Frame: profile header + bio + recent stories.
- Alignment: avatar, bio, and story cards on common grid.
- Components: reporter card, article list, topic chips.
- Responsive: profile summary stacks above stories.
- Errors prevented: low-authority author surfaces.

### 13) About page
- Purpose: mission and editorial identity.
- Key users: trust evaluators.
- Frame: mission blocks + leadership + standards.
- Alignment: modular sections with consistent max width.
- Components: text blocks, profile cards.
- Responsive: reduce columns while preserving sequence.
- Errors prevented: corporate clutter and empty zones.

### 14) Contact page
- Purpose: reader and partner contact.
- Key users: audience/stakeholders.
- Frame: contact details + form split.
- Alignment: form labels/inputs follow global form rules.
- Components: form fields, alerts, info panel.
- Responsive: two-column to stacked form layout.
- Errors prevented: inconsistent validation spacing.

### 15) Login page
### 16) Register page
### 17) Forgot password page
- Purpose: account access and recovery.
- Key users: returning/new readers.
- Frame: two-column trust/auth layout.
- Alignment: same auth shell and field sizing.
- Components: auth card, validation, support links.
- Responsive: single-column mobile collapse.
- Errors prevented: mixed auth patterns and weak error clarity.

### 18) User profile page
### 19) Saved articles page
### 20) Notifications page
- Purpose: personal account management.
- Key users: logged-in readers.
- Frame: account nav + content pane.
- Alignment: shared card/list patterns for saved and alerts.
- Components: account shell, cards, toggles/chips.
- Responsive: nav becomes top tabs/accordion.
- Errors prevented: fragmented account UX.

---

## F. Reader journey UX plan

### Homepage discovery flow
- Scan lead → choose topic path (nav/category/trending) → enter article/video.

### Article reading flow
- Read headline/meta/context → consume body → move to related/tagged stories.

### Live coverage flow
- Enter live module → monitor player + updates timeline → jump to related breaking articles.

### Breaking news flow
- Alert strip → breaking page → update timeline → contextual coverage.

### Search/discovery flow
- Query from header → refine with filters → open result → continue via related links.

### Account/saved content flow
- Sign in/register → save stories → manage preferences/notifications → revisit saved feed.

---

## G. Public component library

### Full reusable component inventory
- Public shell
- Header
- Top strip / ticker
- Live badge
- Breaking badge
- Hero card
- Article card
- Video card
- Category section block
- Sidebar widget
- Search bar
- Language selector
- Auth card
- Footer
- Metadata row
- Share tools
- Tags/chips
- Newsletter block
- Gallery card
- Pagination
- Empty state
- Loading skeleton
- Alert banner

### Usage rules
- One card anatomy for all editorial cards.
- One metadata row order for consistency.
- One badge semantics map for live/breaking/video.
- One responsive collapse sequence for header/sidebar modules.

---

## H. Final cleanup checklist
- [x] Every frame aligned to a master frontend grid.
- [x] Duplicated header/nav patterns removed from updated homepage.
- [x] Oversized visual gaps removed.
- [x] Spacing standardized to 8px rhythm.
- [x] Card rhythm unified for homepage modules.
- [x] Sidebar behavior normalized and balanced.
- [x] Hero-to-sidebar top alignment corrected.
- [x] Footer anchor and terminal spacing stabilized.
- [x] Auth layout strategy defined and standardized.
- [x] Responsive stacking and tap-target behavior defined.
- [x] Full public page family aligned under one design system plan.
