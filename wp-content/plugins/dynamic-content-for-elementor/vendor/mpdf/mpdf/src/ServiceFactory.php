<?php

namespace DynamicOOOS\Mpdf;

use DynamicOOOS\Mpdf\Color\ColorConverter;
use DynamicOOOS\Mpdf\Color\ColorModeConverter;
use DynamicOOOS\Mpdf\Color\ColorSpaceRestrictor;
use DynamicOOOS\Mpdf\Fonts\FontCache;
use DynamicOOOS\Mpdf\Fonts\FontFileFinder;
use DynamicOOOS\Mpdf\Image\ImageProcessor;
use DynamicOOOS\Mpdf\Pdf\Protection;
use DynamicOOOS\Mpdf\Pdf\Protection\UniqidGenerator;
use DynamicOOOS\Mpdf\Writer\BaseWriter;
use DynamicOOOS\Mpdf\Writer\BackgroundWriter;
use DynamicOOOS\Mpdf\Writer\ColorWriter;
use DynamicOOOS\Mpdf\Writer\BookmarkWriter;
use DynamicOOOS\Mpdf\Writer\FontWriter;
use DynamicOOOS\Mpdf\Writer\FormWriter;
use DynamicOOOS\Mpdf\Writer\ImageWriter;
use DynamicOOOS\Mpdf\Writer\JavaScriptWriter;
use DynamicOOOS\Mpdf\Writer\MetadataWriter;
use DynamicOOOS\Mpdf\Writer\OptionalContentWriter;
use DynamicOOOS\Mpdf\Writer\PageWriter;
use DynamicOOOS\Mpdf\Writer\ResourceWriter;
use Psr\Log\LoggerInterface;
class ServiceFactory
{
    public function getServices(Mpdf $mpdf, LoggerInterface $logger, $config, $restrictColorSpace, $languageToFont, $scriptToLanguage, $fontDescriptor, $bmp, $directWrite, $wmf)
    {
        $sizeConverter = new SizeConverter($mpdf->dpi, $mpdf->default_font_size, $mpdf, $logger);
        $colorModeConverter = new ColorModeConverter();
        $colorSpaceRestrictor = new ColorSpaceRestrictor($mpdf, $colorModeConverter, $restrictColorSpace);
        $colorConverter = new ColorConverter($mpdf, $colorModeConverter, $colorSpaceRestrictor);
        $tableOfContents = new TableOfContents($mpdf, $sizeConverter);
        $cacheBasePath = $config['tempDir'] . '/mpdf';
        $cache = new Cache($cacheBasePath, $config['cacheCleanupInterval']);
        $fontCache = new FontCache(new Cache($cacheBasePath . '/ttfontdata', $config['cacheCleanupInterval']));
        $fontFileFinder = new FontFileFinder($config['fontDir']);
        $remoteContentFetcher = new RemoteContentFetcher($mpdf, $logger);
        $cssManager = new CssManager($mpdf, $cache, $sizeConverter, $colorConverter, $remoteContentFetcher);
        $otl = new Otl($mpdf, $fontCache);
        $protection = new Protection(new UniqidGenerator());
        $writer = new BaseWriter($mpdf, $protection);
        $gradient = new Gradient($mpdf, $sizeConverter, $colorConverter, $writer);
        $formWriter = new FormWriter($mpdf, $writer);
        $form = new Form($mpdf, $otl, $colorConverter, $writer, $formWriter);
        $hyphenator = new Hyphenator($mpdf);
        $imageProcessor = new ImageProcessor($mpdf, $otl, $cssManager, $sizeConverter, $colorConverter, $colorModeConverter, $cache, $languageToFont, $scriptToLanguage, $remoteContentFetcher, $logger);
        $tag = new Tag($mpdf, $cache, $cssManager, $form, $otl, $tableOfContents, $sizeConverter, $colorConverter, $imageProcessor, $languageToFont);
        $fontWriter = new FontWriter($mpdf, $writer, $fontCache, $fontDescriptor);
        $metadataWriter = new MetadataWriter($mpdf, $writer, $form, $protection, $logger);
        $imageWriter = new ImageWriter($mpdf, $writer);
        $pageWriter = new PageWriter($mpdf, $form, $writer, $metadataWriter);
        $bookmarkWriter = new BookmarkWriter($mpdf, $writer);
        $optionalContentWriter = new OptionalContentWriter($mpdf, $writer);
        $colorWriter = new ColorWriter($mpdf, $writer);
        $backgroundWriter = new BackgroundWriter($mpdf, $writer);
        $javaScriptWriter = new JavaScriptWriter($mpdf, $writer);
        $resourceWriter = new ResourceWriter($mpdf, $writer, $colorWriter, $fontWriter, $imageWriter, $formWriter, $optionalContentWriter, $backgroundWriter, $bookmarkWriter, $metadataWriter, $javaScriptWriter, $logger);
        return ['otl' => $otl, 'bmp' => $bmp, 'cache' => $cache, 'cssManager' => $cssManager, 'directWrite' => $directWrite, 'fontCache' => $fontCache, 'fontFileFinder' => $fontFileFinder, 'form' => $form, 'gradient' => $gradient, 'tableOfContents' => $tableOfContents, 'tag' => $tag, 'wmf' => $wmf, 'sizeConverter' => $sizeConverter, 'colorConverter' => $colorConverter, 'hyphenator' => $hyphenator, 'remoteContentFetcher' => $remoteContentFetcher, 'imageProcessor' => $imageProcessor, 'protection' => $protection, 'languageToFont' => $languageToFont, 'scriptToLanguage' => $scriptToLanguage, 'writer' => $writer, 'fontWriter' => $fontWriter, 'metadataWriter' => $metadataWriter, 'imageWriter' => $imageWriter, 'formWriter' => $formWriter, 'pageWriter' => $pageWriter, 'bookmarkWriter' => $bookmarkWriter, 'optionalContentWriter' => $optionalContentWriter, 'colorWriter' => $colorWriter, 'backgroundWriter' => $backgroundWriter, 'javaScriptWriter' => $javaScriptWriter, 'resourceWriter' => $resourceWriter];
    }
}
