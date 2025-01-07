<?php

namespace App\Console\Commands;

use App\Models\Institution;
use Illuminate\Console\Command;
use League\Csv\Reader;
use App\Models\Program;

class UpdateExistingPrograms extends Command
{
    protected $signature = 'update:existing-programs {file}';
    protected $description = 'Update existing programs from CSV file';

    public function handle()
    {
        $file = $this->argument('file');
        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $record) {
            $program = Program::where('guid', $record['Progam_guid'])->first();

            if (!$program) {
                $this->error("Program not found: {$record['Progam_guid']}");
                continue;
            }

            $institution = Institution::where('guid', $record['Insitution GUID'])->first();

            if (!$institution) {
                $this->error("Institution not found: {$record['Insitution GUID']}");
                continue;
            }

            $program->update([
                'institution_guid' => $institution->guid,
                'program_name' => $record['Program_Name'],
                'cip_code' => $record['CIP_Code'],
                'program_type' => $record['Program_Type'],
                'credential' => $record['Credential'],
            ]);

            $this->info("Program updated: {$record['Program_Name']}");
        }

        $this->info('Update of existing programs completed successfully.');
    }
}
