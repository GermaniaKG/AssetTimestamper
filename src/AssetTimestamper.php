<?php
namespace Germania\AssetTimestamper;

class AssetTimestamper
{

    /**
     * @var string
     */
    public $base_path;


    /**
     * Timestamp format to use.
     *
     * @see http://php.net/manual/de/function.date.php
     * @var string
     */
    public $format = 'YmdHis';

    /**
     * Separator sign that covers the timestamp
     * @var string
     */
    public $separator = '.';


    /**
     * @param string $base_path Optional: Base path, default: Current work dir.
     * @uses   $base_path
     */
    public function __construct( $base_path = null )
    {
        $this->base_path = $base_path ?: getcwd();
    }

    /**
     * @return string
     * @uses   $base_path
     */
    public function getBasePath()
    {
        return $this->base_path;
    }

    /**
     * @param  string $asset Asset URL
     * @return string Timestamped Asset URL
     * @uses   $base_path
     */
    public function __invoke( $asset )
    {
        // Parse asset URL
        $asset_parts = parse_url( $asset );

        // Exclude if asset seems to come from different location,
        // i.e. if it has defined hostname
        if (is_array($asset_parts)
        && !empty($asset_parts['host'])) {
            return $asset;
        }

        // Prepend DIRECTORY_SEPARATOR if missing
        $work_asset = (substr($asset, 0, strlen(\DIRECTORY_SEPARATOR)) === \DIRECTORY_SEPARATOR)
        ? $asset
        : \DIRECTORY_SEPARATOR . $asset;

        // Glue base path and asset; throw if file not existant
        if (!$real_file = realpath($this->base_path . $work_asset)) {
            return $asset;
        }

        // Build result
        $timestamp = date( $this->format, filemtime( $real_file ));

        $path_info = pathinfo( $asset );

        $result = str_replace(
            $path_info['basename'],
            join( $this->separator, [
                $path_info['filename'],
                $timestamp,
                $path_info['extension']
            ]),
            $asset
        );

        return $result;
    }
}
