# AI Prompt Templates & Guidelines

This document provides templates and guidelines for effectively using AI to enhance, debug, and develop features for the SilverGro project.

## Context Files

Before working on any task, ensure the AI has access to these context files:
1. `.ai/PROJECT_OVERVIEW.md` - High-level project understanding
2. `.ai/CODING_STANDARDS.md` - Code style and best practices
3. `.ai/DATABASE_SCHEMA.md` - Database structure reference
4. `.ai/COMMON_PATTERNS.md` - Reusable code patterns
5. This file - Prompt templates

## Prompt Structure

### General Template

I need help with [FEATURE/BUG/TASK] in the SilverGro Laravel application.

- Area: [Customer Management / Transport / Products / etc.]
- Files involved: [List known files or "Unknown"]
- Related models: [Model names]

Current situation:
[Describe what currently exists or what's broken]

Desired outcome:
[Describe what should happen]

Technical requirements:
- [Requirement 1]
- [Requirement 2]

Constraints:
- [Any limitations or considerations]

---

## Bug Fix Prompts

### Template: Bug Investigation

I've encountered a bug in [FEATURE_NAME].

Symptoms:
[What's going wrong - error messages, incorrect behavior, etc.]

Steps to reproduce:
1. [Step 1]
2. [Step 2]
3. [Expected vs actual result]

Environment:
- Browser/Platform: [if frontend issue]
- PHP version: 8.2
- Laravel version: 12.0
- Vue version: 3.5.0

Affected files:
[List files if known, or request investigation]

Error logs:
[Paste relevant error messages]

Please investigate and provide:
1. Root cause analysis
2. Fix implementation (code edits)
3. Prevention strategy (tests, validation, etc.)

---

### Template: Performance Issue

I'm experiencing performance issues with [FEATURE/PAGE].

Problem:
[Describe slow operation - page load, query, etc.]

Current metrics:
- Load time: [seconds]
- Query count: [if known]
- Memory usage: [if known]

Context:
- Dataset size: [number of records]
- Frequency of use: [how often this runs]
- Related models: [models involved]

Please analyze and optimize:
1. Identify bottlenecks
2. Suggest optimizations (eager loading, caching, indexing)
3. Implement improvements
4. Verify performance gains

---

## Feature Implementation Prompts

### Template: New Feature (CRUD)

I need to implement a new [ENTITY_NAME] management feature.

Entity description:
[Brief description of what this entity represents in the business]

Required fields:
- [field_name] (type, validation rules) - [purpose]
- [field_name] (type, validation rules) - [purpose]

Relationships:
- BelongsTo: [related entity]
- HasMany: [related entity]

Required functionality:
- List view with pagination, search, filters
- Create form with validation
- Edit form
- Delete (soft delete if applicable)
- Show/detail view
- Any specific business logic

Permissions:
- Who can view: [role/permission]
- Who can create: [role/permission]
- Who can edit: [role/permission]
- Who can delete: [role/permission]

UI requirements:
- Integration with existing pages

Please implement:
1. Migration
2. Model with relationships
3. Controller with resourceful methods
4. Form Request validation
5. Routes
6. Vue components (Index, Show, Create/Edit)
7. Permissions setup

---

### Template: Enhance Existing Feature

I need to enhance the [EXISTING_FEATURE] feature.

Current state:
[Brief description of current implementation]

Enhancement needed:
[Detailed description of what to add/change]

Affected areas:
- Backend: [models, controllers, etc.]
- Frontend: [components, pages]
- Database: [new columns, relationships]

Requirements:
1. [Requirement 1]
2. [Requirement 2]

Backward compatibility:
[Must maintain existing functionality / May be breaking]

Please implement changes with:
1. Database migration (if needed)
2. Model updates
3. Controller/logic updates
4. Frontend component updates
5. Validation updates
6. Migration plan for existing data (if applicable)

---

### Template: Form with Complex Validation

I need to create/update a form for [ENTITY_NAME].

Form fields:
[List all fields with types and requirements]

Validation rules:
- [field]: [required, unique, etc.]
- [field]: [conditional validation]
- [field]: [custom validation - describe business rule]

Dynamic behavior:
- [Field X shows/hides based on Field Y]
- [Field A populates Field B automatically]
- [Calculations between fields]

API endpoints:
- [Any external data needed for dropdowns, etc.]

Error handling:
- Display validation errors inline
- Success message on submit
- Handle server errors gracefully

Please implement:
1. Form Request class with validation
2. Vue form component with validation
3. Error display
4. Success handling
5. Any computed fields or dynamic behavior

---

## Debugging Prompts

### Template: Error Analysis

I'm getting the following error:

Error message:
[Full error message with stack trace]

Context:
- Triggered when: [action that causes error]
- File: [file where error occurs]
- Line: [line number if known]
- Recent changes: [what was changed before error appeared]

Code snippet:
[Relevant code where error occurs]

Please:
1. Explain what's causing the error
2. Provide fix
3. Suggest how to prevent similar errors

---

### Template: Unexpected Behavior

[FEATURE] is not working as expected.

Expected behavior:
[What should happen]

Actual behavior:
[What actually happens]

Code involved:
[Relevant code]

Data sample:
[Sample data being processed, if relevant]

Please debug and fix the issue.

---

## Refactoring Prompts

### Template: Code Refactoring

I need to refactor [FILE/METHOD/CLASS].

Current code:
[Paste current code]

Issues with current code:
- [Code smell, duplication, complexity, etc.]
- [Performance issues]
- [Maintainability concerns]

Requirements for refactored code:
- Maintain existing functionality
- Follow Laravel/Vue best practices
- Improve [readability / performance / testability]

Please refactor with:
1. Improved code
2. Explanation of changes
3. Any tests to verify behavior unchanged

---

### Template: Database Optimization

I need to optimize the [TABLE_NAME] table and related queries.

Current issues:
- [Slow queries]
- [Missing indexes]
- [N+1 problems]

Usage patterns:
- [How the data is accessed]
- [Common filters/searches]
- [Typical dataset size]

Current schema:
[Table structure if known]

Problematic queries:
[Eloquent queries that are slow]

Please provide:
1. Index recommendations
2. Query optimizations
3. Eloquent eager loading improvements
4. Migration for schema changes (if needed)

---

## Testing Prompts

### Template: Add Tests

I need tests for [FEATURE/CLASS/METHOD].

Code to test:
[Code that needs testing]

Test cases needed:
- Happy path: [describe]
- Edge cases: [list edge cases]
- Error cases: [list error scenarios]
- Validation: [list validation rules to test]

Test type:
- Unit test
- Feature test

Please create:
1. Test file with descriptive test methods
2. Test setup/factories if needed
3. Assertions for all cases
4. Documentation of what's being tested

---

## Documentation Prompts

### Template: Add Documentation

I need documentation for [FEATURE/CLASS/API].

What needs documenting:
[Feature, API endpoints, complex algorithm, etc.]

Audience:
[Developers, API consumers, end users]

Current code:
[Code to document]

Please provide:
1. PHPDoc comments for classes/methods
2. README section (if applicable)
3. Example usage
4. Edge cases and gotchas

---

## UI/UX Prompts

### Template: UI Component

I need a [COMPONENT_NAME] Vue component.

Purpose:
[What this component does]

Props:
- [prop_name]: [type] - [description]
- [prop_name]: [type] - [description]

Events:
- [event_name] - [when emitted, what data]

Visual design:
[Description or link to mockup]

Behavior:
- [Interaction 1]
- [Interaction 2]
- [State changes]

Responsive:
- Mobile: [mobile behavior]
- Tablet: [tablet behavior]
- Desktop: [desktop behavior]

Please implement:
1. Component with <script setup>
2. Template with Tailwind styling
3. Props validation
4. Events emitted
5. Responsive design
6. Accessibility considerations

---

### Template: Improve UX

I want to improve the UX of [PAGE/FEATURE].

Current UX issues:
- [Issue 1: description]
- [Issue 2: description]

User feedback:
[What users are saying]

Proposed improvements:
1. [Improvement 1]
2. [Improvement 2]

Constraints:
- Must work on mobile
- Must be accessible

Please implement improvements with:
1. Updated components
2. Better user feedback (loading, success, errors)
3. Improved layout/flow
4. Accessibility enhancements

---

## Integration Prompts

### Template: API Integration

I need to integrate with [EXTERNAL_SERVICE].

Service details:
- API documentation: [URL]
- Authentication: [method]
- Endpoints needed: [list endpoints]

Use cases:
[What we're using this for]

Data flow:
[How data moves between systems]

Error handling:
[How to handle API failures]

Please implement:
1. Service class for API calls
2. Configuration for credentials
3. Error handling
4. Logging
5. Tests (mocked external calls)

---

## Migration Prompts

### Template: Data Migration

I need to migrate data from [SOURCE] to [DESTINATION].

Source:
- Format: [CSV, old database, API, etc.]
- Structure: [describe structure]
- Volume: [number of records]

Destination:
- Table: [table name]
- Validation: [what validation needed]

Transformation rules:
- [Field mapping]
- [Data cleaning needed]
- [Calculations]

Error handling:
- [How to handle invalid records]
- [Rollback strategy]

Please create:
1. Migration/import command
2. Validation logic
3. Error logging
4. Progress tracking
5. Rollback capability

---

## Best Practices for AI Assistance

### When Asking for Help

1. Be specific: Provide exact file paths, line numbers, error messages
2. Include context: Share relevant code, not just the problematic line
3. State assumptions: What you've tried, what you know/don't know
4. Define success: What would constitute a complete solution
5. Mention constraints: Performance, backward compatibility, etc.

### Provide This Information

- Current Laravel version (12.0)
- Current Vue version (3.5.0)
- PHP version (8.2)
- Relevant installed packages
- Database type (if relevant)
- Environment (local, staging, production)

### After Receiving Solution

1. Review thoroughly: Understand the changes before applying
2. Test completely: Test happy path and edge cases
3. Check errors: Use `get_errors` tool to validate
4. Ask questions: If anything is unclear, ask for clarification
5. Request tests: If tests weren't provided, ask for them

### Iterative Refinement

If the first solution isn't perfect:
The solution works but [ISSUE]. Can you adjust it to:
- [Specific change 1]
- [Specific change 2]

While maintaining:
- [Thing to keep]
- [Thing to keep]

## Common Task Checklists

### New Feature Checklist

- [ ] Database migration created and tested
- [ ] Model with fillable fields and relationships
- [ ] Controller with proper methods
- [ ] Form Request with validation
- [ ] Routes defined (web.php)
- [ ] Permissions added (if using Spatie)
- [ ] Vue components created
- [ ] Inertia responses correct
- [ ] Error handling implemented
- [ ] Success messages added
- [ ] Mobile responsive
- [ ] Browser tested
- [ ] No console errors
- [ ] No PHP errors

### Bug Fix Checklist

- [ ] Bug reproduced and understood
- [ ] Root cause identified
- [ ] Fix implemented
- [ ] Edge cases handled
- [ ] Tests added to prevent regression
- [ ] Code reviewed for similar issues
- [ ] Documentation updated (if needed)
- [ ] Tested in multiple scenarios

### Refactoring Checklist

- [ ] Tests written before refactoring (if none exist)
- [ ] Refactoring completed
- [ ] All tests still pass
- [ ] No functionality broken
- [ ] Performance maintained or improved
- [ ] Code more readable/maintainable
- [ ] Comments updated
- [ ] Dead code removed

## Quick Reference Commands

### Useful Prompts

Quick fix:
```
Fix this error: [error message]
In file: [file path]
```

Quick feature:
```
Add a [field_name] field to [Model] with [validation]
```

Quick UI update:
```
Update [Component] to show [element] when [condition]
```

Quick optimization:
```
Optimize this query: [query code]
```

Quick validation:
```
Add validation for [field]: [business rule]
```

## Anti-Patterns to Avoid

### Don't Do This:
❌ "Fix my code" (too vague)
❌ "Make it better" (no specific goal)
❌ "Debug this" (no error or context)
❌ "Create a form" (no specifications)
❌ Ask for huge changes without breaking into steps

### Do This Instead:
✅ "Fix the N+1 query in CustomerController@index when loading transport transactions"
✅ "Improve performance of the transaction summary page by implementing eager loading"
✅ "Debug the error 'Call to undefined method' on line 45 of CustomerController"
✅ "Create a customer form with fields: name, email (required), phone (optional, validated), credit limit (decimal)"
✅ Break large features into smaller, manageable tasks
</attachment> </attachments>xima conversation. Now need to proceed. We already called insert_edit_into_file; need to call get_errors. Use functions.get_errors with relevant files paths. Let's call with .ai/AI_PROMPTING_GUIDE.md and other edited files: README.md, CONTRIBUTING.md, .ai files maybe. Per editFileInstructions: after editing a file, MUST call get_errors to validate. So do that. Then read back first lines to confirm content. Use functions.get_errors. Then functions.read_file for AI_PROMPTING_GUIDE.md head. Let's call get_errors. Then read_file. We'll include files in get_errors list. Use functions.get_errors. Then read file. Then respond to user with status and summary and next steps. Keep concise. Let's call tools. Need to use functions.get_errors. Then functions.read_file. Proceed.ianchi. We'll make tool calls now.curité. (Use commentary channel for tool calls.) Let's call get_errors. Then read file. Then respond. Ensure compliance with tool use instructions: preface each tool call batch with one-sentence why/what/outcome—We did earlier: required. Must preface this tool call. I need to include such preface before calling multiple tools. In this turn, we will make a small batch of two tools; earlier instruction: 
