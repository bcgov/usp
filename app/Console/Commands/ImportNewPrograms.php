<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use League\Csv\Reader;
use App\Models\Institution;
use App\Models\Program;

class ImportNewPrograms extends Command
{
    protected $signature = 'import:new-programs {file}';
    protected $description = 'Import new programs from CSV file';

    public function handle()
    {
        $file = $this->argument('file');
        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $record) {
            $institution = Institution::where('guid', $record['Insitution GUID'])->first();

            if (!$institution) {
                $this->error("Institution not found: {$record['Insitution GUID']}");
                continue;
            }

            // Determine if the program is a graduate program
            $program_graduate = ($record['Program_Type'] === 'Graduate Programs') && ($record['Credential'] === 'Degree');

            // Remove any hyphens that could be exist in the csv file provided
            $guid = str_replace('-', '', $record['JM_Assigned_GUID']);

            Program::create([
                'institution_guid' => $institution->guid,
                'guid' => $guid,
                'active_status' => true,
                'program_name' => $record['Program_Name'],
                'cip_code' => $record['CIP_Code'],
                'program_type' => $record['Program_Type'],
                'credential' => $record['Credential'],
                'program_graduate' => $program_graduate,
            ]);

            $this->info("Program added: {$record['Program_Name']}");
        }

        $this->info('Import of new programs completed successfully.');
    }
}
