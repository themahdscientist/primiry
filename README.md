# PRIMIRY - Enhancing education with technology

## Issues
<!-- *resolved -->
- add a view page to the guardian resource and use that as the link to view the guardian of a particular student on the table
- add real-time validation to the entirety of the app
- you don't have to modify the state of year_head if it's already an "id" and not a "name"
- grades have problem hooking up the year head (staffI) id .. needs to be re-migrated
- when an option from a dropdown is selected and saved, it shouldn't be found in the searchable list again. it's preferred behavior
- add a graduated_at column in the users table for students record timestamp
- try putting fields that can't be modified or should be extracted to the side  at the sidebar schema. it'll look more aesthetically pleasing.
- add the nin field for students but it's optional
<!-- todo:unresolved -->
- the trashed filter on the table is causing the resource pages to crash w the error:  
- you can add clauses to the unique validation method to have the same NIN in more than one record for as long as their roles are different. this is handled programmatically. the unique method in the database schema doesn't support that, or does it? ask gpt. [update: why will i want this? ask AI]
- look for another icon set that greatly compliments heroicons.
- students should log into their portal using their admission number.
- add a button to a custom page on the classroom resource to create multiple classrooms w the same form teacher at once
- look for what scopedUnique actually improves in ur data retrieval.
- there are many filament methods u need to apply on tables and forms and even in a service provider; check documentation.

- [backend] change the settings table to schools .. use the schools table as tenancy and hook everything up! MOST PARAMOUNT!
- [frontend] add a column showing the amount of wards a guardian has
- [backend] the student's admission should be a series of numbers w components from things that increment like the current timestamp and maybe a randomly generated number.
- [fullstack] add buttons to assign subjects to particular grades in both subjects and grades tables and their functionalities [copilot]; also add the subject counts to the particular grades.
- [frontend] add a relationship manager for the subjects in the grades edit form
- [frontend] add a back button
- [frontend] add a context menu
- [backend] binkode/laravel-paystack (i'm yet to install this crucial package in older skoolmaven versions) - rethinking using paystack. flutterwave is better.
<!-- ?resolving -->
- checkout if u can get a replacement for the nationality function in the student's model. use AI.

```php
// this gives real-time validation
protected static function verifyNIN($livewire, Field $component, mixed $state, Get $get)
    {
        $livewire->validateOnly($component->getStatePath(), messages: ['unique' => 'This :attribute belongs to another.']);
        // verify nin
    }
```

Problem

Build multi-tenancy for a school management Laravel app: isolated data per school (tenant) while keeping one codebase.

Proposed approach (shared database, tenant_id/"school_id")

Phases & Steps

1) Decision & scaffold
- Choose shared DB + schema with school_id (fastest). Optionally separate DB per-tenant later.
- Create new Laravel app: composer create-project laravel/laravel

2) Tenant model & migration
- Create schools table (model: School) with fields: id, name, code, billing info, owner_user_id, settings JSON, created_at, etc.
- Add unique school_code and indexes.

3) Design tenancy primitives
- Create a Tenantable base model or Trackable: defines school_id fillable, casts, timestamps, soft deletes.
- Implement TenantScope (global scope) to automatically filter by school_id.
- Implement TenantObserver to set school_id on create from resolved tenant.

4) Tenant resolution
- Implement TenantResolver middleware/service to determine current tenant by:
  - subdomain (school.example.com) OR
  - request header / api key OR
  - route parameter (e.g., /s/{school}/...)
- Bind resolved School into container and set current_tenant in request and app singleton.

5) Migrations & DB
- For every domain table (students, staff, classrooms, grades, subjects, schedules, timetables, results, etc.) add foreignId('school_id')->constrained('schools') and index.
- For pivot tables, include school_id to avoid cross-tenant mixing.
- Add composite unique constraints where needed (school_id + unique_field).

6) Auth & Users
- Users belong to a school (user.school_id) or to a super-admin root account.
- Support cross-school admins (super admin) via role flag.
- On login, ensure user.school_id matches resolved tenant or disallow.

7) File storage & assets
- Store school assets under school-specific paths (storage/app/schools/{id}/...)
- Configure Vite/asset URLs accordingly.

8) Caching, queues & rate limits
- Namespace cache by school_id.
- Tag/route jobs with school_id and, if using central queues, ensure workers respect tenant context.
- Apply per-tenant rate-limiting.

9) Policies & Resources
- Update Policies, Filament resources, and controllers to assume tenant scoping. Prefer global TenantScope to avoid missing filters.

10) Tests, factories, seeders
- Create factories using School factory and set school_id in created models.
- Write integration tests asserting tenant isolation and resolver correctness.

11) Data migration (for existing apps)
- Create backfill scripts to set school_id based on user->school relationship.
- Run in transactions and verify.

12) Operations & Billing
- Add subscription/billing linking to schools table.
- Implement onboarding flow to create a School, an owner User, and default settings.

13) Monitoring & backups
- Backup DB regularly; consider per-tenant export features.
- Add tenant-aware logging (include school_id in logs).

Deliverables
- Migrations: schools table + school_id on tenant tables and pivots
- Models: School, Tenantable/Trackable base, TenantScope, TenantObserver
- Middleware/service: TenantResolver
- Auth adjustments, policies, Filament resources, tests, seeders

Notes/Trade-offs
- Shared DB is easiest and performant; separate DBs per tenant provides stronger isolation but more operational complexity.
- Decide tenant resolution strategy early (subdomain vs route) — it affects routing, auth cookies, and wildcard domains.

Appendix: Example migrations/snippets available on request.
