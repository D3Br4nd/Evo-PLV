---
trigger: always_on
---
# Architectural Guidelines

## 1. Logic Placement
- **Skinny Controllers**: Controllers should only handle request validation, delegate to an Action/Service, and return a response.
- **Actions**: Use Single-Action Classes (e.g., `App\Actions\Events\CheckInMember`) for complex business logic.
- **Fat Models**: Keep models focused on relationships, scopes, and simple accessors. Avoid complex business logic in models.

## 2. Frontend-Backend Data Flow (Inertia)
- **Props**: Pass data to Views via Inertia Props. Use Resource classes (`JsonResource`) to transform models before sending them to the frontend.
- **Shared Props**: Use `HandleInertiaRequests` middleware only for global app state (User, Flash messages, Config).

## 3. Type Safety
- **Strict Types**: usage `declare(strict_types=1);` in all PHP files.
- **Return Types**: Always specify return types for methods.
- **DTOs**: Use Data Transfer Objects for complex data structures passing between layers.

## 4. Error Handling
- **Exceptions**: Throw custom exceptions (e.g., `AlreadyCheckedInException`) and let the global handler or controller catch them to return user-friendly flash messages.
