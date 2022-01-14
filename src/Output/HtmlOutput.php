<?php

namespace App\Output;

use App\Model\MethodScoresMapping;
use App\Model\SourceClone\SourceClone;
use App\Model\SourceCloneMethodScoresMapping;
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

    /**
     * @param SourceCloneMethodScoresMapping[] $sourceCloneMethodScoresMappings
     * @throws FilesystemException
     */
    public function createReport(array $sourceCloneMethodScoresMappings, string $reportPath): self
    {
        $sortedSourceCloneMethodScoreMappings = [
            SourceClone::TYPE_1 => [],
            SourceClone::TYPE_2 => [],
            SourceClone::TYPE_3 => [],
            SourceClone::TYPE_4 => [],
        ];

        foreach ($sourceCloneMethodScoresMappings as $sourceCloneMethodScoresMapping) {
            $cloneType = $sourceCloneMethodScoresMapping->getSourceClone()->getType();
            $sortedSourceCloneMethodScoreMappings[$cloneType][] = $sourceCloneMethodScoresMapping;
        }

        $htmlDom = HtmlDOM::create();
        $htmlTag = Tag::create('html',
            [Attribute::create('lang', 'en')],
            [
                $this->createHead(),
                $this->createBody($sortedSourceCloneMethodScoreMappings),
            ]
        );

        $htmlDom->add($htmlTag);

        \Safe\file_put_contents($reportPath, $htmlDom->asCode());

        return $this;
    }

    private function createHead(): Tag
    {
        return Tag::create('head',
            [],
            [
                Tag::create('meta',
                    [Attribute::create('charset', 'utf-8')],
                    []
                ),
                Tag::create('meta',
                    [
                        Attribute::create('name', 'viewport'),
                        Attribute::create('content', 'width=device-width, initial-scale=1'),
                    ],
                    []
                ),
                Tag::create('link',
                    [
                        Attribute::create('href', self::BOOTSTRAP_CSS_URL),
                        Attribute::create('integrity', self::BOOTSTRAP_CSS_INTEGRITY),
                        Attribute::create('rel', 'stylesheet'),
                        Attribute::create('crossorigin', 'anonymous'),
                    ],
                    []
                ),
                Tag::create('link',
                    [
                        Attribute::create('href', self::HIGHLIGHT_CSS_URL),
                        Attribute::create('rel', 'stylesheet'),
                    ],
                    []
                ),
                Tag::create('title',
                    [],
                    [Content::create('Source Code Clones')]
                ),
            ]
        );
    }

    /** @param SourceCloneMethodScoresMapping[][] $sortedSourceCloneMethodScoreMappings */
    private function createBody(array $sortedSourceCloneMethodScoreMappings): Tag
    {
        return Tag::create('body',
            [],
            [
                Tag::create('script',
                    [
                        Attribute::create('src', self::BOOTSTRAP_JS_URL),
                        Attribute::create('integrity', self::BOOTSTRAP_JS_INTEGRITY),
                        Attribute::create('crossorigin', 'anonymous'),
                    ],
                    []
                ),
                Tag::create('script',
                    [
                        Attribute::create('src', self::HIGHLIGHT_JS_URL),
                    ],
                    []
                ),
                Tag::create('script',
                    [],
                    [Content::create('hljs.highlightAll();')]
                ),
                Tag::create('div',
                    [Attribute::create('class', 'container')],
                    [
                        Tag::create('h1',
                            [],
                            [Content::create('Source Code Clones')]
                        ),
                        $this->createNavigation(),
                        Tag::create('div',
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
        return Tag::create('nav',
            [],
            [
                Tag::create('div',
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
        return Tag::create('button',
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
        return Tag::create('div',
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
        return Tag::create('card',
            [Attribute::create('class', 'card')],
            [
                Tag::create('card-body',
                    [Attribute::create('class', 'card-body')],
                    [
                        Tag::create('h5',
                            [Attribute::create('class', 'card-title')],
                            [
                                Tag::create('a',
                                    [
                                        Attribute::create('id', $id),
                                        Attribute::create('href', '#' . $id),
                                    ],
                                    [
                                        Tag::create('span',
                                            [Attribute::create('class', 'badge bg-secondary')],
                                            [Content::create($title)]
                                        ),
                                    ]
                                ),
                            ]
                        ),
                        $this->createTable($methodScoresMappings, $showScores),
                    ]
                ),
            ]
        );
    }

    /** @param MethodScoresMapping[] $methodScoresMappings */
    private function createTable(array $methodScoresMappings, bool $showScores): Tag
    {
        return Tag::create('table',
            [Attribute::create('class', 'table')],
            [
                $this->createTableHead($showScores),
                $this->createTableBody($methodScoresMappings, $showScores),
            ]
        );
    }

    private function createTableHead(bool $showScores): Tag
    {
        return Tag::create('thead',
            [],
            [
                Tag::create('tr',
                    [],
                    [
                        $this->createColumnTh('#'),
                        $this->createColumnTh('File'),
                        $this->createColumnTh('Start'),
                        $this->createColumnTh('End'),
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
        return Tag::create('th',
            [Attribute::create('scope', 'col')],
            [Content::create($caption)]
        );
    }

    /** @param MethodScoresMapping[] $methodScoresMappings */
    private function createTableBody(array $methodScoresMappings, bool $showScores): Tag
    {
        $tableRows = [];
        foreach ($methodScoresMappings as $i => $methodScoresMapping) {
            $tableRows[] = $this->createTableRow($i + 1, $methodScoresMapping, $showScores);
        }

        return Tag::create('tbody',
            [],
            $tableRows
        );
    }

    private function createTableRow(int $index, MethodScoresMapping $methodScoresMapping, bool $showScores): Tag
    {
        $method = $methodScoresMapping->getMethod();
        $methodStart = $method->getCodePositionRange()->getStart();
        $methodEnd = $method->getCodePositionRange()->getEnd();
        return Tag::create('tr',
            [],
            [
                $this->createRowTh((string)$index),
                $this->createTd($method->getFilepath()),
                $this->createTd($methodStart->getLine() . ':' . $methodStart->getFilePos()),
                $this->createTd($methodEnd->getLine() . ':' . $methodEnd->getFilePos()),
                $this->createTd((string)$method->getCodePositionRange()->countOfLines()),
                $showScores ? Tag::create('td',
                    [],
                    [
                        $this->createScoresList($methodScoresMapping->getScores()),
                    ]
                ) : null,
                Tag::create('td',
                    [],
                    [
                        Tag::create('pre',
                            [],
                            [
                                Tag::create('code',
                                    [
                                        Attribute::create('class', 'language-php'),
                                    ],
                                    [Content::create($method->getContent())]
                                ),
                            ]
                        ),
                    ]
                ),
            ]
        );
    }

    private function createRowTh(string $caption): Tag
    {
        return Tag::create('th',
            [Attribute::create('scope', 'row')],
            [Content::create($caption)]
        );
    }

    private function createTd(string $caption): Tag
    {
        return Tag::create('td',
            [],
            [Content::create($caption)]
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
        return Tag::create('h3',
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
            fn(Score $score): Tag => $this->createScoreLi($score->getScoreType(), $score->getPoints()),
            $scores
        );

        return Tag::create('ul',
            [],
            $lis,
        );
    }

    private function createScoreLi(string $scoreType, int $points): Tag
    {
        return Tag::create('li',
            [],
            [Content::create("$scoreType: $points")],
        );
    }
}