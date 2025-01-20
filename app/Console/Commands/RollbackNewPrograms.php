<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Program;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;

class RollbackNewPrograms extends Command
{
    protected $signature = 'rollback:new-programs {file}';
    protected $description = 'Rollback new programs imported from CSV file';

    public function handle()
    {
        $file = $this->argument('file');
        $csv = Reader::createFromPath($file, 'r');
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        DB::beginTransaction();

        try {
            foreach ($records as $record) {
                $guid = str_replace('-', '', $record['JM_Assigned_GUID']);
                $this->info("Searching for program with GUID: $guid");
                $program = Program::where('guid', $guid)->first();

                if ($program) {
                    $program->forceDelete();
                    $this->info("Program deleted: {$record['Program_Name']}");
                } else {
                    $this->warn("Program not found: {$record['Program_Name']}");
                }
            }

            DB::commit();
            $this->info('Rollback of new programs completed successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('An error occurred during rollback: ' . $e->getMessage());
        }
    }
}
