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

            $institution = Institution::where('guid', $record['Institution_guid'])->first();

            if (!$institution) {
                $this->error("Institution not found: {$record['Institution_guid']}");
                continue;
            }

            // Determine if the program is a graduate program
            $program_graduate = ($record['Program_Type'] === 'Graduate Programs') && ($record['Credential'] === 'Degree');

            $program->update([
                'institution_guid' => $institution->guid,
                'program_name' => $record['Program_Name'],
                'cip_code' => $record['CIP_Code'],
                'program_type' => $record['Program_Type'],
                'credential' => $record['Credential'],
                'program_graduate' => $program_graduate,
            ]);

            $this->info("Program updated: {$record['Program_Name']}");
        }

        $this->info('Update of existing programs completed successfully.');
    }
}
