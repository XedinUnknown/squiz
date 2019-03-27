<?php

namespace XedinUnknown\SQuiz;

use Throwable;

/**
 * Resolves a relative path to an absolute one.
 *
 * This implementation will check a number of base paths for the relative path,
 * and return the absolute path of the first match.
 *
 * @package SQuiz
 */
class File_Path_Resolver {

    protected $base_paths;

    public function __construct(array $base_paths)
    {
        $this->base_paths = $base_paths;
    }

    /**
     * @param string $path
     * @return string|null The absolute path if resolved; null otherwise;
     *
     * @throws 
     */
    public function resolve(string $path)
    {
        foreach ($this->base_paths as $base_path) {
            $base_path = rtrim($base_path, '/');
            $absolute_path = "{$base_path}/{$path}";
            if ($this->_path_exists($absolute_path)) {
                return $absolute_path;
            }
        }


        return null;
    }

    /**
     * Determines whether a file or folder exists at the specified path.
     *
     * @param string $path The path to the file or folder.
     * @return bool True if the path exists; false otherwise.
     *
     * @throws Throwable If problem determining whether path exists.
     */
    protected function _path_exists(string $path)
    {
        return file_exists($path);
    }
}