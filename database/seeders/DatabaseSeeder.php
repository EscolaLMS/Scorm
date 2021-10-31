<?php

namespace EscolaLms\Scorm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use App\Library\ScormHelper;
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
        $this->helper =  new ScormHelper();
    }

    private function fromZip($filePath)
    {
        $fullFilePath = __DIR__.'/scorm/'.$filePath;
        $fullTmpPath = __DIR__.'/scorm/tmp.zip';

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
        $this->fromZip('employee-health-and-wellness-sample-course-scorm12-z_legm6c.zip');
        $this->fromZip('runtimebasiccalls_scorm12.zip');
        $this->fromZip('sl360_lms_scorm_1_2.zip');
    }
}
