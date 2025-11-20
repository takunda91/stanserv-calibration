<?php

namespace App\Filament\Resources\Audits\Tables;

use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class AuditsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('event')->sortable(),
                TextColumn::make('auditable_type')->label('Model'),
                TextColumn::make('changes')
                    ->label('Changes')
                    ->state(function ($record) {
                        $old = $record->old_values ?? [];
                        $new = $record->new_values ?? [];


                        $diffLines = [];

                        foreach ($new as $field => $newValue) {
                            $oldValue = $old[$field] ?? null;

                            // Only show fields that actually changed
                            if ($oldValue !== $newValue) {
                                $diffLines[] = sprintf(
                                    "%s: %s → %s",
                                    ucfirst(str_replace('_', ' ', $field)),
                                    $oldValue ?? '(null)',
                                    $newValue ?? '(null)'
                                );
                            }
                        }

                        return implode("\n", $diffLines) ?: '—';
                    })
                    ->wrap()
                    ->limit()
                    ->tooltip(fn($state) => $state) // full text on hover
                    ->toggleable(),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                self::viewChangeList()

            ]);
    }

    public static function viewChangeList(): Action
    {
        return Action::make('viewChanges')
            ->label('Changes')
            ->icon(Heroicon::OutlinedBookOpen)
            ->color('info')
            ->modalHeading('Field Changes')
            ->modalSubmitAction(false)
            ->modalContent(function ($record) {

                $old = $record->old_values ?? [];
                $new = $record->new_values ?? [];

                $rows = [];

                foreach ($new as $field => $newValue) {
                    $oldValue = $old[$field] ?? null;

                    // CASE 1: Field is meta JSON → Expand it properly
                    if (in_array($field, ['permit_to_work', 'precheck', 'risk_assessment']) && is_string($newValue)) {
                        $oldMeta = json_decode($oldValue ?? '{}', true, 512, JSON_THROW_ON_ERROR) ?: [];
                        $newMeta = json_decode($newValue ?? '{}', true, 512, JSON_THROW_ON_ERROR) ?: [];

                        foreach ($newMeta as $key => $nv) {
                            $ov = $oldMeta[$key] ?? null;

                            if ($ov !== $nv) {
                                $rows[] = [
                                    'field' => ucfirst(str_replace('_', ' ', $key)),
                                    'old' => is_bool($ov) ? ($ov ? 'Yes' : 'No') : ($ov ?? ' null '),
                                    'new' => is_bool($nv) ? ($nv ? 'Yes' : 'No') : ($nv ?? ' null '),
                                ];
                            }
                        }

                        continue;
                    }

                    // CASE 2: Normal scalar field change
                    if ($oldValue !== $newValue) {
                        $rows[] = [
                            'field' => ucfirst(str_replace('_', ' ', $field)),
                            'old' => json_encode($oldValue),
                            'new' => json_encode($newValue),
                        ];
                    }
                }

                // Build clean UI
                $html = '<div class="space-y-3">';

                foreach ($rows as $row) {
                    $html .= '
            <div class="border rounded-lg p-3 bg-gray-50">
                <div class="font-semibold mb-1 text-gray-800">' . $row['field'] . '</div>
                <div class="flex items-center space-x-2">
                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded">' . $row['old'] . '</span>
                    <span class="text-gray-500"> → </span>
                    <span class="px-2 py-1 bg-green-100 text-green-700 rounded">' . $row['new'] . '</span>
                </div>
            </div>';
                }

                $html .= '</div>';

                return new HtmlString($html);
            });
    }
}
