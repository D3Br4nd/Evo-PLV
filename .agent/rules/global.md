---
trigger: always_on
---

# Project: Pro Loco Venticanese Evolution
# Stack: Laravel 12, Svelte 5, Tailwind 4, Postgres 18

## 1. Coding Standards (Svelte 5)
- **ALWAYS** use Svelte 5 Runes syntax.
- **NEVER** use `export let` for props. Use `let { propName } = $props();`.
- **NEVER** use `writable()` stores for local state. Use `let x = $state(0);`.
- Use `snippet` instead of `<slot>`.

## 2. Styling (Tailwind 4 & Shadcn Default)
- **CSS-First:** Do not use `tailwind.config.js`. All configuration must be in `src/app.css` using the `@theme` directive.
- **Theme:** Use the **Standard Shadcn Zinc Theme** (Black Primary, White Background).
- **Tweakcn Compatibility:** Define all colors as CSS variables (e.g., `--primary: 240 5.9% 10%;`) inside `:root` and map them in `@theme`.
- **Constraint:** Do not use custom colors yet. Keep strictly to the monochrome Zinc palette.

## 3. Database & Models (Laravel 12)
- **UUIDv7:** Every model must use the `HasUuids` trait and strictly implement UUIDv7 logic.
- **Migrations:** DO NOT generate multiple migration files. **SQUASH** all schema definitions into a SINGLE file named `2025_01_01_000000_create_plv_schema.php`.
- **Seeds:** Populate `DatabaseSeeder.php` with real Venticano data (e.g., "Fiera Campionaria 2025", "Sagra del Prosciutto") to test the UI with real content.

## 4. Context & Tone
- This is a PWA for "Pro Loco Venticanese".
- The Tech Lead is "Max Brandini".
- Keep the UI minimal, clean, and high-performance.