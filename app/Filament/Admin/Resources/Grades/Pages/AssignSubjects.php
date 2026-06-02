<?php

namespace App\Filament\Admin\Resources\Grades\Pages;

use App\Filament\Admin\Resources\Grades\GradeResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Filament\Components\Forms\Sidebar;

class AssignSubjects extends CreateRecord
{
    protected static string $resource = GradeResource::class;

    protected static ?string $title = 'Assign Subjects';

    public function getBreadcrumb(): string
    {
        return 'Subjects';
    }

    public function canCreateAnother(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Sidebar::make([

                ], [])
                    ->columnSpanFull(),
            ]);
    }

    protected function handleRecordCreation(array $data): Model
    {
        return new ($this->getModel())();
    }
}
