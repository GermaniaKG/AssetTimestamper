<?php
namespace tests;

use Germania\AssetTimestamper\AssetTimestamper;


class AssetTimestamperTest extends \PHPUnit\Framework\TestCase
{
    public $mock_dir;

    public function setUp()
    {
        $this->mock_dir = realpath( __DIR__ . '/../mocks');
    }

    public function testSimpleInstantiation() {
        $sut = new AssetTimestamper;
        $this->assertEquals( getcwd(), $sut->getBasePath() );
    }

    /**
     * @dataProvider provideDirectories
     */
    public function testInstantiationWithPath( $path ) {
        $sut = new AssetTimestamper( $path );
        $this->assertEquals( $path, $sut->getBasePath() );
    }


    /**
     * @dataProvider provideValidFilenames
     */
    public function testTimestamping( $asset ) {
        $sut = new AssetTimestamper( $this->mock_dir );

        $path_info = pathinfo( $asset );

        $result = $sut( $asset );
        $this->assertRegExp( "!"
            . $path_info['filename']

            // This hopefully preg_matches timestamp:
            . "\.[\d]+\."

            . $path_info['extension'] . "$!", $result);

        // Make sure that DIRECTORY_SEPARATOR has not been wrangled into the result.
        // First characters should be equal anyway.
        $asset_first_char  = substr($asset, 0, 1);
        $result_first_char = substr($result, 0, 1);

        $this->assertEquals( $asset_first_char,  $result_first_char );
    }


    /**
     * @dataProvider provideInvalidFilenames
     */
    public function testTimestampingOnNotExistingFiles( $asset ) {
        $sut = new AssetTimestamper( $this->mock_dir );

        $path_info = pathinfo( $asset );

        // As of v2, no Exception here
        $result = $sut( $asset );
        $this->assertEquals($result, $asset);
    }


    /**
     * @dataProvider provideUrls
     */
    public function testTimestampingOnUrls( $asset ) {
        $sut = new AssetTimestamper( $this->mock_dir );

        $path_info = pathinfo( $asset );

        $result = $sut( $asset );
        $this->assertEquals($result, $asset);
    }


    public function provideUrls()
    {
        return array(
            [ "//localhost/path/to/foo.txt" ],
            [ "http://www.test.com/bar.html" ]
        );
    }

    public function provideInvalidFilenames()
    {
        return array(
            [ "notexisting.txt" ],
            [ "/subdir/notexisting.txt" ]
        );
    }

    public function provideValidFilenames()
    {
        return array(
            [ "dummy.txt" ],
            [ "/dummy.txt" ],
            [ "subdir/dummy2.txt" ],
            [ "/subdir/dummy2.txt" ]
        );
    }

    public function provideDirectories()
    {
        return array(
            [ __DIR__ ],
            [ realpath( __DIR__ . '/../mocks') ]
        );
    }


}
