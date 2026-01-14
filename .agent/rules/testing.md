---
trigger: always_on
---
# Testing Standards

## 1. Framework
- **Pest PHP**: Use Pest for all backend testing. PHPUnit syntax is discouraged.
- **Browser Testing**: Use Laravel Dusk or Inertia testing helpers for critical flows.

## 2. Coverage Requirements
- **Critical Paths**: ABSOLUTELY MUST have feature tests:
    - Authentication (Login, Register, Reset Password)
    - Membership Purchase/Renewal
    - Event Check-in
    - Broadcast Sending
- **Happy & Sad Paths**: Test both success and expected failure cases (e.g., "User cannot check in twice").

## 3. Data & Factories
- **Realism**: Use `faker` localized to Italy (`it_IT`) where possible.
- **No "Foo Bar"**: Use realistic names (e.g., "Mario Rossi", "Sagra del Prosciutto").
- **State**: Use Factory States for different user roles (e.g., `User::factory()->admin()->create()`).

## 4. Frontend Testing
- **Vitest**: Use Vitest for complex Svelte logic/stores.
- **Components**: Test generic UI components (Bits UI wrappers) independently.
