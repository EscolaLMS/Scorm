<?php

namespace EscolaLms\Scorm\Database\Seeders;

use EscolaLms\Scorm\Services\Contracts\ScormTrackServiceContract;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

use EscolaLms\Scorm\Services\ScormService;
use Peopleaps\Scorm\Model\ScormModel;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $helper;

    public function __construct()
    {
        $scormTrackService = app(ScormTrackServiceContract::class);
        $this->helper =  new ScormService($scormTrackService);
    }

    private function fromZip($filePath)
    {
        $fullFilePath = __DIR__.'/../mocks/'.$filePath;
        $fullTmpPath = __DIR__.'/../mocks/tmp.zip';

        copy($fullFilePath, $fullTmpPath);
        $file =  new UploadedFile($fullTmpPath, basename($filePath), 'application/zip', null, true);

        try {
            $scorm = $this->helper->uploadScormArchive($file);
        } catch (\Exception $err) {
            echo $err->getMessage();
        } finally {
            if (is_file($fullTmpPath)) {
                unlink($fullTmpPath);
            }
        }

        return $scorm;
    }

    private function scormToCourse($filePath)
    {
        $scorm = $this->fromZip($filePath);
        $model = ScormModel::firstWhere('origin_file', $scorm['name']);
        $course = Course::factory()->create(['base_price'=>0, 'scorm_id'=>$model->id]);
        return $course;
    }

    public function run()
    {
        $this->fromZip('1.zip');
        $this->fromZip('2.zip');
        $this->fromZip('3.zip');
    }
}
