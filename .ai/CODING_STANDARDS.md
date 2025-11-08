
# Coding Standards & Best Practices
|

## PHP/Laravel Standards

### General PHP
- PHP 8.2+ features encouraged (typed properties, match expressions, etc.)
- PSR-12 coding standards
- Type hints for all method parameters and return types
- Strict types declaration: `declare(strict_types=1);` where appropriate

### Laravel Specific

#### Controllers
```php
// Keep controllers thin - delegate business logic to actions/services
class CustomerController extends Controller
{
    public function store(StoreCustomerRequest $request)
    {
        // Validate via Form Request
        // Delegate to action class or model method
        // Return Inertia response
        return Inertia::render('Customer/Show', [
            'customer' => $customer
        ]);
    }
}
```

#### Models
- Use `$fillable` or `$guarded` properties
- Define relationships explicitly with return types
- Use accessors/mutators with new attribute pattern (Laravel 11+)
- Add `$appends` for computed properties
- Use soft deletes where appropriate
- Add meaningful scopes for common queries

```php
protected function fullName(): Attribute
{
    return new Attribute(
        get: fn () => "{$this->first_name} {$this->last_name}"
    );
}

public function scopeActive(Builder $query): void
{
    $query->where('is_active', true);
}
```

#### Form Requests
- Create separate request classes for validation
- Add authorization logic in `authorize()` method
- Use custom validation rules when needed

#### Database
- Use migrations for all schema changes
- Add meaningful indexes
- Use foreign key constraints
- Include rollback logic in migrations
- Seeders for default/test data

#### Queries
- Use eager loading to prevent N+1 queries: `->with(['relation'])`
- Use `->select()` to limit columns when not all needed
- Use query scopes for reusable query logic
- Use database transactions for multi-step operations

```php
DB::transaction(function () {
    // Multiple database operations
});
```

## Vue.js Standards

### Component Structure
```vue
<script setup>
// 1. Imports (external libraries first, then internal)
import { ref, computed, watch, onMounted } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

// 2. Props definition
const props = defineProps({
    customer: Object,
    permissions: Object,
});

// 3. Reactive state
const form = useForm({
    // form fields
});

// 4. Computed properties
const hasPermission = computed(() => {
    return props.permissions.canEdit;
});

// 5. Methods
const handleSubmit = () => {
    form.post(route('customers.store'));
};

// 6. Lifecycle hooks
onMounted(() => {
    // initialization
});

// 7. Watchers
watch(() => props.customer, (newVal) => {
    // react to changes
});
</script>

<template>
  <!-- Template with proper indentation -->
  <div class="container">
    <!-- Content -->
  </div>
</template>
```

### Naming Conventions
- **Components**: PascalCase (e.g., `CustomerForm.vue`)
- **Props**: camelCase (e.g., `customerId`)
- **Events**: kebab-case (e.g., `@update-customer`)
- **Variables**: camelCase (e.g., `const isActive = ref(false)`)
- **Constants**: SCREAMING_SNAKE_CASE (e.g., `const MAX_ITEMS = 100`)

### Vue Best Practices
- Use Composition API with `<script setup>`
- Define prop types explicitly
- Emit events for parent communication, not prop mutation
- Use computed properties for derived state
- Keep template logic simple - complex logic in computed/methods
- Use `v-show` for toggling, `v-if` for conditional rendering
- Add keys to v-for lists
- Avoid inline styles - use Tailwind classes

### Inertia.js Patterns
```javascript
// Navigation
router.visit(route('customers.show', customer.id));

// Form submission
form.post(route('customers.store'), {
    preserveScroll: true,
    onSuccess: () => {
        // Handle success
    },
    onError: () => {
        // Handle errors
    },
});

// Preserve state on reload
router.reload({ only: ['customers'] });
```

## Tailwind CSS

### Class Organization
Order classes logically:
1. Layout (flex, grid, display)
2. Positioning (absolute, relative, top, left)
3. Box model (width, height, padding, margin)
4. Typography (font, text)
5. Visual (background, border, shadow)
6. Misc (cursor, transition, animation)

```html
<div class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium bg-white border rounded-md shadow-sm hover:bg-gray-50">
```

### Responsive Design
- Mobile-first approach
- Use responsive prefixes: `sm:`, `md:`, `lg:`, `xl:`, `2xl:`
- Test on multiple screen sizes

### Custom Classes
- Define custom classes in `resources/css/app.css`
- Use `@apply` directive for repeated patterns
- Prefer Tailwind utilities over custom CSS

## JavaScript

### Modern JavaScript
- Use ES6+ features (arrow functions, destructuring, spread operator)
- Use `const` by default, `let` when reassignment needed, avoid `var`
- Async/await over promise chains
- Template literals for string interpolation
- Optional chaining and nullish coalescing

```javascript
// Good
const user = response?.data?.user ?? null;

// Avoid
var user = response && response.data && response.data.user ? response.data.user : null;
```

### Code Organization
- One component per file
- Group related functionality
- Extract reusable logic into composables
- Keep functions small and focused
- Use meaningful variable and function names

## Error Handling

### Backend
```php
try {
    // risky operation
} catch (Exception $e) {
    Log::error('Error message', [
        'exception' => $e->getMessage(),
        'user_id' => auth()->id(),
    ]);
    
    return back()->with('error', 'User-friendly message');
}
```

### Frontend
```javascript
form.post(route('action'), {
    onError: (errors) => {
        swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: errors.message || 'Something went wrong!',
        });
    },
});
```

## Security

### Backend Security
- Use Form Requests for validation
- Implement authorization checks (policies, gates)
- Sanitize user input
- Use parameterized queries (Eloquent does this)
- Implement CSRF protection (Laravel default)
- Hash sensitive data
- Use environment variables for secrets

### Frontend Security
- Never trust user input
- Validate on backend even if validated on frontend
- Don't expose sensitive data in props
- Use proper authentication checks
- Sanitize displayed HTML content

## Performance

### Backend
- Use query caching where appropriate
- Implement pagination for large datasets
- Use queues for time-consuming tasks
- Optimize database indexes
- Use eager loading for relationships
- Implement response caching for static data

### Frontend
- Lazy load components when possible
- Debounce user input handlers
- Minimize prop drilling
- Use `v-show` instead of `v-if` for frequently toggled elements
- Optimize images and assets
- Code splitting for large pages

## Testing

### Writing Tests
- Test happy path and edge cases
- Use descriptive test names
- Follow AAA pattern: Arrange, Act, Assert
- Mock external dependencies
- Test one thing per test method

```php
public function test_customer_can_be_created_with_valid_data()
{
    // Arrange
    $data = ['name' => 'Test Customer'];
    
    // Act
    $response = $this->post(route('customers.store'), $data);
    
    // Assert
    $response->assertRedirect();
    $this->assertDatabaseHas('customers', $data);
}
```

## Documentation

### Code Comments
- Comment "why" not "what"
- Use PHPDoc for methods and classes
- Document complex business logic
- Keep comments up to date

### Commit Messages
- Use descriptive commit messages
- Start with verb (Add, Fix, Update, Remove)
- Reference issue numbers when applicable
- Keep first line under 72 characters

```
Add customer credit limit validation

- Implement soft and hard credit limit checks
- Add validation rules in CustomerRequest
- Update UI to display limit warnings
- Fixes #123
```

## Common Pitfalls to Avoid

1. **N+1 Query Problem**: Always use eager loading
2. **Mass Assignment Vulnerability**: Define `$fillable` or `$guarded`
3. **Missing Authorization**: Check permissions on all routes
4. **Ignoring Soft Deletes**: Remember to handle in queries
5. **Hardcoded Values**: Use config files or constants
6. **Missing Validation**: Validate all user input
7. **Not Handling Errors**: Implement proper error handling
8. **Poor Date Handling**: Be consistent with timezones
9. **Mutating Props**: Never mutate props in Vue components
10. **Missing Keys in v-for**: Always add unique keys

## Tools & Linting

### PHP
- Laravel Pint for code formatting
- PHPStan for static analysis (if configured)
- Laravel Debugbar for development

### JavaScript
- ESLint with Vue plugin
- Prettier for formatting
- Vue DevTools browser extension

### IDE
- Use IDE helpers for Laravel (laravel-ide-helper)
- Configure editor for ESLint and Prettier auto-fix
- Use appropriate extensions for Vue and Laravel

