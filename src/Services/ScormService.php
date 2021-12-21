<?php

namespace EscolaLms\Scorm\Services;

use DOMDocument;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Peopleaps\Scorm\Entity\Scorm;
use Peopleaps\Scorm\Exception\InvalidScormArchiveException;
use Peopleaps\Scorm\Exception\StorageNotFoundException;
use Peopleaps\Scorm\Library\ScormLib;
use Peopleaps\Scorm\Model\ScormModel;
use Peopleaps\Scorm\Model\ScormScoModel;
use Ramsey\Uuid\Uuid;
use ZipArchive;
use EscolaLms\Scorm\Services\Contracts\ScormServiceContract;
use \Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ScormService implements ScormServiceContract
{
    /** @var ScormLib */
    private ScormLib $scormLib;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->scormLib = new ScormLib();
    }

    public function uploadScormArchive(UploadedFile $file): array
    {
        // Checks if it is a valid scorm archive
        $zip = new ZipArchive();
        $openValue = $zip->open($file);
        $isScormArchive = (true === $openValue) && $zip->getStream('imsmanifest.xml');

        $zip->close();

        if (!$isScormArchive) {
            throw new InvalidScormArchiveException('invalid_scorm_archive_message');
        }

        $scormData = $this->generateScorm($file);

        // save to db
        if ($scormData && is_array($scormData)) {
            $scorm = new ScormModel();
            $scorm->version = $scormData['version'];
            $scorm->hash_name = $scormData['hashName'];
            $scorm->origin_file = $scormData['name'];
            $scorm->origin_file_mime = $scormData['type'];
            $scorm->uuid = $scormData['hashName'];
            $scorm->save();

            $this->saveToDb($scormData['scos'], $scorm);
        }

        return [
            'scormData' => $scormData,
            'model' => $scorm ?? null
        ];
    }

    public function saveToDb(array $scormData, ScormModel $scormModel = null): void
    {
        foreach ($scormData as $scorm) {
            $sco = new ScormScoModel();
            $sco->scorm_id = $scormModel->id;
            $sco->uuid = $scorm->uuid;
            $sco->sco_parent_id = $scorm->scoParent ? $scorm->scoParent->id : null;
            $sco->entry_url = $scorm->entryUrl;
            $sco->identifier = $scorm->identifier;
            $sco->title = $scorm->title;
            $sco->visible = $scorm->visible;
            $sco->sco_parameters = $scorm->parameters;
            $sco->launch_data = $scorm->launchData;
            $sco->max_time_allowed = $scorm->maxTimeAllowed;
            $sco->time_limit_action = $scorm->timeLimitAction;
            $sco->block = $scorm->block;
            $sco->score_int = $scorm->scoreToPassInt;
            $sco->score_decimal = $scorm->scoreToPassDecimal;
            $sco->completion_threshold = $scorm->completionThreshold;
            $sco->prerequisites = $scorm->prerequisites;
            $sco->save();

            if (!empty($scorm->scoChildren)) {
                $this->saveToDb($scorm->scoChildren, $scormModel);
            }
        }
    }

    public function removeRecursion($data): array
    {
        $scormData = $data['scormData'];
        $scormData['scos'] = array_map(function ($row) {
            if (isset($row->scoChildren)) {
                $row->scoChildren = array_map(function ($child) {
                    if (isset($child->scoParent)) {
                        unset($child->scoParent);
                    }
                    return $child;
                }, $row->scoChildren);
            }
            return $row;
        }, $data['scormData']['scos']);

        return array_merge($data, ['scormData' => $scormData]);
    }

    public function parseScormArchive(UploadedFile $file): array
    {
        $data = [];
        $contents = '';
        $zip = new \ZipArchive();

        $zip->open($file);
        $stream = $zip->getStream('imsmanifest.xml');

        while (!feof($stream)) {
            $contents .= fread($stream, 2);
        }

        $dom = new DOMDocument();

        if (!$dom->loadXML($contents)) {
            throw new InvalidScormArchiveException('cannot_load_imsmanifest_message');
        }

        $scormVersionElements = $dom->getElementsByTagName('schemaversion');

        if ($scormVersionElements->length > 0) {
            switch ($scormVersionElements->item(0)->textContent) {
                case '1.2':
                    $data['version'] = Scorm::SCORM_12;
                    break;
                case 'CAM 1.3':
                case '2004 3rd Edition':
                case '2004 4th Edition':
                    $data['version'] = Scorm::SCORM_2004;
                    break;
                default:
                    throw new InvalidScormArchiveException('invalid_scorm_version_message');
            }
        } else {
            throw new InvalidScormArchiveException('invalid_scorm_version_message');
        }
        $scos = $this->scormLib->parseOrganizationsNode($dom);

        if (0 >= count($scos)) {
            throw new InvalidScormArchiveException('no_sco_in_scorm_archive_message');
        }
        $data['scos'] = $scos;

        return $data;
    }

    public function deleteScormData($model)
    {
        // Delete after the previous item is stored
        if ($model) {
            $oldScos = $model->scos()->get();

            // Delete all tracking associate with sco
            foreach ($oldScos as $oldSco) {
                $oldSco->scoTrackings()->delete();
            }

            $model->scos()->delete(); // delete scos
            $model->delete(); // delete scorm

            // Delete folder from server
            $this->deleteScormFolder($model->hash_name);
        }
    }

    /**
     * @param $folderHashedName
     * @return bool
     */
    protected function deleteScormFolder($folderHashedName): bool
    {
        return Storage::disk('scorm')->deleteDirectory($folderHashedName);
    }

    /**
     * Unzip a given ZIP file into the web resources directory.
     *
     * @param string $hashName name of the destination directory
     * @throws StorageNotFoundException
     */
    private function unzipScormArchive(UploadedFile $file, $hashName): void
    {
        $zip = new \ZipArchive();
        $zip->open($file);

        if (!config()->has('filesystems.disks.' . config('scorm.disk') . '.root')) {
            throw new StorageNotFoundException();
        }

        $rootFolder = config('filesystems.disks.' . config('scorm.disk') . '.root');

        if (substr($rootFolder, -1) != '/') {
            // If end with xxx/
            $rootFolder = config('filesystems.disks.' . config('scorm.disk') . '.root') . '/';
        }

        $destinationDir = $rootFolder . $hashName; // file path

        if (!File::isDirectory($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true, true);
        }

        $zip->extractTo($destinationDir);
        $zip->close();
    }

    /**
     * @param UploadedFile $file
     * @return array
     * @throws InvalidScormArchiveException|StorageNotFoundException
     */
    private function generateScorm(UploadedFile $file): array
    {
        $hashName = Uuid::uuid4();
        $hashFileName = $hashName . '.zip';
        $scormData = $this->parseScormArchive($file);
        $this->unzipScormArchive($file, 'scorm/' . $scormData['version'] . '/' . $hashName);

        if (!config()->has('filesystems.disks.' . config('scorm.disk') . '.root')) {
            throw new StorageNotFoundException();
        }

        $rootFolder = config('filesystems.disks.' . config('scorm.disk') . '.root');

        if (substr($rootFolder, -1) != '/') {
            // If end with xxx/
            $rootFolder = config('filesystems.disks.' . config('scorm.disk') . '.root') . '/';
        }

        $destinationDir = 'scorm/' . $scormData['version'] . '/' . $rootFolder . $hashName; // file path

        // Move Scorm archive in the files directory
        $finalFile = $file->move($destinationDir, $hashName . '.zip');

        return [
            'name' => $hashFileName, // to follow standard file data format
            'hashName' => $hashName,
            'type' => $finalFile->getMimeType(),
            'version' => $scormData['version'],
            'scos' => $scormData['scos'],
        ];
    }

    /**
     * Get SCO list
     * @param $scormId
     * @return Builder[]|Collection
     */
    public function getScos($scormId): ScormScoModel
    {
        return ScormScoModel::with(['scorm'])
            ->where('scorm_id', $scormId)
            ->get();
    }

    /**
     * Get sco by uuid
     * @param $scoUuid
     * @return null|Builder|Model
     */
    public function getScoByUuid($scoUuid): ScormScoModel
    {
        return ScormScoModel::with(['scorm'])
            ->where('uuid', $scoUuid)
            ->firstOrFail();
    }

    /**
     * Get sco data to view by uuid
     * @param $scoUuid
     * @return array
     */
    public function getScoViewDataByUuid($scoUuid): array
    {
        $data = $this->getScoByUuid($scoUuid);

        $data['entry_url_absolute'] = Storage::url('scorm/' . $data->scorm->version . '/' . $data->scorm->uuid . '/' . $data->entry_url . $data->sco_parameters);
        $data['version'] = $data->scorm->version;
        $data['player'] = (object)[
            'lmsCommitUrl' => '/api/lms',
            'logLevel' => 1,
            'autoProgress' => true,
            'cmi' => [] // cmi is user progress
        ];

        return $data;
    }

    public function listModels($per_page = 15, array $columns = ['*']): LengthAwarePaginator
    {
        return ScormModel::with(['scos' => fn($query) => $query->select(['*'])->where('block', '=', 0)])
            ->select($columns)
            ->paginate(intval($per_page));
    }
}
