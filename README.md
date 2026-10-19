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
- [backend] change the settings table to schools .. use the schools table as tenancy and hook everything up! MOST PARAMOUNT!
- [frontend] add a column showing the amount of wards a guardian has
- add a button to a custom page on the classroom resource to create multiple classrooms w the same form teacher at once

<!-- todo:unresolved --> 
- you can add clauses to the unique validation method to have the same NIN in more than one record for as long as their roles are different. this is handled programmatically. the unique method in the database schema doesn't support that, or does it? ask gpt. [update: why will i want this? ask AI]
- students should log into their portal using their admission number.
- there are many filament methods u need to apply on tables and forms and even in a service provider; check documentation.
- [backend] the student's admission should be a series of numbers w components from things that increment like the current timestamp and maybe a randomly generated number.
- [fullstack] add buttons to assign subjects to particular grades in both subjects and grades tables and their functionalities [copilot]; also add the subject counts to the particular grades.
- [frontend] add a relationship manager for the subjects in the grades edit form
- [frontend] add a back button
- [frontend] add a context menu
- [backend] binkode/laravel-paystack (i'm yet to install this crucial package in older skoolmaven versions) - rethinking using paystack. flutterwave is better.
- [backend] the tenant isn't automatically being saved into pivot tables - one being the staff_subject and classroom_subject - and forms aren't the saving relationship cuz the school_id column can't be nullable.
- [fullstack] use ai (gemini, copliot) to create the timetable resource - use reference from the previous skoolmaven app if needed.

<!-- ?resolving -->
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

12) Operations & Billing
- Add subscription/billing linking to schools table.
- Implement onboarding flow to create a School, an owner User, and default settings.

Notes/Trade-offs
- Shared DB is easiest and performant; separate DBs per tenant provides stronger isolation but more operational complexity.
- Decide tenant resolution strategy early (subdomain vs route) — it affects routing, auth cookies, and wildcard domains.