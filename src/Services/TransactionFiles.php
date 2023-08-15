<?php

namespace TromsFylkestrafikk\RagnarokRuter\Services;

use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use TromsFylkestrafikk\RagnarokSink\Models\RawFile;

/**
 * Helper class to manage data in local files.
 */
class TransactionFiles
{
    /**
     * @var Filesystem
     */
    protected $disk;

    /**
     * Local path where files are to be found.
     *
     * @var string
     */
    protected $path = '';

    public function __construct(protected string $sinkId)
    {
        $this->disk = app('filesystem')->build(config('ragnarok_sink.local_disk'));
    }

    public function toFile($filename, $content)
    {
        $checksum = md5($content);
        $existing = $this->getFile($filename);
        if ($existing) {
            if ($existing->checksum !== $checksum) {
                $existing->fill([
                    'checksum' => $checksum,
                    'import_status' => 'updated',
                    'import_msg' => null,
                ]);
                $existing->save();
            }
            return $existing;
        }
        $filePath = $this->getFilePath($filename);
        $this->disk->put($filePath, $content);
        return RawFile::create([
            'sink_id' => $this->sinkId,
            'name' => $filePath,
            'checksum' => $checksum,
        ]);
    }

    /**
     * @param $filename
     * @return RawFile|null
     */
    public function getFile($filename)
    {
        return RawFile::firstWhere(['sink_id' => $this->sinkId, 'name' => $this->getFilePath($filename)]);
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setPath($path = '')
    {
        $this->path = rtrim(ltrim($path, '/'), '/');
        return $this;
    }

    /**
     * Get local file path of given file.
     *
     * @param string $filename
     * @return string
     */
    public function getFilePath($filename)
    {
        $lDir = $this->getLocalDir();
        // Make sure local directory exists
        if (!$this->disk->exists($lDir)) {
            $this->disk->makeDirectory($lDir);
        }
        return implode('/', [$lDir, ltrim($filename)]);
    }

    /**
     * Get local directory path
     *
     * @return string
     */
    public function getLocalDir()
    {
        $walk = ['/' . $this->sinkId];
        if (strlen($this->path)) {
            $walk[] = $this->path;
        }
        return implode('/', $walk);
    }

}
