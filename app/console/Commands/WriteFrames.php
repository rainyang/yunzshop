<?php

namespace app\console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class WriteFrames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'write:frame {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer the specified file to word';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $finder = Finder::create()->in($this->destPath());

        $fileCount = $finder->count();

        $bar = $this->output->createProgressBar($fileCount);
        $value = (string)$this->argument('file');

        if (strpos($value, '/') !== false) {

            $all = explode('/', $value);
            $name = $all[count($all) -1];

        } else {
            $name = $value;
        }
        file_put_contents(storage_path('logs/mytest.log'), $value, FILE_APPEND);

        foreach ($finder as $file) {
            /**
             * @var SplFileInfo $file
             */
            if ($file->isFile()) {
                $fileContent = $file->getContents();
                $this->writeWord($fileContent, $name);
            }
            $bar->advance();
        }
        $bar->finish();
        $this->comment('Importing Word Success');
    }

    private function writeWord($content, $name)
    {
        $fileName = base_path($name) . '.wps';

        file_put_contents($fileName, $content, FILE_APPEND | LOCK_EX);
    }
    /**
     * @return string
     */
    private function destPath($name)
    {
        $path = base_path($name. DIRECTORY_SEPARATOR );
        if (is_dir($path)) {
            return $path;
        }
        $this->error('Folder does not exist!');
        exit();
    }
}
