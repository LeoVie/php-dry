<?php

declare(strict_types=1);

namespace App\Factory;

use App\Model\FilepathMethods\FilepathMethods;
use LeoVie\PhpMethodsParser\Service\MethodsParser;
use Safe\Exceptions\FilesystemException;

class FilepathMethodsFactory
{
    public function __construct(private MethodsParser $methodsParser)
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
        return FilepathMethods::create($filepath, $this->methodsParser->extractMethods($filepath));
    }
}