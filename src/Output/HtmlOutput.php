<?php

namespace App\Output;

use App\Configuration\Configuration;
use App\Model\Method\Method;
use App\Model\MethodScoresMapping;
use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneMethodScoresMapping;
use App\OutputFormatter\Model\Method\MethodSignatureOutputFormatter;
use LeoVie\PhpCleanCode\Model\Score;
use LeoVie\PhpHtmlBuilder\Model\Attribute;
use LeoVie\PhpHtmlBuilder\Model\Content;
use LeoVie\PhpHtmlBuilder\Model\HtmlDOM;
use LeoVie\PhpHtmlBuilder\Model\Tag;
use Safe\Exceptions\FilesystemException;

class HtmlOutput
{
    private const NAV_TYPE_1_CLONES_TAB = 'nav-type-1-clones-tab';
    private const NAV_TYPE_2_CLONES_TAB = 'nav-type-2-clones-tab';
    private const NAV_TYPE_3_CLONES_TAB = 'nav-type-3-clones-tab';
    private const NAV_TYPE_4_CLONES_TAB = 'nav-type-4-clones-tab';
    private const NAV_TYPE_1_CLONES = 'nav-type-1-clones';
    private const NAV_TYPE_2_CLONES = 'nav-type-2-clones';
    private const NAV_TYPE_3_CLONES = 'nav-type-3-clones';
    private const NAV_TYPE_4_CLONES = 'nav-type-4-clones';
    private const BOOTSTRAP_CSS_URL = 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css';
    private const BOOTSTRAP_CSS_INTEGRITY = 'sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC';
    private const BOOTSTRAP_JS_URL = 'https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js';
    private const BOOTSTRAP_JS_INTEGRITY = 'sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM';
    private const HIGHLIGHT_CSS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/styles/default.min.css';
    private const HIGHLIGHT_JS_URL = 'https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.3.1/highlight.min.js';

    public function __construct(private MethodSignatureOutputFormatter $methodSignatureOutput)
    {
    }

    /**
     * @param SourceCloneMethodScoresMapping[] $sourceCloneMethodScoresMappings
     * @throws FilesystemException
     */
    public function createReport(array $sourceCloneMethodScoresMappings, Configuration $configuration): self
    {
        $sortedSourceCloneMethodScoreMappings = [
            SourceClone::TYPE_1 => [],
            SourceClone::TYPE_2 => [],
            SourceClone::TYPE_3 => [],
            SourceClone::TYPE_4 => [],
        ];

        foreach ($sourceCloneMethodScoresMappings as $sourceCloneMethodScoresMapping) {
            $methodScoresMappings = $sourceCloneMethodScoresMapping->getMethodScoresMappings();

            $methodScoresMappingsWithRelativePath = [];
            foreach ($methodScoresMappings as $methodScoresMapping) {
                $methodScoresMappingsWithRelativePath[] = MethodScoresMapping::create(
                    $this->convertMethodFilepathToProjectRelative($methodScoresMapping->getMethod(), $configuration->directory()),
                    $methodScoresMapping->getScores()
                );
            }

            $sourceCloneMethodScoresMappingWithRelativePath = SourceCloneMethodScoresMapping::create(
                $sourceCloneMethodScoresMapping->getSourceClone(),
                $methodScoresMappingsWithRelativePath
            );

            $cloneType = $sourceCloneMethodScoresMappingWithRelativePath->getSourceClone()->getType();
            $sortedSourceCloneMethodScoreMappings[$cloneType][] = $sourceCloneMethodScoresMappingWithRelativePath;
        }

        $htmlDom = HtmlDOM::create();
        $htmlTag = Tag::create(
            'html',
            [Attribute::create('lang', 'en')],
            [
                $this->createHead(),
                $this->createBody($sortedSourceCloneMethodScoreMappings),
            ]
        );

        $htmlDom->add($htmlTag);

        \Safe\file_put_contents($configuration->htmlReportFile(), $htmlDom->asCode());

        return $this;
    }

    private function convertMethodFilepathToProjectRelative(Method $method, string $projectRoot): Method
    {
        return Method::create(
            $method->getMethodSignature(),
            $method->getName(),
            $this->convertAbsoluteFilepathToProjectRelative($method->getFilepath(), $projectRoot),
            $method->getCodePositionRange(),
            $method->getContent(),
            $method->getParsedMethod()
        );
    }

    private function convertAbsoluteFilepathToProjectRelative(string $absolute, string $projectRoot): string
    {
        /** @var string $projectRelativePath */
        $projectRelativePath = \Safe\preg_replace("@^$projectRoot@", '', $absolute);

        return $projectRelativePath;
    }

    private function createHead(): Tag
    {
        return Tag::create(
            'head',
            [],
            [
                Tag::create(
                    'meta',
                    [Attribute::create('charset', 'utf-8')],
                    []
                ),
                Tag::create(
                    'meta',
                    [
                        Attribute::create('name', 'viewport'),
                        Attribute::create('content', 'width=device-width, initial-scale=1'),
                    ],
                    []
                ),
                Tag::create(
                    'link',
                    [
                        Attribute::create('href', self::BOOTSTRAP_CSS_URL),
                        Attribute::create('integrity', self::BOOTSTRAP_CSS_INTEGRITY),
                        Attribute::create('rel', 'stylesheet'),
                        Attribute::create('crossorigin', 'anonymous'),
                    ],
                    []
                ),
                Tag::create(
                    'link',
                    [
                        Attribute::create('href', self::HIGHLIGHT_CSS_URL),
                        Attribute::create('rel', 'stylesheet'),
                    ],
                    []
                ),
                Tag::create(
                    'title',
                    [],
                    [Content::create('php-dry: Report')]
                ),
            ]
        );
    }

    /** @param SourceCloneMethodScoresMapping[][] $sortedSourceCloneMethodScoreMappings */
    private function createBody(array $sortedSourceCloneMethodScoreMappings): Tag
    {
        return Tag::create(
            'body',
            [],
            [
                Tag::create(
                    'script',
                    [
                        Attribute::create('src', self::BOOTSTRAP_JS_URL),
                        Attribute::create('integrity', self::BOOTSTRAP_JS_INTEGRITY),
                        Attribute::create('crossorigin', 'anonymous'),
                    ],
                    []
                ),
                Tag::create(
                    'script',
                    [
                        Attribute::create('src', self::HIGHLIGHT_JS_URL),
                    ],
                    []
                ),
                Tag::create(
                    'script',
                    [],
                    [Content::create('hljs.highlightAll();')]
                ),
                Tag::create(
                    'div',
                    [Attribute::create('class', 'container')],
                    [
                        Tag::create(
                            'div',
                            [Attribute::create('class', 'row')],
                            [
                                Tag::create(
                                    'div',
                                    [Attribute::create('class', 'col-1')],
                                    [
                                        Tag::create(
                                            'svg',
                                            [
                                                Attribute::create('viewbox', '0 0 64 64'),
                                                Attribute::create('xmlns', 'http://www.w3.org/2000/svg'),
                                            ],
                                            [
                                                Tag::create(
                                                    'path',
                                                    [Attribute::create('d', 'M61,4H46V3a1,1,0,0,0-2,0V4H20V3a1,1,0,0,0-2,0V4H3A1,1,0,0,0,3,6H18V8H14a1,1,0,0,0-.633.226l-11,9a1,1,0,0,0-.211,1.311l7,11a1,1,0,0,0,.711.454,1.009,1.009,0,0,0,.806-.251L13,27.624V61a1,1,0,0,0,1,1H50a1,1,0,0,0,1-1V27.838l2.367,1.936a1,1,0,0,0,1.452-.2l7-10a1,1,0,0,0-.146-1.313l-11-10A1,1,0,0,0,50,8H46V6H61a1,1,0,0,0,0-2ZM49.613,10l10.063,9.148-5.877,8.4-3.166-2.589A1,1,0,0,0,49,25.728V60H15V25.364a1,1,0,0,0-1.673-.74l-3.121,2.837L4.32,18.212,14.357,10H18v1a1,1,0,0,0,2,0V10h5.071a7,7,0,0,0,13.858,0H44v1a1,1,0,0,0,2,0V10ZM44,8H38a1,1,0,0,0-1,1A5,5,0,0,1,27,9a1,1,0,0,0-1-1H20V6H44Z')],
                                                    [],
                                                ),
                                            ]
                                        ),
                                    ]
                                ),
                                Tag::create(
                                    'div',
                                    [Attribute::create('class', 'col-11')],
                                    [
                                        Tag::create(
                                            'h1',
                                            [],
                                            [Content::create('php-dry: Report')]
                                        ),
                                    ]
                                ),
                            ]
                        ),
                        $this->createNavigation(),
                        Tag::create(
                            'div',
                            [
                                Attribute::create('class', 'tab-content'),
                                Attribute::create('id', 'nav-tabContent'),
                            ],
                            [
                                $this->createType1ClonesTab($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_1]),
                                $this->createType2ClonesTab($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_2]),
                                $this->createType3ClonesTab($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_3]),
                                $this->createType4ClonesTab($sortedSourceCloneMethodScoreMappings[SourceClone::TYPE_4]),
                            ]
                        ),
                    ]
                ),
            ],
        );
    }

    private function createNavigation(): Tag
    {
        return Tag::create(
            'nav',
            [],
            [
                Tag::create(
                    'div',
                    [
                        Attribute::create('class', 'nav nav-tabs'),
                        Attribute::create('id', 'nav-tab'),
                        Attribute::create('role', 'tablist'),
                    ],
                    [
                        $this->createNavigationItem(
                            self::NAV_TYPE_1_CLONES_TAB,
                            '#' . self::NAV_TYPE_1_CLONES,
                            'Type 1 Clones',
                            true
                        ),
                        $this->createNavigationItem(
                            self::NAV_TYPE_2_CLONES_TAB,
                            '#' . self::NAV_TYPE_2_CLONES,
                            'Type 2 Clones',
                        ),
                        $this->createNavigationItem(
                            self::NAV_TYPE_3_CLONES_TAB,
                            '#' . self::NAV_TYPE_3_CLONES,
                            'Type 3 Clones',
                        ),
                        $this->createNavigationItem(
                            self::NAV_TYPE_4_CLONES_TAB,
                            '#' . self::NAV_TYPE_4_CLONES,
                            'Type 4 Clones',
                        ),
                    ]
                ),
            ]
        );
    }

    private function createNavigationItem(string $id, string $target, string $caption, bool $active = false): Tag
    {
        return Tag::create(
            'button',
            [
                Attribute::create('class', 'nav-link' . ($active ? ' active' : '')),
                Attribute::create('id', $id),
                Attribute::create('data-bs-toggle', 'tab'),
                Attribute::create('data-bs-target', $target),
            ],
            [Content::create($caption)]
        );
    }

    /** @param Tag[] $childs */
    private function createTab(string $id, string $labeledBy, bool $active, array $childs): Tag
    {
        return Tag::create(
            'div',
            [
                Attribute::create('class', 'tab-pane fade show' . ($active ? ' active' : '')),
                Attribute::create('id', $id),
                Attribute::create('role', 'tabpanel'),
                Attribute::create('aria-labelledby', $labeledBy),
            ],
            $childs
        );
    }

    /** @param MethodScoresMapping[] $methodScoresMappings */
    private function createCard(string $title, string $id, array $methodScoresMappings, bool $showScores): Tag
    {
        return Tag::create(
            'card',
            [Attribute::create('class', 'card')],
            [
                Tag::create(
                    'card-body',
                    [Attribute::create('class', 'card-body')],
                    [
                        Tag::create(
                            'h5',
                            [Attribute::create('class', 'card-title')],
                            [
                                Tag::create(
                                    'a',
                                    [
                                        Attribute::create('id', $id),
                                        Attribute::create('href', '#' . $id),
                                    ],
                                    [
                                        Tag::create(
                                            'span',
                                            [Attribute::create('class', 'badge bg-secondary')],
                                            [Content::create($title)]
                                        ),
                                    ]
                                ),
                            ]
                        ),
                        $this->createTable($methodScoresMappings, $id, $showScores),
                    ]
                ),
            ]
        );
    }

    /** @param MethodScoresMapping[] $methodScoresMappings */
    private function createTable(array $methodScoresMappings, string $cardId, bool $showScores): Tag
    {
        return Tag::create(
            'table',
            [Attribute::create('class', 'table')],
            [
                $this->createTableHead($showScores),
                $this->createTableBody($methodScoresMappings, $cardId, $showScores),
            ]
        );
    }

    private function createTableHead(bool $showScores): Tag
    {
        return Tag::create(
            'thead',
            [],
            [
                Tag::create(
                    'tr',
                    [],
                    [
                        $this->createColumnTh('#'),
                        $this->createColumnTh('File'),
                        $this->createColumnTh('Lines'),
                        $showScores ? $this->createColumnTh('Scores') : null,
                        $this->createColumnTh('Content'),
                    ]
                ),
            ]
        );
    }

    private function createColumnTh(string $caption): Tag
    {
        return Tag::create(
            'th',
            [Attribute::create('scope', 'col')],
            [Content::create($caption)]
        );
    }

    /** @param array<int, MethodScoresMapping> $methodScoresMappings */
    private function createTableBody(array $methodScoresMappings, string $cardId, bool $showScores): Tag
    {
        $tableRows = [];
        foreach ($methodScoresMappings as $i => $methodScoresMapping) {
            $tableRows[] = $this->createTableRow($i + 1, $methodScoresMapping, $cardId, $showScores);
        }

        return Tag::create(
            'tbody',
            [],
            $tableRows
        );
    }

    private function createTableRow(int $index, MethodScoresMapping $methodScoresMapping, string $cardId, bool $showScores): Tag
    {
        $method = $methodScoresMapping->getMethod();
        $methodStart = $method->getCodePositionRange()->getStart();
        $methodEnd = $method->getCodePositionRange()->getEnd();
        return Tag::create(
            'tr',
            [],
            [
                $this->createRowTh((string)$index),
                $this->createTd(
                    $method->getFilepath()
                    . '<br>'
                    . $methodStart->getLine() . ':' . $methodStart->getFilePos()
                    . ' - '
                    . $methodEnd->getLine() . ':' . $methodEnd->getFilePos()
                ),
                $this->createTd((string)$method->getCodePositionRange()->countOfLines()),
                $showScores ? Tag::create(
                    'td',
                    [],
                    [
                        $this->createScoresList($methodScoresMapping->getScores()),
                    ]
                ) : null,
                Tag::create(
                    'td',
                    [],
                    [
                        $this->createContentAccordion($index, $method, $cardId),
                    ]
                ),
            ]
        );
    }

    private function createRowTh(string $caption): Tag
    {
        return Tag::create(
            'th',
            [Attribute::create('scope', 'row')],
            [Content::create($caption)]
        );
    }

    private function createTd(string $caption): Tag
    {
        return Tag::create(
            'td',
            [],
            [Content::create($caption)]
        );
    }

    private function createContentAccordion(int $index, Method $method, string $cardId): Tag
    {
        $accordionHeadingId = $cardId . "_codeAccordionHeading$index";
        $accordionId = $cardId . "_codeAccordion$index";
        return Tag::create(
            'div',
            [Attribute::create('class', 'accordion')],
            [
                Tag::create(
                    'div',
                    [Attribute::create('class', 'accordion-item')],
                    [
                        Tag::create(
                            'h2',
                            [
                                Attribute::create('class', 'accordion-header'),
                                Attribute::create('id', $accordionHeadingId)
                            ],
                            [
                                Tag::create(
                                    'a',
                                    [
                                        Attribute::create('class', 'btn accordion-button collapsed'),
                                        Attribute::create('type', 'button'),
                                        Attribute::create('data-bs-toggle', 'collapse'),
                                        Attribute::create('data-bs-target', "#$accordionId"),
                                        Attribute::create('aria-expanded', "false"),
                                        Attribute::create('aria-controls', $accordionId),
                                    ],
                                    [
                                        Tag::create(
                                            'pre',
                                            [],
                                            [
                                                Tag::create(
                                                    'code',
                                                    [
                                                        Attribute::create('class', 'language-php'),
                                                    ],
                                                    [
                                                        Content::create(
                                                            'function ' . $method->getName() . $this->methodSignatureOutput->format($method->getMethodSignature())
                                                        )
                                                    ]
                                                ),
                                            ]
                                        )
                                    ]
                                )
                            ]
                        ),
                        Tag::create(
                            'div',
                            [
                                Attribute::create('id', $accordionId),
                                Attribute::create('class', 'accordion-collapse collapse'),
                                Attribute::create('aria-labelledby', $accordionHeadingId),
                            ],
                            [
                                Tag::create(
                                    'div',
                                    [Attribute::create('class', 'accordion-body')],
                                    [
                                        Tag::create(
                                            'pre',
                                            [],
                                            [
                                                Tag::create(
                                                    'code',
                                                    [
                                                        Attribute::create('class', 'language-php'),
                                                    ],
                                                    [Content::create($method->getContent())]
                                                ),
                                            ]
                                        )
                                    ]
                                )
                            ]
                        )
                    ]
                )
            ]
        );
    }

    /** @param SourceCloneMethodScoresMapping[] $sourceCloneMethodScoresMappings */
    private function createType1ClonesTab(array $sourceCloneMethodScoresMappings): Tag
    {
        $tables = [];
        foreach ($sourceCloneMethodScoresMappings as $i => $sourceCloneMethodScoresMapping) {
            $tables[] = $this->createCard(
                "Clone $i",
                "type_1_clone_$i",
                $sourceCloneMethodScoresMapping->getMethodScoresMappings(),
                false
            );
        }

        return $this->createTab(
            self::NAV_TYPE_1_CLONES,
            self::NAV_TYPE_1_CLONES_TAB,
            true,
            array_merge([$this->createTabHeadline($sourceCloneMethodScoresMappings)], $tables)
        );
    }

    /** @param SourceCloneMethodScoresMapping[] $sourceCloneMethodScoresMappings */
    private function createTabHeadline(array $sourceCloneMethodScoresMappings): Tag
    {
        return Tag::create(
            'h3',
            [],
            [Content::create(\Safe\sprintf(
                'Detected %d clone%s',
                count($sourceCloneMethodScoresMappings),
                count($sourceCloneMethodScoresMappings) > 1 ? 's' : ''
            ))]
        );
    }

    /** @param SourceCloneMethodScoresMapping[] $sourceCloneMethodScoresMappings */
    private function createType2ClonesTab(array $sourceCloneMethodScoresMappings): Tag
    {
        $tables = [];
        foreach ($sourceCloneMethodScoresMappings as $i => $sourceCloneMethodScoresMapping) {
            $tables[] = $this->createCard(
                "Clone $i",
                "type_2_clone_$i",
                $sourceCloneMethodScoresMapping->getMethodScoresMappings(),
                false
            );
        }

        return $this->createTab(
            self::NAV_TYPE_2_CLONES,
            self::NAV_TYPE_2_CLONES_TAB,
            false,
            array_merge([$this->createTabHeadline($sourceCloneMethodScoresMappings)], $tables)
        );
    }

    /** @param SourceCloneMethodScoresMapping[] $sourceCloneMethodScoresMappings */
    private function createType3ClonesTab(array $sourceCloneMethodScoresMappings): Tag
    {
        $tables = [];
        foreach ($sourceCloneMethodScoresMappings as $i => $sourceCloneMethodScoresMapping) {
            $tables[] = $this->createCard(
                "Clone $i",
                "type_3_clone_$i",
                $sourceCloneMethodScoresMapping->getMethodScoresMappings(),
                false
            );
        }

        return $this->createTab(
            self::NAV_TYPE_3_CLONES,
            self::NAV_TYPE_3_CLONES_TAB,
            false,
            array_merge([$this->createTabHeadline($sourceCloneMethodScoresMappings)], $tables)
        );
    }

    /** @param SourceCloneMethodScoresMapping[] $sourceCloneMethodScoresMappings */
    private function createType4ClonesTab(array $sourceCloneMethodScoresMappings): Tag
    {
        $tables = [];
        foreach ($sourceCloneMethodScoresMappings as $i => $sourceCloneMethodScoresMapping) {
            $tables[] = $this->createCard(
                "Clone $i",
                "type_4_clone_$i",
                $sourceCloneMethodScoresMapping->getMethodScoresMappings(),
                true
            );
        }

        return $this->createTab(
            self::NAV_TYPE_4_CLONES,
            self::NAV_TYPE_4_CLONES_TAB,
            false,
            array_merge([$this->createTabHeadline($sourceCloneMethodScoresMappings)], $tables)
        );
    }

    /** @param Score[] $scores */
    private function createScoresList(array $scores): Tag
    {
        $lis = array_map(
            fn (Score $score): Tag => $this->createScoreLi($score->getScoreType(), $score->getPoints()),
            $scores
        );

        return Tag::create(
            'ul',
            [],
            $lis,
        );
    }

    private function createScoreLi(string $scoreType, int $points): Tag
    {
        return Tag::create(
            'li',
            [],
            [Content::create("$scoreType: $points")],
        );
    }
}
