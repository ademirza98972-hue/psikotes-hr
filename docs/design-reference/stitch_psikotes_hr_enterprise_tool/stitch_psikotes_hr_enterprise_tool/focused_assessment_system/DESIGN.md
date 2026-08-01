---
name: Focused Assessment System
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
  surface-tint: '#326575'
  primary: '#0d4757'
  on-primary: '#ffffff'
  primary-container: '#2c5f6f'
  on-primary-container: '#a5d7ea'
  inverse-primary: '#9ccee1'
  secondary: '#586061'
  on-secondary: '#ffffff'
  secondary-container: '#dae1e2'
  on-secondary-container: '#5d6465'
  tertiary: '#603716'
  on-tertiary: '#ffffff'
  tertiary-container: '#7b4e2b'
  on-tertiary-container: '#ffc398'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#b8eafd'
  primary-fixed-dim: '#9ccee1'
  on-primary-fixed: '#001f28'
  on-primary-fixed-variant: '#164d5c'
  secondary-fixed: '#dde4e5'
  secondary-fixed-dim: '#c1c8c9'
  on-secondary-fixed: '#161d1e'
  on-secondary-fixed-variant: '#414849'
  tertiary-fixed: '#ffdcc5'
  tertiary-fixed-dim: '#f7ba8e'
  on-tertiary-fixed: '#301400'
  on-tertiary-fixed-variant: '#663d1b'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  display-lg:
    fontFamily: Work Sans
    fontSize: 40px
    fontWeight: '600'
    lineHeight: 48px
    letterSpacing: -0.02em
  headline-md:
    fontFamily: Work Sans
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
  body-lg:
    fontFamily: Work Sans
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Work Sans
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  label-caps:
    fontFamily: Work Sans
    fontSize: 12px
    fontWeight: '600'
    lineHeight: 16px
    letterSpacing: 0.05em
  timer-display:
    fontFamily: Work Sans
    fontSize: 20px
    fontWeight: '700'
    lineHeight: 24px
    letterSpacing: 0.01em
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  gap-stack-xl: 3rem
  container-max-width: 720px
  gutter-md: 1.5rem
  margin-page: 2rem
---

## Brand & Style

The design system is centered on a "calm and focused" philosophy, specifically tailored for candidates and employees undergoing psychometric evaluation. The goal is to reduce test-taking anxiety by minimizing visual noise and prioritizing cognitive clarity.

The aesthetic follows a **Modern Corporate** direction with a tilt toward **Minimalism**. It utilizes a light, airy background with a singular, authoritative primary color to guide the user's attention. Transitions are subtle, and the interface remains predictable to ensure the user's focus remains entirely on the assessment content rather than the UI mechanics.

## Colors

The palette is restricted to promote a low-stress environment.
- **Primary (#2C5F6F):** A deep teal used for primary actions, selected states, and progress indicators. It conveys stability and professionalism.
- **Secondary (#E9F0F1):** A very light teal tint used for "Selected" backgrounds and soft highlights.
- **Neutral/Surface (#F8FAFC):** The Slate-50 background provides a clean, non-glare canvas for long-form reading.
- **Text:** High-contrast slate-900 for readability, with slate-500 for secondary instructional metadata.

## Typography

This design system prioritizes readability over density. **Work Sans** is used throughout for its geometric clarity and friendly but professional demeanor. 

- **Body Text:** Increased to `18px` for assessment questions to ensure candidates can read long passages without eye strain.
- **Instructional Text:** Uses a generous `1.6` line-height ratio to prevent text crowding.
- **Numbers:** Tabular lining should be enabled for timers and progress percentages to prevent visual jitter.

## Layout & Spacing

The layout shifts from a management-style sidebar to a **Centered Focus** model. 
- **The Core Container:** Content is restricted to a maximum width of `720px` to maintain optimal line lengths for reading and to keep interaction points within a narrow visual field.
- **Vertical Rhythm:** The `gap-stack-xl` (3rem) is the standard spacing between distinct sections (e.g., the question text and the choice cards) to reduce cognitive load.
- **Mobile Adaptivity:** On smaller screens, horizontal margins reduce to `1rem`, and the container becomes fluid.

## Elevation & Depth

To maintain a "flat and calm" feel, the system avoids heavy shadows. 
- **Surface Layering:** Depth is conveyed through subtle tonal changes rather than elevation.
- **Choice Cards:** Use a single-pixel stroke (`Slate-200`) in their default state.
- **Focus States:** When an item is selected or active, it uses a `2px` stroke of the Primary Teal color, creating a clear "pressed" or "active" intent without requiring 3D effects.

## Shapes

The design system uses a "Rounded" (0.5rem) corner radius for most UI elements to soften the interface.
- **Buttons and Inputs:** Use `rounded-md` (8px).
- **Choice Cards:** Use `rounded-lg` (16px) to distinguish them as large, interactive containers.
- **Progress Bars:** Fully rounded (pill-shaped) to represent a fluid, ongoing journey.

## Components

### Choice Cards
These are the primary interaction points. They are large, block-level buttons. 
- **Default:** White background, Slate-200 border, Body-LG text.
- **Selected:** Secondary Teal background (#E9F0F1), 2px Primary Teal border (#2C5F6F).

### Progress Indicators
Progress bars should be thin (8px height) with a smooth CSS transition on the width property. Use the Primary Teal color for the fill and a light neutral for the track. Include a "Question X of Y" label in `label-caps` style above the bar.

### Timers
The timer should be placed in the top-right corner or pinned to the top of the viewport. Use `timer-display` typography. If the time falls below 2 minutes, the color may shift to a subtle warning orange, but never a vibrating red, to maintain the "calm" directive.

### Instructions
Numbered lists for instructions use a custom teal-colored numeral with `body-lg` text. Each list item should have a `1.5rem` bottom margin to ensure each step is distinct.

### Buttons
"Start Test" and "Next" buttons are high-prominence.
- **Size:** Minimum height of `56px`.
- **Style:** Primary Teal background with white text.
- **Placement:** Always bottom-right or centered at the bottom of the container to signify progress.