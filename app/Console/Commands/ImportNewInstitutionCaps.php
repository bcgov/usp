<?php

namespace App\Console\Commands;

use App\Models\Cap;
use Illuminate\Console\Command;
use League\Csv\Reader;

class ImportNewInstitutionCaps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:new-institutions-caps {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import caps data from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File does not exist: $file");
            return 1;
        }

        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $record) {
            try {
                // Remove any hyphens that could be exist in the csv file provided
                $guid = str_replace('-', '', $record['guid']);

                Cap::create([
                    'guid' => $guid,
                    'fed_cap_guid' => $record['fed_cap_guid'],
                    'institution_guid' => $record['institution_guid'],
                    'start_date' => $record['start_date'],
                    'end_date' => $record['end_date'],
                    'total_attestations' => $record['total_attestations'],
                    'active_status' => TRUE,
                    'confirmed' => TRUE,
                    'total_reserved_graduate_attestations' => $record['total_reserved_graduate_attestations'],
                ]);

                $this->info("Created cap for: {$record['guid']}");
            } catch (\Exception $e) {
                $this->error("Error processing record for {$record['guid']}: " . $e->getMessage());
            }
        }

        $this->info('Data import completed.');
        return 0;
    }
}
