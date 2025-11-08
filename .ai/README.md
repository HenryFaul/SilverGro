# AI Documentation Directory

This directory contains comprehensive documentation to help AI assistants understand and work with the SilverGro project effectively.

## 📁 Files Overview

### [PROJECT_OVERVIEW.md](./PROJECT_OVERVIEW.md)
**Purpose:** High-level understanding of the entire project  
**Use when:** Starting any new task, understanding project structure, or getting context about the application

**Contains:**
- Tech stack (Laravel 12, Vue 3, Inertia.js, Tailwind)
- Project structure and business domains
- Key models and entities
- Core features and functionality
- Development patterns
- Common issues to watch for

### [CODING_STANDARDS.md](./CODING_STANDARDS.md)
**Purpose:** Code style and best practices guide  
**Use when:** Writing new code, refactoring, or reviewing code quality

**Contains:**
- PHP/Laravel standards
- Vue.js/JavaScript standards
- Tailwind CSS conventions
- Error handling patterns
- Security best practices
- Performance optimization guidelines
- Testing standards
- Common pitfalls to avoid

### [DATABASE_SCHEMA.md](./DATABASE_SCHEMA.md)
**Purpose:** Database structure and relationships reference  
**Use when:** Working with models, queries, or database operations

**Contains:**
- All major tables and their columns
- Relationship definitions
- Polymorphic relationships
- Foreign keys and indexes
- Soft delete information
- Activity logging setup
- Query optimization notes

### [COMMON_PATTERNS.md](./COMMON_PATTERNS.md)
**Purpose:** Reusable code patterns and examples  
**Use when:** Implementing common features or following established patterns

**Contains:**
- Controller patterns (CRUD operations)
- Vue component patterns
- Inertia.js patterns
- Form handling
- Permission checks
- Query patterns
- Notification patterns
- UI component examples

### [AI_PROMPTING_GUIDE.md](./AI_PROMPTING_GUIDE.md)
**Purpose:** Templates and guidelines for effective AI prompts  
**Use when:** Requesting AI assistance for specific tasks

**Contains:**
- Prompt templates for different scenarios
- Bug fixing prompts
- Feature implementation prompts
- Refactoring prompts
- Testing prompts
- Best practices for AI collaboration
- Task checklists

## 🚀 Quick Start Guide

### For Bug Fixes
1. Read `PROJECT_OVERVIEW.md` to understand the area affected
2. Check `DATABASE_SCHEMA.md` if database queries are involved
3. Review `CODING_STANDARDS.md` for error handling patterns
4. Use templates from `AI_PROMPTING_GUIDE.md` to structure your request

### For New Features
1. Review `PROJECT_OVERVIEW.md` for similar existing features
2. Check `DATABASE_SCHEMA.md` for related tables and relationships
3. Follow patterns in `COMMON_PATTERNS.md` for implementation
4. Adhere to `CODING_STANDARDS.md` for code quality
5. Use feature template from `AI_PROMPTING_GUIDE.md`

### For Refactoring
1. Review `CODING_STANDARDS.md` for best practices
2. Check `COMMON_PATTERNS.md` for established patterns
3. Ensure compliance with standards in `CODING_STANDARDS.md`
4. Use refactoring template from `AI_PROMPTING_GUIDE.md`

### For Performance Optimization
1. Check `DATABASE_SCHEMA.md` for index opportunities
2. Review query patterns in `COMMON_PATTERNS.md`
3. Follow performance guidelines in `CODING_STANDARDS.md`
4. Use optimization prompt from `AI_PROMPTING_GUIDE.md`

## 📋 Recommended Reading Order

### First Time Working with Project
1. `PROJECT_OVERVIEW.md` - Get the big picture
2. `CODING_STANDARDS.md` - Understand code expectations
3. `DATABASE_SCHEMA.md` - Learn the data structure
4. `COMMON_PATTERNS.md` - See how things are done
5. `AI_PROMPTING_GUIDE.md` - Learn to ask for help effectively

### Before Each Task
1. Skim relevant sections of `PROJECT_OVERVIEW.md`
2. Check `DATABASE_SCHEMA.md` for related entities
3. Review applicable patterns in `COMMON_PATTERNS.md`
4. Use appropriate template from `AI_PROMPTING_GUIDE.md`

## 🎯 Common Scenarios

### Scenario 1: Adding a New CRUD Resource
**Files to reference:**
- `COMMON_PATTERNS.md` → Standard Controller patterns
- `CODING_STANDARDS.md` → Validation and security standards
- `DATABASE_SCHEMA.md` → Related tables and relationships
- `AI_PROMPTING_GUIDE.md` → New Feature (CRUD) template

### Scenario 2: Fixing a Database Query Issue
**Files to reference:**
- `DATABASE_SCHEMA.md` → Table structure and relationships
- `COMMON_PATTERNS.md` → Query patterns section
- `CODING_STANDARDS.md` → Query optimization guidelines
- `AI_PROMPTING_GUIDE.md` → Performance Issue template

### Scenario 3: Creating a Complex Form
**Files to reference:**
- `COMMON_PATTERNS.md` → Form patterns and validation
- `CODING_STANDARDS.md` → Vue and form standards
- `AI_PROMPTING_GUIDE.md` → Form with Complex Validation template

### Scenario 4: Implementing Permissions
**Files to reference:**
- `PROJECT_OVERVIEW.md` → Permission system overview
- `COMMON_PATTERNS.md` → Permission patterns (frontend & backend)
- `CODING_STANDARDS.md` → Security standards
- `DATABASE_SCHEMA.md` → Users and roles tables

### Scenario 5: UI Component Development
**Files to reference:**
- `COMMON_PATTERNS.md` → Vue component patterns
- `CODING_STANDARDS.md` → Vue and Tailwind standards
- `AI_PROMPTING_GUIDE.md` → UI Component template

## 💡 Tips for AI Collaboration

### DO:
✅ Reference specific files when asking for help  
✅ Provide context about what you're working on  
✅ Include error messages and stack traces  
✅ Share relevant code snippets  
✅ Specify the area of the app (Customer, Transport, etc.)  
✅ Mention which patterns you want to follow  

### DON'T:
❌ Ask vague questions without context  
❌ Request changes without explaining business requirements  
❌ Ignore existing patterns and standards  
❌ Skip reading relevant documentation sections  
❌ Forget to mention constraints or requirements  

## 🔄 Keeping Documentation Updated

This documentation should be updated when:
- New major features are added
- Coding standards change
- New patterns are established
- Database schema significantly changes
- Tech stack is upgraded

**How to update:**
1. Identify which file needs updating
2. Make changes that are clear and concise
3. Follow the existing format and style
4. Update this README if adding new files

## 📚 Additional Resources

### Laravel Resources
- [Laravel Documentation](https://laravel.com/docs/12.x)
- [Laravel Jetstream](https://jetstream.laravel.com/)
- [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission/v6/introduction)

### Vue Resources
- [Vue 3 Documentation](https://vuejs.org/)
- [Inertia.js Documentation](https://inertiajs.com/)
- [HeadlessUI](https://headlessui.com/)
- [Tailwind CSS](https://tailwindcss.com/)

### Best Practices
- [Laravel Best Practices](https://github.com/alexeymezenin/laravel-best-practices)
- [Vue Best Practices](https://vuejs.org/style-guide/)
- [Clean Code Principles](https://github.com/ryanmcdermott/clean-code-javascript)

## 🤝 Contributing to Documentation

If you identify gaps in this documentation:
1. Note what information was missing or unclear
2. Add the information in the appropriate file
3. Follow the markdown formatting style
4. Keep explanations concise but complete
5. Include code examples where helpful

## ⚙️ File Maintenance

**Last Updated:** November 8, 2025  
**Version:** 1.0  
**Maintained By:** Development Team

**Review Schedule:**
- Monthly: Quick review for accuracy
- Quarterly: Comprehensive update for new features
- After Major Changes: Immediate update to affected sections

---

## Quick Command Reference

### Reading Files
When AI needs to read project files:
```
Please read the following files for context:
- .ai/PROJECT_OVERVIEW.md
- .ai/DATABASE_SCHEMA.md
- [specific app files]
```

### Implementing Features
When implementing new features:
```
I need to implement [feature]. Please reference:
- .ai/COMMON_PATTERNS.md for implementation patterns
- .ai/CODING_STANDARDS.md for code quality
- .ai/DATABASE_SCHEMA.md for data structure
```

### Debugging
When debugging issues:
```
I have a bug in [area]. Context files:
- .ai/PROJECT_OVERVIEW.md (section on [relevant area])
- .ai/DATABASE_SCHEMA.md (tables involved)
- Error details: [paste error]
```

---

**Remember:** These documentation files are designed to make AI assistance more effective and accurate. Always provide relevant context from these files when requesting help!

