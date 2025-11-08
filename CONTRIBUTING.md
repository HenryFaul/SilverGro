# Contributing to SilverGro

Thank you for your interest in contributing to SilverGro! This guide will help you understand our workflow and standards.

## 📋 Before You Start

1. **Read the documentation** in the `.ai/` directory:
   - [PROJECT_OVERVIEW.md](.ai/PROJECT_OVERVIEW.md) - Understand the project
   - [CODING_STANDARDS.md](.ai/CODING_STANDARDS.md) - Follow our standards
   - [COMMON_PATTERNS.md](.ai/COMMON_PATTERNS.md) - Use established patterns

2. **Check existing issues** - Look for related issues or feature requests

3. **Discuss major changes** - Open an issue first for significant changes

## 🚀 Development Workflow

### 1. Setup Your Environment

```bash
# Clone the repository
git clone [repository-url]
cd SilverGro

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Start development
php artisan serve    # Terminal 1
npm run dev         # Terminal 2
```

### 2. Create a Feature Branch

```bash
# Create and checkout a new branch
git checkout -b feature/your-feature-name

# Or for bug fixes
git checkout -b fix/bug-description
```

Branch naming conventions:
- `feature/` - New features
- `fix/` - Bug fixes
- `refactor/` - Code refactoring
- `docs/` - Documentation updates
- `test/` - Test additions/updates

### 3. Make Your Changes

Follow our [Coding Standards](.ai/CODING_STANDARDS.md):

**Backend (Laravel/PHP):**
- Use type hints for parameters and return types
- Follow PSR-12 coding standards
- Create Form Requests for validation
- Add PHPDoc comments for complex methods
- Use meaningful variable names

**Frontend (Vue/JavaScript):**
- Use Composition API with `<script setup>`
- Follow Vue 3 best practices
- Use Tailwind CSS utilities (avoid custom CSS)
- Add prop validation
- Keep components focused and reusable

**Database:**
- Create migrations for schema changes
- Use foreign key constraints
- Add indexes for performance
- Include rollback logic

### 4. Write Tests

```bash
# Create a test
php artisan make:test CustomerTest

# Run tests
php artisan test

# Run specific test
php artisan test --filter CustomerTest
```

Aim for:
- Feature tests for user-facing functionality
- Unit tests for complex business logic
- Test happy path and edge cases

### 5. Code Quality Checks

```bash
# Format PHP code
./vendor/bin/pint

# Check for errors (if configured)
./vendor/bin/phpstan analyse

# Lint JavaScript
npm run lint

# Format JavaScript
npm run format
```

### 6. Commit Your Changes

Follow conventional commit format:

```bash
git add .
git commit -m "feat: add customer credit limit validation"
```

Commit message format:
```
<type>: <subject>

<body>

<footer>
```

**Types:**
- `feat:` - New feature
- `fix:` - Bug fix
- `refactor:` - Code refactoring
- `docs:` - Documentation changes
- `test:` - Test additions/updates
- `chore:` - Maintenance tasks
- `style:` - Code style changes (formatting)
- `perf:` - Performance improvements

**Examples:**
```
feat: add customer export to Excel functionality

- Implement export action in CustomerController
- Add export button to customer index page
- Include all customer fields in export

Closes #123
```

```
fix: resolve N+1 query in transport orders

- Add eager loading for customer and supplier relationships
- Reduce query count from 50+ to 3

Fixes #456
```

### 7. Push and Create Pull Request

```bash
# Push your branch
git push origin feature/your-feature-name
```

Then create a Pull Request on GitHub/GitLab with:
- Clear title describing the change
- Description of what was changed and why
- Reference to related issues
- Screenshots for UI changes
- Test instructions

## 🎯 Pull Request Guidelines

### PR Checklist

Before submitting, ensure:

- [ ] Code follows [Coding Standards](.ai/CODING_STANDARDS.md)
- [ ] Tests are added/updated and passing
- [ ] No console errors or warnings
- [ ] No PHP errors or warnings
- [ ] Documentation is updated (if needed)
- [ ] Migrations are reversible
- [ ] Code is self-documenting or well-commented
- [ ] UI is responsive (mobile, tablet, desktop)
- [ ] Functionality works in different browsers
- [ ] No breaking changes (or clearly documented)

### PR Description Template

```markdown
## Description
Brief description of the changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Refactoring
- [ ] Documentation update

## Related Issues
Closes #123

## Changes Made
- Change 1
- Change 2
- Change 3

## Screenshots (if applicable)
[Add screenshots for UI changes]

## Testing Instructions
1. Step 1
2. Step 2
3. Expected result

## Checklist
- [ ] Tests added/updated
- [ ] Documentation updated
- [ ] Code follows standards
- [ ] No breaking changes
```

## 🐛 Bug Reports

When reporting bugs, include:

1. **Bug description** - Clear description of the issue
2. **Steps to reproduce** - Detailed steps
3. **Expected behavior** - What should happen
4. **Actual behavior** - What actually happens
5. **Environment** - Browser, OS, PHP version, etc.
6. **Screenshots/logs** - If applicable
7. **Possible fix** - If you have ideas

**Bug Report Template:**
```markdown
## Bug Description
[Clear description]

## Steps to Reproduce
1. Go to...
2. Click on...
3. See error

## Expected Behavior
[What should happen]

## Actual Behavior
[What actually happens]

## Environment
- Browser: Chrome 120
- OS: macOS Sonoma
- PHP: 8.2.14
- Laravel: 12.0

## Screenshots
[If applicable]

## Error Logs
```
[Paste relevant logs]
```

## Possible Solution
[If you have ideas]
```

## 💡 Feature Requests

When requesting features:

1. **Use case** - Explain why this is needed
2. **Proposed solution** - How you think it should work
3. **Alternatives** - Other approaches considered
4. **Additional context** - Mockups, examples, etc.

## 🔍 Code Review Process

### For Reviewers

- Check code quality and adherence to standards
- Test functionality thoroughly
- Review for security concerns
- Ensure proper error handling
- Check for performance issues
- Verify responsive design
- Test edge cases

### For Contributors

- Respond to feedback promptly
- Make requested changes
- Re-request review after changes
- Keep PR scope focused
- Be open to suggestions

## 📝 Code Style

### PHP

```php
// Good ✅
public function store(StoreCustomerRequest $request): RedirectResponse
{
    DB::transaction(function () use ($request) {
        $customer = Customer::create($request->validated());
        
        activity()
            ->performedOn($customer)
            ->log('Customer created');
    });
    
    return redirect()
        ->route('customers.show', $customer)
        ->with('success', 'Customer created successfully.');
}

// Avoid ❌
public function store($request)
{
    $customer = Customer::create($request->all()); // No validation
    return redirect('customers/' . $customer->id); // No route helper
}
```

### Vue

```html
<!-- Good (example) -->
<!-- This is an example Vue 3 \<script setup\> snippet. Marked as HTML to avoid static analyzers parsing it as a runnable file. -->
\<script setup\>
// Example only: Composition API pattern
// import { ref, computed } from 'vue';
// import { useForm } from '@inertiajs/vue3';

// const props = defineProps({
//     customer: { type: Object, required: true },
// });

// const form = useForm({ name: props.customer.name });
// const isValid = computed(() => form.name.length > 0);
\</script>
```

```html
<!-- Avoid (example) -->
<!-- Example of the older options API; shown for contrast only. Marked as HTML to avoid static analysis. -->
\<script\>
export default {
    props: ['customer'], // No prop type validation shown intentionally
    data() {
        return {
            name: this.customer?.name ?? ''
        };
    },
};
\</script>
```

## 🚫 Common Mistakes to Avoid

1. **Mass assignment vulnerabilities** - Always use `$fillable` or `$guarded`
2. **Missing validation** - Validate all user input
3. **N+1 queries** - Use eager loading
4. **Hardcoded values** - Use config files or constants
5. **Missing authorization checks** - Check permissions
6. **Ignoring soft deletes** - Remember `withTrashed()`
7. **Poor error handling** - Use try-catch blocks
8. **Mutating props** - Never mutate props in Vue
9. **Missing keys in v-for** - Always add unique keys
10. **Inline styles** - Use Tailwind classes

## 🎓 Learning Resources

### Laravel
- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Laracasts](https://laracasts.com)

### Vue.js
- [Vue 3 Documentation](https://vuejs.org)
- [Vue Composition API](https://vuejs.org/guide/extras/composition-api-faq.html)
- [Vue Best Practices](https://vuejs.org/style-guide/)

### Inertia.js
- [Inertia.js Documentation](https://inertiajs.com)

### Tailwind CSS
- [Tailwind Documentation](https://tailwindcss.com)

## 💬 Getting Help

- **Documentation** - Check `.ai/` directory first
- **Issues** - Search existing issues
- **Discussions** - Use GitHub Discussions for questions
- **Team** - Contact team members for clarification

## 🎉 Recognition

Contributors are recognized in:
- Git commit history
- Release notes for significant contributions
- Project documentation

Thank you for contributing to SilverGro! 🚀
