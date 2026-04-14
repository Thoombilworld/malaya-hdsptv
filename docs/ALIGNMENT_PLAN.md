# HDSPTV UX Alignment & Layout System Plan

## A. Global design system

### Frame sizes
- Desktop frame: 1440px artboard, max content 1280px.
- Tablet frame: 1024px.
- Mobile frame: 390px.
- Outer margins: 80px desktop, 32px tablet, 16px mobile.

### Grid rules
- 12-column grid for desktop/tablet page scaffolds.
- 24px column gutter desktop/tablet.
- Public pages: default `8/4` split for main + sidebar.
- Admin pages: fixed sidebar + 12-col content canvas.

### Spacing rules
- 8px base scale only (8,16,24,32,40,48).
- Section spacing: 32–48.
- Card spacing: 20/24.
- Row spacing in tables: 12–16.

### Card rules
- Radius 16px standard, 20px for hero/auth cards.
- Uniform card anatomy: header / content / footer.
- Use equal-height cards in shared rows.

### Color system
- Primary Red `#D60000`
- Dark `#111111`
- Deep Navy `#0B1220`
- Background `#F6F7FB`
- Card White `#FFFFFF`
- Border `#E5E7EB`
- Text Gray `#6B7280`
- Success `#16A34A`, Warning `#F59E0B`, Info `#2563EB`

### Type scale
- H1: 36–40
- H2: 28–32
- H3: 20–24
- Body: 14–16
- Meta: 12–13
- Tight headline line-height (1.1–1.25), body line-height 1.5.

### Button/input/badge patterns
- Primary button: red fill, white text, 48px height.
- Secondary button: white fill + border.
- Inputs: 48–52px height, 12px radius.
- Badges: pill style, semantic colors (breaking/live/status).

## B. Public website page-by-page alignment plan

For each page, purpose + structure + responsive behavior + fixes:

1. **Homepage**
   - Purpose: front-door editorial discovery.
   - Frame: header + breaking strip + 8/4 content split.
   - Sections: hero, top stories, latest grid, video, editors’ picks, trending, most viewed, gallery, footer.
   - Fixes: remove duplicate nav, normalize card heights, align sidebar baseline.

2. **Breaking News**
   - Frame: 9/3 split.
   - Sections: active alert, timeline, latest updates, related stories.
   - Fixes: unify live/breaking badges, reduce top whitespace.

3. **Live TV**
   - Frame: 8/4 split.
   - Sections: player, live status, current/upcoming programs, ticker, related live stories.
   - Fixes: align player and metadata cards; stabilize sidebar widths.

4. **Category Listing**
   - Frame: full-width header + 3-column card grid.
   - Sections: lead story, filters/sort, story cards, pagination.
   - Fixes: consistent card rhythm and meta baseline.

5. **Subcategory**
   - Same as category page with breadcrumb context and tighter filter set.

6. **Article Detail**
   - Frame: reading column + optional sidebar.
   - Sections: headline block, media, body, pull quotes, tags, author, related.
   - Fixes: readable width, vertical rhythm, elegant share tools.

7. **Search Results**
   - Frame: 8/4.
   - Sections: query summary, filters, results list, pagination.
   - Fixes: remove floating controls and align empty states.

8. **Trending**
   - Frame: 3-column cards + ranking numbers.
   - Fixes: equal card heights, metadata consistency.

9. **Video News**
   - Frame: featured player + video grid + shorts rail.
   - Fixes: player/card alignment and caption spacing.

10. **Photo Gallery**
   - Masonry/grid hybrid with fixed aspect previews.
   - Fixes: spacing and hover action consistency.

11. **Special Reports**
   - Long-form package cards with section labels.
   - Fixes: hierarchy between flagship package and secondary items.

12. **Reporter Profile**
   - Bio header + latest stories + beats/tags.
   - Fixes: normalize avatar, bio, and card gutters.

13. **About**
   - Editorial mission blocks + leadership cards.
   - Fixes: reduce dead space and standardize card widths.

14. **Contact**
   - Two-column layout (info + form).
   - Fixes: form labels, validation spacing, CTA hierarchy.

15. **Login**
16. **Register**
17. **Forgot Password**
   - Two-column premium auth pattern.
   - Fixes: 20px card radius, consistent input heights, clear error box.

18. **User Profile**
19. **Saved Articles**
20. **Notifications**
   - Account shell with left nav and right content pane.
   - Fixes: unified list rows, status chips, empty states.

## C. Admin / newsroom page-by-page alignment plan

1. **Admin Login**: split-brand/auth layout, trust-focused.
2. **Dashboard**: KPI row + operational widgets + recent activity table.
3. **All News**: filter/search bar, bulk actions, table/card toggle.
4. **Create News**: 8/4 editor + settings rail.
5. **Edit Article**: same scaffold with revision/status indicators.
6. **Drafts**: filtered list on common table pattern.
7. **Pending Review**: review queue with SLA/priority chips.
8. **Scheduled Posts**: calendar + list hybrid.
9. **Published Posts**: performance and moderation actions.
10. **Breaking Manager**: active/scheduled/expired columns + ticker preview.
11. **Live TV Control**: stream preview, source controls, health cards.
12. **Homepage Manager**: visual section ordering + pin controls.
13. **Categories Manager**: hierarchy table + quick actions.
14. **Tags Manager**: searchable chips/table hybrid.
15. **Reporters Management**: profile cards + assignment load.
16. **Editors/Roles**: role matrix with scoped permissions.
17. **Media Library**: grid/list switch + metadata drawer.
18. **Video Library**: video-focused metadata and duration/status badges.
19. **Notifications/Push**: campaign composer + performance cards.
20. **Comments Moderation**: queue with approve/reject batch controls.
21. **SEO Manager**: global defaults + per-page preview modules.
22. **Ads/Banners**: slot inventory + performance summary.
23. **Analytics/Reports**: card + chart stack with readable axes.
24. **Settings**: grouped tabs (brand, language, integrations, security).
25. **Audit Logs**: dense table with filters + event detail drawer.

Admin-wide rules:
- Fixed left sidebar.
- Sticky top action bar.
- Shared table paddings and row heights.
- One status badge language across all modules.

## D. Reusable component inventory
- Header (public + admin variants)
- Footer
- Hero cards
- Article cards (feature, standard, compact)
- Widgets (trending, most read, live status, KPI)
- Sidebars (public context + admin navigation)
- Filters (inline, drawer, chips)
- Forms (single, multi-step, validation states)
- Tables (dense + expanded modes)
- Modals / side drawers
- Alerts (breaking/live/system)
- Status badges (draft/pending/published/live)
- Analytics cards/charts

## E. Final cleanup checklist
- [x] Duplicated sections removed from homepage implementation.
- [x] Core frame constraints centralized in shared CSS tokens.
- [x] Global spacing scale standardized to 8px multiples.
- [x] Card radii/padding normalized across new homepage components.
- [x] Form and button primitives standardized in shared stylesheet.
- [x] Responsive behavior defined for desktop/tablet/mobile breakpoints.
- [x] Public UI direction unified by shared design system variables.
- [x] Admin/page-by-page blueprint defined for full rollout.
