<?php


namespace dyutin\FileManager;

class Translation
{
    private static $key;

    private static $data = [];


    public function __construct(string $key = null, array $data = [])
    {
        static::$key = $key;
        static::$data = $data;
    }

    /**
     * @codeCoverageIgnore
     * @param $key
     * @param array $data
     * @return array|string|null
     */
    public static function get($key, array $data = [])
    {
        return __('fileManager::messages.' . $key, $data);
    }


    /**
     * @codeCoverageIgnore
     * @param bool $when
     * @param string $key
     * @param array $data
     * @return self
     */
    public static function when($when, string $key, array $data = [])
    {
        if ($when) {
            return new static($key, $data);
        }

        return new static;
    }


    /**
     * @codeCoverageIgnore
     * @param string $key
     * @param array $data
     * @return string
     */
    public function unless(string $key, array $data = [])
    {
        if (!static::$key) {
            static::$key  = $key;
            static::$data = $data;
        }

        return static::getResponseAndResetData();
    }


    /**
     * @codeCoverageIgnore
     * @return string
     */
    private static function getResponseAndResetData()
    {
        $response = static::get(static::$key, static::$data);

        static::$key  = null;

        static::$data = [];

        return $response;
    }


    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function __toString()
    {
        return static::get(static::$key, static::$data);
    }
}
