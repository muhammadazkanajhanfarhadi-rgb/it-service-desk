---
name: Apex Service Desk
colors:
  surface: '#0c1321'
  surface-dim: '#0c1321'
  surface-bright: '#323949'
  surface-container-lowest: '#070e1c'
  surface-container-low: '#151b2a'
  surface-container: '#19202e'
  surface-container-high: '#232a39'
  surface-container-highest: '#2e3544'
  on-surface: '#dce2f6'
  on-surface-variant: '#c3c6d7'
  inverse-surface: '#dce2f6'
  inverse-on-surface: '#2a3040'
  outline: '#8d90a0'
  outline-variant: '#434655'
  surface-tint: '#b4c5ff'
  primary: '#b4c5ff'
  on-primary: '#002a78'
  primary-container: '#2563eb'
  on-primary-container: '#eeefff'
  inverse-primary: '#0053db'
  secondary: '#afc6ff'
  on-secondary: '#002d6c'
  secondary-container: '#045ecf'
  on-secondary-container: '#d5e0ff'
  tertiary: '#adc6ff'
  on-tertiary: '#002e6a'
  tertiary-container: '#0f69dc'
  on-tertiary-container: '#edf0ff'
  error: '#ffb4ab'
  on-error: '#690005'
  error-container: '#93000a'
  on-error-container: '#ffdad6'
  primary-fixed: '#dbe1ff'
  primary-fixed-dim: '#b4c5ff'
  on-primary-fixed: '#00174b'
  on-primary-fixed-variant: '#003ea8'
  secondary-fixed: '#d9e2ff'
  secondary-fixed-dim: '#afc6ff'
  on-secondary-fixed: '#001a43'
  on-secondary-fixed-variant: '#004398'
  tertiary-fixed: '#d8e2ff'
  tertiary-fixed-dim: '#adc6ff'
  on-tertiary-fixed: '#001a42'
  on-tertiary-fixed-variant: '#004395'
  background: '#0c1321'
  on-background: '#dce2f6'
  surface-variant: '#2e3544'
  app-bg: '#0B1220'
  sidebar-surface: '#10182A'
  card-surface: '#121C31'
  card-elevated: '#16233F'
  input-surface: '#0F1930'
  primary-dark: '#1D4ED8'
  accent-electric: '#4F8CFF'
  accent-light: '#60A5FA'
  text-primary: '#F8FAFC'
  text-secondary: '#94A3B8'
  text-tertiary: '#64748B'
  text-disabled: '#475569'
  border-subtle: rgba(148, 163, 184, 0.14)
  border-input: rgba(148, 163, 184, 0.16)
  border-hover: rgba(59, 130, 246, 0.40)
  border-glass: rgba(255, 255, 255, 0.08)
  status-success: '#22C55E'
  status-warning: '#F59E0B'
  status-danger: '#EF4444'
  status-info: '#38BDF8'
  status-pending: '#A78BFA'
  overlay-backdrop: rgba(0, 0, 0, 0.60)
  interactive-active-bg: rgba(37, 99, 235, 0.18)
typography:
  headline-xl:
    fontFamily: Inter
    fontSize: 36px
    fontWeight: '700'
    lineHeight: 44px
  headline-xl-mobile:
    fontFamily: Inter
    fontSize: 28px
    fontWeight: '700'
    lineHeight: 36px
  headline-lg:
    fontFamily: Inter
    fontSize: 22px
    fontWeight: '600'
    lineHeight: 28px
  headline-md:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  headline-sm:
    fontFamily: Inter
    fontSize: 15px
    fontWeight: '600'
    lineHeight: 20px
  body-lg:
    fontFamily: Inter
    fontSize: 15px
    fontWeight: '400'
    lineHeight: 22px
  body-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  body-sm:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '400'
    lineHeight: 18px
  label-md:
    fontFamily: Inter
    fontSize: 13px
    fontWeight: '600'
    lineHeight: 16px
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
  caption:
    fontFamily: Inter
    fontSize: 11px
    fontWeight: '400'
    lineHeight: 14px
  code-meta:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gutter: 1rem
  gutter-desktop: 1.5rem
  margin: 1rem
  margin-desktop: 2rem
  space-xs: 0.25rem
  space-sm: 0.5rem
  space-md: 0.75rem
  space-lg: 1rem
  space-xl: 1.5rem
  space-2xl: 2rem
---

## Brand & Style

The design system establishes a high-performance, command-center aesthetic tailored for Tier 1–3 IT Operations, SecOps engineers, and enterprise incident commanders. It shifts away from generic corporate ticketing software toward an ergonomic, cockpit-inspired experience designed for sustained focus during high-stress operational windows.

### Visual Aesthetic & Movement
The design language combines **Modern Corporate Precision** with **Atmospheric Layering**:
- **Dark Navy Canvas Foundation**: Recessed surfaces minimize photopic eye strain during multi-monitor night shifts and extended triaging.
- **Electric Blue Radiance**: High-energy chromatic blues indicate focal points, active states, and interactive thresholds without visual clutter.
- **Micro-Glass Translucency**: Subtle background blurs (`12px`–`20px`) provide contextual layering for modals, notifications, and telemetry surfaces without sacrificing performance or legibility.
- **Scannable Information Hierarchy**: Form follows function; ticket identifiers, incident severity levels, and SLA countdowns take precedence over decorative UI elements.

## Colors

The color architecture is built around layered dark-navy luminosity steps and vivid telemetry accents. 

### Surface Roles
- `app-bg` (`#0B1220`): Foundation layer for viewport canvases and app framing.
- `sidebar-surface` (`#10182A`): Structural column for global navigational hierarchy.
- `card-surface` (`#121C31`): Resting background for operational widgets, feeds, and ticket grids.
- `card-elevated` (`#16233F`): Floating state for dragged cards, context menus, and elevated metric panels.
- `input-surface` (`#0F1930`): Recessed visual well ensuring clear contrast for form controls.

### Semantic Status Palette
Operational status colors are calibrated against navy surfaces to guarantee WCAG AA text and icon readability:
- **Critical / Danger (`#EF4444`)**: Breached SLAs, P1 incident tags, and destructive actions.
- **Warning (`#F59E0B`)**: Impending SLA expirations, unassigned alerts, and degraded infrastructure.
- **Success (`#22C55E`)**: Resolved tickets, operational systems, and compliant runbooks.
- **Info (`#38BDF8`)**: In-progress triage, dispatched agents, and informational logs.
- **Pending (`#A78BFA`)**: Customer hold states, vendor feedback loops, and deferred items.

## Typography

The design system implements a single, high-clarity sans-serif typeface (`Inter`) to eliminate stylistic friction and prioritize immediate data comprehension. 

### Operational Typography Guidelines
- **Density Over Display**: Restrain display scale sizes on active operational dashboards. Page headers rarely exceed `22px` within dense service queues to preserve vertical screen real estate for incident tables.
- **Monospace-Grade Alignment**: Ticket identifiers (`INC-9402`), IP addresses, and timestamps use tabular numeric alignment (`font-feature-settings: "tnum"`).
- **Foreground Hierarchy**:
  - Primary text (`#F8FAFC`): Action titles, modal headers, entered input text.
  - Secondary text (`#94A3B8`): Column titles, descriptions, status metadata.
  - Tertiary text (`#64748B`): Helper hints, input placeholders, timestamp footers.
  - Disabled text (`#475569`): Unselected or inactive controls.

## Layout & Spacing

The layout is anchored to a strict **4px spatial rhythm** (`4px`, `8px`, `12px`, `16px`, `20px`, `24px`, `32px`, `40px`, `48px`, `64px`). All margins, gutters, component paddings, and control bounding boxes must resolve to this baseline.

### Shell Architecture
- **Sidebar**: Fixed width between `240px` and `260px`, docked to the left viewport edge with full-height vertical separation.
- **Utility / Context Rail**: Optional dockable right pane (`280px`–`320px`) for SLA timers, customer telemetry, and contextual quick actions.
- **Main Viewport Canvas**: Responsive fluid container stretching across the center stage.

### Responsive Breakpoints
- **Mobile (`< 768px`)**: Single-column layout. Sidebar collapses into a slide-over drawer; page margins drop to `16px` (`margin`); cards render with `16px` internal padding.
- **Tablet (`768px` – `1023px`)**: 8-column layout. Sidebar switches to icon-collapsed mode (`64px`); utility rail tucks into a tabbed bottom sheet.
- **Desktop (`1024px+`)**: 12-column layout. `24px` gutter gaps (`gutter-desktop`); full sidebar persistent; `32px` page canvas margins (`margin-desktop`).

## Elevation & Depth

Depth is established through stacked navy surface luminosity, hairline boundary rings, and targeted photon glows rather than muddy, dark dropshadows.

### Surface Tiers
1. **Level 0 (Canvas Base)**: `#0B1220` with no elevation.
2. **Level 1 (Card Resting)**: Surface `#121C31` bounded by `1px solid rgba(148, 163, 184, 0.14)` and shadow `0 8px 30px rgba(0, 0, 0, 0.18)`.
3. **Level 2 (Elevated & Hover)**: Surface `#16233F` with shadow `0 16px 50px rgba(0, 0, 0, 0.28)` and border transition to `rgba(59, 130, 246, 0.40)`.
4. **Level 3 (Modals & Overlays)**: Surface `#16233F` or glassmorphic `rgba(18, 28, 49, 0.75)` over a `rgba(0, 0, 0, 0.60)` backdrop blur layer.

### Glassmorphism & Accent Glows
- **Ambient Electric Blue Glow**: Selected items, primary buttons on hover, and active navigation indicators emit an ethereal glow: `box-shadow: 0 0 30px rgba(37, 99, 235, 0.25)`.
- **Soft Frosted Glass**: Used for floating notification panels, quick filters, and toast notifications: `backdrop-filter: blur(16px)` paired with `background: rgba(18, 28, 49, 0.75)` and `border: 1px solid rgba(255, 255, 255, 0.08)`.

## Shapes

The design system employs a refined geometric curve language that avoids boxy brutalism while remaining compact enough for high-density enterprise layouts.

### Geometry Hierarchy
- **Pill (`9999px`)**: Reserved exclusively for status indicators, SLA micro-badges, and filter count bubbles.
- **Large Container Corners (`20px`–`24px`)**: Applied to application modals, slide-out drawer sheets, and major dashboard panels.
- **Card Surfaces (`16px`)**: Standard container radius for all modular widgets, metric cards, and table wrappers.
- **Interactive Controls (`10px`–`12px`)**: Input text fields, select menus, standard buttons, and search bars.
- **Micro Elements (`6px`–`8px`)**: Checkbox toggles, nested tooltips, and table row indicator badges.

## Components

### Buttons
- **Primary**: Background `#2563EB`, text `#FFFFFF`, radius `10px` to `12px`, height `36px`–`40px`. Hover initiates `#3B82F6` with `box-shadow: 0 0 30px rgba(37, 99, 235, 0.25)`. Active uses `#1D4ED8`.
- **Secondary / Ghost**: Background `rgba(18, 28, 49, 0.75)`, border `1px solid rgba(148, 163, 184, 0.14)`, text `#F8FAFC`. Hover switches border to `rgba(59, 130, 246, 0.40)` and text to `#38BDF8`.
- **Danger Action**: Background `rgba(239, 68, 68, 0.12)`, border `1px solid rgba(239, 68, 68, 0.32)`, text `#EF4444`. Hover switches to solid `#EF4444` with white text.

### Form Inputs & Select Controls
- **Resting**: Background `#0F1930`, border `1px solid rgba(148, 163, 184, 0.16)`, height `40px`, corner radius `10px`, typography `14px` (`#F8FAFC`). Placeholder uses `#64748B`.
- **Focus**: Border highlights to `#3B82F6`, accompanying an electric outer ring: `box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25)`. Outline is none.

### Status Chips & Badges
- **Structure**: Height `22px`–`24px`, padding `0 10px`, border radius `9999px`, typography `11px` weight `600`.
- **Variants**:
  - *Resolved*: Background `rgba(34, 197, 94, 0.15)`, text `#22C55E`, border `1px solid rgba(34, 197, 94, 0.3)`.
  - *SLA Breach / Critical*: Background `rgba(239, 68, 68, 0.15)`, text `#EF4444`, border `1px solid rgba(239, 68, 68, 0.3)`.
  - *Warning*: Background `rgba(245, 158, 11, 0.15)`, text `#F59E0B`, border `1px solid rgba(245, 158, 11, 0.3)`.
  - *Assigned*: Background `rgba(56, 189, 248, 0.15)`, text `#38BDF8`, border `1px solid rgba(56, 189, 248, 0.3)`.
  - *Pending*: Background `rgba(167, 139, 250, 0.15)`, text `#A78BFA`, border `1px solid rgba(167, 139, 250, 0.3)`.

### Cards & Tables
- **Container Structure**: Surface `#121C31`, border `1px solid rgba(148, 163, 184, 0.14)`, radius `16px`, padding `20px` to `24px`.
- **Hover Micro-interaction**: `transform: translateY(-1px)`, border color `rgba(59, 130, 246, 0.40)`, shadow `0 16px 50px rgba(0, 0, 0, 0.28)`.
- **Data Tables**: Transparent row backgrounds with hairline cell divider borders (`1px solid rgba(148, 163, 184, 0.08)`). Header row text uses `#94A3B8` at `12px` uppercase. Row hover transitions surface to `rgba(22, 35, 63, 0.60)`.

### Checkboxes & Radios
- **Control**: Size `18px x 18px`, radius `6px` (checkbox) or `9999px` (radio). Surface `#0F1930`, border `1px solid rgba(148, 163, 184, 0.24)`.
- **Checked**: Surface `#2563EB`, border `#2563EB`, icon white checkmark, glow `0 0 12px rgba(37, 99, 235, 0.35)`.

### SLA Progress Indicators
- **Track**: Height `6px`, background `rgba(255, 255, 255, 0.06)`, border radius `9999px`.
- **Fill**: Solid semantic status color with an animated glowing pulse tip for breached or warning thresholds.