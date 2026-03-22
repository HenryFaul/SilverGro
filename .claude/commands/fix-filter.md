Fix the scopeFilter orWhere bug (BUG-001) in the model: **$ARGUMENTS**

Read `AI/TODO.md` BUG-001 for full context.

**The bug:** `scopeFilter()` methods use `orWhere()` without wrapping in a closure, causing OR conditions to leak into outer query clauses when multiple filters are active simultaneously.

**The pattern to find:**
```php
// BROKEN — orWhere leaks out of the intended group
$query->where('name', 'LIKE', "%{$filters['search']}%")
      ->orWhere('email', 'LIKE', "%{$filters['search']}%");
```

**The fix — wrap in a closure:**
```php
// CORRECT — OR conditions are grouped
$query->where(function ($q) use ($filters) {
    $q->where('name', 'LIKE', "%{$filters['search']}%")
      ->orWhere('email', 'LIKE', "%{$filters['search']}%");
});
```

Read the specified model file, find the `scopeFilter()` method, and apply this fix to every `orWhere` chain in it. Do not change any other logic.

After fixing, confirm the SQL that would be generated is correctly grouped with parentheses around the OR conditions.
