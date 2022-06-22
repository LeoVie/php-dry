<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite17cba945be499726e4323268288eb20
{
    public static $prefixLengthsPsr4 = array (
        'L' => 
        array (
            'LeoVie\\DTORepository\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'LeoVie\\DTORepository\\' => 
        array (
            0 => __DIR__ . '/..' . '/leovie/dto-repository/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'LeoVie\\CloneDetectionTestdataWithDTOs\\DTO' => __DIR__ . '/../..' . '/src/DTO.php',
        'LeoVie\\CloneDetectionTestdataWithDTOs\\ToAnalyze\\A' => __DIR__ . '/../..' . '/src/ToAnalyze/01_A.php',
        'LeoVie\\CloneDetectionTestdataWithDTOs\\ToAnalyze\\A_Changed_Syntax' => __DIR__ . '/../..' . '/src/ToAnalyze/04_A_Changed_Syntax.php',
        'LeoVie\\CloneDetectionTestdataWithDTOs\\ToAnalyze\\A_Exact_Copy' => __DIR__ . '/../..' . '/src/ToAnalyze/03_A_Exact_Copy.php',
        'LeoVie\\CloneDetectionTestdataWithDTOs\\ToAnalyze\\B' => __DIR__ . '/../..' . '/src/ToAnalyze/02_B.php',
        'LeoVie\\DTORepository\\VendorDTO' => __DIR__ . '/..' . '/leovie/dto-repository/src/VendorDTO.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite17cba945be499726e4323268288eb20::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite17cba945be499726e4323268288eb20::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite17cba945be499726e4323268288eb20::$classMap;

        }, null, ClassLoader::class);
    }
}
