<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\FilepathMethods\FilepathMethods;
use App\Parse\Parser\ClassMethodsParser;
use App\Parse\Parser\ClassnameParser;
use App\Parse\Parser\FunctionsParser;
use Safe\Exceptions\FilesystemException;

class FilepathMethodsFactory
{
    public function __construct(
        private ClassMethodsParser $classMethodsParser,
        private FunctionsParser    $functionsParser,
        private ClassnameParser    $classnameParser
    )
    {
    }

    /**
     * @param string[] $filepaths
     * @return FilepathMethods[]
     *
     * @throws FilesystemException
     */
    public function create(array $filepaths): array
    {
        return array_map(fn(string $f) => $this->createOne($f), $filepaths);
    }

    /** @throws FilesystemException */
    private function createOne(string $filepath): FilepathMethods
    {
        // TODO: Dont use parser for checking if class method or not
        $classname = $this->classnameParser->extractClassname($filepath);

        // TODO: Remove if-else
        if ($classname === null) {
            $methods = $this->functionsParser->extractFunctions($filepath);
        } else {
            $methods = $this->classMethodsParser->extractClassMethods($filepath);
        }

        return FilepathMethods::create($filepath, $methods);
    }
}