---
name: Proctor Admin
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#40484b'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#71787c'
  outline-variant: '#c0c8cb'
  surface-tint: '#336576'
  primary: '#00303c'
  on-primary: '#ffffff'
  primary-container: '#0d4757'
  on-primary-container: '#83b5c7'
  inverse-primary: '#9ccee1'
  secondary: '#505f76'
  on-secondary: '#ffffff'
  secondary-container: '#d4e3ff'
  on-secondary-container: '#56657c'
  tertiary: '#232b3f'
  on-tertiary: '#ffffff'
  tertiary-container: '#394156'
  on-tertiary-container: '#a5adc6'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#b8eafe'
  primary-fixed-dim: '#9ccee1'
  on-primary-fixed: '#001f28'
  on-primary-fixed-variant: '#164d5d'
  secondary-fixed: '#d4e3ff'
  secondary-fixed-dim: '#b8c7e2'
  on-secondary-fixed: '#0c1c30'
  on-secondary-fixed-variant: '#39485e'
  tertiary-fixed: '#dae2fd'
  tertiary-fixed-dim: '#bec6e0'
  on-tertiary-fixed: '#131b2e'
  on-tertiary-fixed-variant: '#3e465c'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
  info-warning-bg: '#fefce8'
  info-warning-border: '#fef08a'
  info-warning-text: '#854d0e'
  success-icon: '#0d4757'
  border-subtle: '#e0e3e5'
typography:
  display-lg:
    fontFamily: Work Sans
    fontSize: 36px
    fontWeight: '600'
    lineHeight: 44px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Work Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  title-sm:
    fontFamily: Work Sans
    fontSize: 18px
    fontWeight: '600'
    lineHeight: 24px
  body-lg:
    fontFamily: Work Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-md:
    fontFamily: Work Sans
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-sm:
    fontFamily: Work Sans
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
    letterSpacing: 0.01em
  label-caps:
    fontFamily: Work Sans
    fontSize: 11px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
rounded:
  sm: 0.125rem
  DEFAULT: 0.25rem
  md: 0.375rem
  lg: 0.5rem
  xl: 0.75rem
  full: 9999px
spacing:
  sidebar-width: 280px
  header-height: 64px
  gutter: 24px
  stack-sm: 8px
  stack-md: 16px
  stack-lg: 32px
  container-max: 1440px
---

## Brand & Style
The brand personality is **Professional, Systematic, and Secure**. Designed specifically for enterprise HR and psychological assessment contexts, the UI evokes a sense of reliability and institutional trust. 

The aesthetic follows a **Corporate / Modern** style, utilizing a sophisticated palette of deep teals and slate grays. It prioritizes clarity and information density without feeling cluttered. Visual interest is maintained through subtle tonal shifts rather than loud decorative elements, ensuring the focus remains on high-stakes data management and administrative accuracy.

## Colors
The color system is built on a "Fidelity" variant, using a deep teal (`#0d4757`) as the primary anchor for actions and branding. 

- **Surface Strategy:** Uses a multi-tiered grayscale approach. Backgrounds utilize `#f7f9fb`, while containers and cards use white (`#ffffff`) to create contrast against the subtle off-white background.
- **Functional Colors:** A specialized "Warning" palette (yellow-800 scale) is reserved for critical system information and instructions.
- **State Management:** Active navigation states use a 10% opacity overlay of the primary color or a solid 4px left-border accent to denote selection without overwhelming the user.

## Typography
The system uses **Work Sans** exclusively to maintain a clean, humanist-industrial look that is highly legible in data-heavy tables.

- **Headlines:** Use semi-bold weights (`600`) to establish clear hierarchy for page titles and section headers.
- **Data Display:** Tables and lists utilize `body-md` (14px) for optimal information density.
- **Navigation Labels:** Sidebar categories use `label-caps` to distinguish grouping from interactive items.
- **Hierarchy:** Contrast is achieved primarily through weight shifts and color (Primary for titles, On-Surface-Variant for descriptions) rather than dramatic size changes.

## Layout & Spacing
The layout employs a **Fixed Sidebar + Fluid Content** model.

- **Structure:** A permanent 280px sidebar provides global navigation, while a 64px fixed header contains contextual tools. 
- **Grid:** Content is housed within a max-width container (1440px) and follows a 12-column logic. In the current view, a 4-column (left) and 8-column (right) split is used for master-detail interactions.
- **Rhythm:** An 8px base unit drives all spacing. Standard page margins and gutters are set to 24px (`stack-lg`) to provide significant breathing room between dense data blocks.

## Elevation & Depth
Depth is communicated through **Tonal Layers** and **Low-Contrast Outlines** rather than heavy shadows.

- **Flat Architecture:** Most components sit on the same elevation plane, distinguished by borders (`#c0c8cb`) or background shifts (`#f2f4f6`).
- **Surface Tiers:** 
  - Level 0: Background (`#f7f9fb`)
  - Level 1: Cards/Containers (White)
  - Level 2: Table Headers/Inactive Inputs (`#f2f4f6`)
- **Shadows:** A singular `shadow-sm` (subtle ambient drop shadow) is used only for primary action buttons to suggest clickability.

## Shapes
The system uses **Soft (Level 1)** roundedness to balance professional rigor with modern accessibility.

- **Standard Elements:** Buttons and Input fields use a 0.5rem (`rounded-lg`) corner radius.
- **Large Containers:** Cards and Matrix tables use a 0.75rem (`rounded-xl`) radius to soften the large surface areas.
- **Small Elements:** Checkboxes and small badges use a 0.25rem (`DEFAULT`) radius.
- **Avatars:** Strictly circular (`rounded-full`) to differentiate human entities from system components.

## Components
- **Buttons:**
  - *Primary:* Solid `#0d4757` with white text. High-contrast, 0.5rem radius.
  - *Outline:* Single pixel border (`#71787c`) with transparent background for secondary actions like "Cancel".
- **Checkboxes:** Custom-styled to match the primary brand color. When checked, they use `#0d4757` with a white checkmark; when unchecked, they feature a subtle `#c0c8cb` border.
- **Data Tables:**
  - Headers are styled with a light gray background (`#f2f4f6`) and uppercase labels.
  - Row hover states use a subtle color shift (`#eceef0`) and a momentary primary-tinted flash on interaction.
- **Navigation Items:**
  - Inactive: Transparent background, Slate-gray icons/text.
  - Active: 10% primary tint background and a 4px solid primary-colored left border.
- **Cards:** White background, 1px border (`#c0c8cb`), and 0.75rem corner radius. Section headers within cards are separated by a subtle horizontal rule.
- **Input Fields:** 0.5rem radius, `#f7f9fb` background, and a 2px primary ring focus state.