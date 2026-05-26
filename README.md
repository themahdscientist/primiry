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
- you can add clauses to the unique validation method to have the same NIN in more than one record for as long as their roles are different. this is handled programmatically. the unique method in the database schema doesn't support that, or does it? ask gpt. [update: why will i want this? ask AI]
- look for another icon set that greatly compliments heroicons.
- students should log into their portal using their admission number.
- add a button to a custom page on the classroom resource to create multiple classrooms w the same form teacher at once
- look for what scopedUnique actually improves in ur data retrieval.
- there are many filament methods u need to apply on tables and forms and even in a service provider; check documentation.

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
