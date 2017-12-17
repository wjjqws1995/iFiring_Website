<?php
namespace App\Services;

use Carbon\Carbon;
use Dflydev\ApacheMimeTypes\PhpRepository;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Directory;

/**
 * Created by PhpStorm.
 * User: Rowan-Gao
 * Date: 2016/6/5
 * Time: 15:13
 */
class UploadsManager {
    protected $disk;
    protected $mimeDetect;

    public function __construct(PhpRepository $mimeDetect) {
        $this->disk = Storage::disk(config('blog.uploads.storage'));//设置存储地点
        $this->mimeDetect = $mimeDetect;
    }

    /**
     * 文件夹详情
     * @param $folder
     * @return array
     */
    public function folderInfo($folder) {
        $folder = $this->cleanFolder($folder);//清理文件名

        $breadcrumbs = $this->breadcrumbs($folder);
        $slice = array_slice($breadcrumbs, - 1);//去除最后一个
        $folderName = current($slice);
        $breadcrumbs = array_slice($breadcrumbs, 0, - 1);

        $subfolders = [];
        foreach (array_unique($this->disk->directories($folder)) as $subfolder) {
            $subfolders["/$subfolder"] = basename($subfolder);
        }

        $files = [];
        foreach ($this->disk->files($folder) as $path) {
            $files[] = $this->fileDetails($path);
        }

        return compact(
            'folder',//当前的目录
            'folderName',//当前目录的名字
            'breadcrumbs',//导航栏
            'subfolders',//当前目录的子目录
            'files'//当前目录的所有文件
        );

    }

    public function cleanFolder($folder) {
        return '/' . trim(str_replace('..', '', $folder) . '/');
    }

    /**
     * 返回当前目录路径
     * @param $folder
     * @return array
     */
    public function breadcrumbs($folder) {
        $folder = trim($folder, '/');
        $crumbs = ['/' => 'root'];
        if (empty($folder)) {
            return $crumbs;
        }

        $folders = explode('/', $folder);
        $build = '';
        foreach ($folders as $folder) {
            $build .= '/' . $folder;
            $crumbs[ $build ] = $folder;
        }

        return $crumbs;
    }

    /**
     * 文件详情
     * @param $path
     * @return array
     */
    public function fileDetails($path) {
        $path = '/' . ltrim($path, '/');

        return [
            'name' => basename($path),
            'fullPath' => $path,
            'webPath' => $this->fileWebpath($path),
            'size' => $this->fileSize($path),
            'mimeType' => $this->fileMimeType($path),
            'modified' => $this->fileModified($path),
        ];
    }

    /**
     * 返回文件完整的web路径
     * @param $path
     * @return string
     */
    public function fileWebpath($path) {
        $path = rtrim(config('blog.uploads.webpath'), '/') . '/' . ltrim($path, '/');

        return url($path);
    }

    /**
     * 返回文件类型
     * @param $path
     * @return mixed|null|string
     */
    public function fileMimeType($path) {
        return $this->mimeDetect->findType(
            pathinfo($path, PATHINFO_EXTENSION)
        );
    }

    /**
     * 返回文件大小
     * @param $path
     * @return mixed
     */
    public function fileSize($path) {
        return $this->disk->size($path);
    }

    /**
     * 返回最后修改时间
     * @param $path
     * @return static
     */
    public function fileModified($path) {
        return Carbon::createFromTimestamp(
            $this->disk->lastModified($path)
        );
    }

    /**
     * 创建文件夹
     * @param $folder
     * @return string
     */
    public function createDirectory($folder) {
        $folder = $this->cleanFolder($folder);

        if ($this->disk->exists($folder)) {
            return "「$folder」文件夹已经存在！";
        } else {
            return $this->disk->makeDirectory($folder);
        }

    }

    /**
     * 删除文件夹，先要判断文件夹是否为空
     * @param $folder
     * @return string
     */
    public function deleteDirectory($folder) {
        $folder = $this->cleanFolder($folder);

        $filesFolders = array_merge(
            $this->disk->directories($folder),
            $this->disk->files($folder)

        );
        if (!empty($filesFolders)) {
            return "文件夹必须为空才能删除！";
        } else {
            return $this->disk->deleteDirectory($folder);
        }


    }


    /**
     * 根据完整路径删除文件
     * @param $path
     * @return string
     */
    public function deleteFile($path) {
        $path = $this->cleanFolder($path);
        if (!$this->disk->exists($path)) {
            return '文件不存在！';
        } else {
            return $this->disk->delete($path);
        }
    }

    /**
     * 保存文件，将文件保存在指定的文件夹中，传进来的是，包含路径的完整文件名
     * @param $path
     * @param $content
     * @return string
     */
    public function saveFile($path, $content) {
        $path = $this->cleanFolder($path);
        if ($this->disk->exists($path)) {
            return '文件已经存在！';
        } else {
            return $this->disk->put($path, $content);
        }
    }


}